<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Owns who holds which task.
 *
 * Claiming is the one genuinely contended operation on the platform: several
 * workers refresh the same queue and press Claim within the same second. The
 * row is locked and re-checked inside the transaction so exactly one of them
 * wins and the rest get a clean "already taken".
 */
class TaskService
{
    public const OUTCOME_CLAIMED = 'claimed';

    public const OUTCOME_TAKEN = 'taken';

    public const OUTCOME_AT_LIMIT = 'at_limit';

    public function holdMinutes(): int
    {
        return (int) config('sortifya.task_hold_minutes');
    }

    /**
     * Attempts to lock a task to a worker.
     *
     * @return self::OUTCOME_*
     */
    public function claim(User $user, Task $task): string
    {
        return DB::transaction(function () use ($user, $task) {
            $held = Task::heldBy($user)
                ->where('expires_at', '>', now())
                ->count();

            if ($held >= (int) config('sortifya.max_concurrent_tasks')) {
                return self::OUTCOME_AT_LIMIT;
            }

            // SELECT ... FOR UPDATE. Whoever gets here second sees the row
            // already flipped to 'assigned' and backs out.
            $locked = Task::whereKey($task->getKey())->lockForUpdate()->first();

            if (! $locked || $locked->status !== TaskStatus::Available) {
                return self::OUTCOME_TAKEN;
            }

            $locked->update([
                'status' => TaskStatus::Assigned,
                'assigned_to_user_id' => $user->id,
                'assigned_at' => now(),
                'expires_at' => now()->addMinutes($this->holdMinutes()),
            ]);

            return self::OUTCOME_CLAIMED;
        });
    }

    /** Hands a task back to the queue, whether by choice or by timeout. */
    public function release(Task $task): void
    {
        DB::transaction(function () use ($task) {
            $locked = Task::whereKey($task->getKey())->lockForUpdate()->first();

            if (! $locked || $locked->status !== TaskStatus::Assigned) {
                return;
            }

            $locked->update([
                'status' => TaskStatus::Available,
                'assigned_to_user_id' => null,
                'assigned_at' => null,
                'expires_at' => null,
            ]);
        });
    }

    /**
     * Returns every task whose hold has lapsed. Driven by the
     * `tasks:release-expired` command every five minutes.
     *
     * @return int the number of tasks put back
     */
    public function releaseExpired(): int
    {
        $released = 0;

        // Chunked so a long backlog does not load the whole table at once.
        Task::lapsed()->select('id')->chunkById(200, function ($tasks) use (&$released) {
            foreach ($tasks as $task) {
                $this->release($task);
                $released++;
            }
        });

        return $released;
    }

    /**
     * Locks a task as done. Called when a submission is approved — the task
     * leaves the queue for good rather than returning to it.
     */
    public function markCompleted(Task $task): void
    {
        $task->update([
            'status' => TaskStatus::Completed,
            'expires_at' => null,
        ]);
    }

    /**
     * Puts a task back after its submission was rejected, so the same worker
     * (or another) can redo it. The hold restarts from now.
     */
    public function reopenForRework(Task $task, ?User $keepWith = null): void
    {
        if ($keepWith !== null) {
            $task->update([
                'status' => TaskStatus::Assigned,
                'assigned_to_user_id' => $keepWith->id,
                'assigned_at' => now(),
                'expires_at' => now()->addMinutes($this->holdMinutes()),
            ]);

            return;
        }

        $this->release($task);
    }
}
