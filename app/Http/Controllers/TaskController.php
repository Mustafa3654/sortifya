<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Task;
use App\Services\SubmissionService;
use App\Services\TaskService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $tasks,
        private readonly SubmissionService $submissions,
    ) {}

    /** The workbench: source file, template, and the upload box. */
    public function show(Request $request, Task $task): View
    {
        $user = $request->user();

        abort_unless($task->isHeldBy($user) || $task->submissions()->where('user_id', $user->id)->exists(), 403);

        return view('tasks.show', [
            'task' => $task,
            'submission' => $task->submissions()
                ->where('user_id', $user->id)
                ->latest()
                ->first(),
        ]);
    }

    /** Locks an open task to the current worker. */
    public function claim(Request $request, Task $task): RedirectResponse
    {
        $outcome = $this->tasks->claim($request->user(), $task);

        return match ($outcome) {
            TaskService::OUTCOME_CLAIMED => redirect()
                ->route('tasks.show', $task)
                ->with('success', __('sortifya.task.claim_success', [
                    'minutes' => $this->tasks->holdMinutes(),
                ])),

            TaskService::OUTCOME_AT_LIMIT => back()
                ->with('warning', __('sortifya.task.claim_limit')),

            default => redirect()
                ->route('dashboard')
                ->with('warning', __('sortifya.task.claim_taken')),
        };
    }

    /** Hands a held task back to the queue. */
    public function release(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->isHeldBy($request->user()), 403);

        $this->tasks->release($task);

        return redirect()->route('dashboard')->with('success', __('sortifya.task.released'));
    }

    /** Receives the finished spreadsheet. */
    public function submit(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->isHeldBy($request->user()), 403, __('sortifya.task.not_yours'));

        // The hold ran out between page load and submit.
        if ($task->lockHasExpired()) {
            $this->tasks->release($task);

            return redirect()->route('dashboard')->with('warning', __('sortifya.task.expired_notice'));
        }

        $request->validate([
            'spreadsheet' => [
                'required',
                'file',
                'mimes:'.implode(',', config('sortifya.uploads.accepted')),
                'max:'.config('sortifya.uploads.max_upload_kb'),
            ],
        ]);

        $this->submissions->store($request->user(), $task, $request->file('spreadsheet'));

        return redirect()
            ->route('dashboard')
            ->with('success', __('sortifya.task.submitted'))
            ->with('celebrate', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Downloads
    |--------------------------------------------------------------------------
    */

    /**
     * Streams a submitted spreadsheet back.
     *
     * Submissions live outside the web root, so this is the only way to reach
     * one — and it checks that the asker either wrote it or reviews them.
     */
    public function downloadSubmission(Request $request, Submission $submission): StreamedResponse
    {
        $user = $request->user();

        abort_unless($submission->user_id === $user->id || $user->isAdmin(), 403);

        $disk = Storage::disk(config('sortifya.uploads.submissions_disk'));

        abort_unless($disk->exists($submission->uploaded_excel_path), 404);

        return $disk->download($submission->uploaded_excel_path, $submission->fileName());
    }
}
