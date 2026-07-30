<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskClaimTest extends TestCase
{
    use RefreshDatabase;

    private function worker(): User
    {
        return User::factory()->create(['is_active' => true]);
    }

    public function test_claiming_locks_the_task_to_the_worker(): void
    {
        $worker = $this->worker();
        $task = Task::factory()->create();

        $outcome = app(TaskService::class)->claim($worker, $task);

        $this->assertSame(TaskService::OUTCOME_CLAIMED, $outcome);

        $task->refresh();
        $this->assertSame(TaskStatus::Assigned, $task->status);
        $this->assertSame($worker->id, $task->assigned_to_user_id);
        $this->assertTrue($task->expires_at->isFuture());
    }

    /** The contended case: two workers, one task, exactly one winner. */
    public function test_a_second_worker_cannot_claim_the_same_task(): void
    {
        $first = $this->worker();
        $second = $this->worker();
        $task = Task::factory()->create();

        $tasks = app(TaskService::class);

        $this->assertSame(TaskService::OUTCOME_CLAIMED, $tasks->claim($first, $task));
        $this->assertSame(TaskService::OUTCOME_TAKEN, $tasks->claim($second, $task->fresh()));

        $this->assertSame($first->id, $task->fresh()->assigned_to_user_id);
    }

    public function test_a_worker_may_hold_only_one_task_at_a_time(): void
    {
        $worker = $this->worker();
        $tasks = app(TaskService::class);

        $tasks->claim($worker, Task::factory()->create());

        $this->assertSame(
            TaskService::OUTCOME_AT_LIMIT,
            $tasks->claim($worker, Task::factory()->create()),
        );
    }

    public function test_the_sweeper_returns_lapsed_holds_to_the_queue(): void
    {
        $worker = $this->worker();
        $task = Task::factory()->create();
        $tasks = app(TaskService::class);

        $tasks->claim($worker, $task);
        $task->fresh()->update(['expires_at' => now()->subMinute()]);

        $this->assertSame(1, $tasks->releaseExpired());

        $task->refresh();
        $this->assertSame(TaskStatus::Available, $task->status);
        $this->assertNull($task->assigned_to_user_id);
        $this->assertNull($task->expires_at);
    }

    public function test_a_live_hold_survives_the_sweeper(): void
    {
        $tasks = app(TaskService::class);
        $task = Task::factory()->create();
        $tasks->claim($this->worker(), $task);

        $this->assertSame(0, $tasks->releaseExpired());
        $this->assertSame(TaskStatus::Assigned, $task->fresh()->status);
    }

    public function test_the_workbench_is_closed_to_someone_who_does_not_hold_the_task(): void
    {
        $holder = $this->worker();
        $stranger = $this->worker();
        $task = Task::factory()->create();

        app(TaskService::class)->claim($holder, $task);

        $this->actingAs($stranger)->get(route('tasks.show', $task))->assertForbidden();
        $this->actingAs($holder)->get(route('tasks.show', $task))->assertOk();
    }

    public function test_a_suspended_worker_is_signed_out(): void
    {
        $worker = User::factory()->create(['is_active' => false]);

        $this->actingAs($worker)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }
}
