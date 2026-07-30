<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Enums\TransactionType;
use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Receives finished spreadsheets and moves them through review.
 */
class SubmissionService
{
    public function __construct(
        private readonly SpreadsheetParser $parser,
        private readonly WalletService $wallet,
        private readonly TaskService $tasks,
    ) {}

    /**
     * Stores an upload privately, extracts its preview, and puts it in the
     * review queue. The task stays assigned until a reviewer decides.
     */
    public function store(User $user, Task $task, UploadedFile $file): Submission
    {
        $disk = config('sortifya.uploads.submissions_disk');
        $directory = config('sortifya.uploads.submissions_path');

        // Predictable, collision-proof, and it tells you what it is at a glance.
        $filename = sprintf(
            'task-%d-user-%d-%s.%s',
            $task->id,
            $user->id,
            Str::lower(Str::random(10)),
            $file->getClientOriginalExtension(),
        );

        $path = $file->storeAs($directory, $filename, $disk);

        $preview = $this->parser->previewSafely(Storage::disk($disk)->path($path));

        return Submission::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'uploaded_excel_path' => $path,
            'parsed_preview_data' => $preview,
            'status' => SubmissionStatus::Pending,
        ]);
    }

    /**
     * Accepts the work: credits the reward and retires the task.
     *
     * Guarded against a double-click in the admin panel — a submission that
     * is already approved returns untouched rather than paying twice.
     */
    public function approve(Submission $submission, User $reviewer): Submission
    {
        return DB::transaction(function () use ($submission, $reviewer) {
            $fresh = Submission::whereKey($submission->getKey())->lockForUpdate()->firstOrFail();

            if ($fresh->status === SubmissionStatus::Approved) {
                return $fresh;
            }

            $fresh->update([
                'status' => SubmissionStatus::Approved,
                'rejection_reason' => null,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $task = $fresh->task;

            $this->wallet->credit(
                $fresh->user,
                (float) $task->reward_usd,
                TransactionType::TaskReward,
                $task->title('en'),
                $fresh,
            );

            $this->tasks->markCompleted($task);

            return $fresh;
        });
    }

    /**
     * Sends the work back with a reason and returns the task to the worker so
     * they can fix it. The reward is never credited, so nothing is clawed back.
     */
    public function reject(Submission $submission, User $reviewer, string $reason, bool $keepWithWorker = true): Submission
    {
        return DB::transaction(function () use ($submission, $reviewer, $reason, $keepWithWorker) {
            $fresh = Submission::whereKey($submission->getKey())->lockForUpdate()->firstOrFail();

            if ($fresh->status === SubmissionStatus::Approved) {
                // Reversing a paid approval is a ledger operation, not a status flip.
                return $fresh;
            }

            $fresh->update([
                'status' => SubmissionStatus::Rejected,
                'rejection_reason' => $reason,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $this->tasks->reopenForRework(
                $fresh->task,
                $keepWithWorker ? $fresh->user : null,
            );

            return $fresh;
        });
    }

    /** Removes the stored spreadsheet. Used when a submission is deleted. */
    public function deleteFile(Submission $submission): void
    {
        Storage::disk(config('sortifya.uploads.submissions_disk'))
            ->delete($submission->uploaded_excel_path);
    }
}
