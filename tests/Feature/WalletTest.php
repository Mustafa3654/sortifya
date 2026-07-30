<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Enums\TaskStatus;
use App\Enums\TransactionType;
use App\Enums\WithdrawalMethod;
use App\Enums\WithdrawalStatus;
use App\Models\Task;
use App\Models\User;
use App\Services\SubmissionService;
use App\Services\TaskService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    private WalletService $wallet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wallet = app(WalletService::class);
    }

    private function workerWith(float $balance): User
    {
        $user = User::factory()->create(['is_active' => true]);

        if ($balance > 0) {
            $this->wallet->credit($user, $balance, TransactionType::Bonus, 'Opening balance');
        }

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | Ledger
    |--------------------------------------------------------------------------
    */

    public function test_a_balance_is_the_sum_of_its_ledger_lines(): void
    {
        $user = User::factory()->create();

        $this->wallet->credit($user, 1.50, TransactionType::TaskReward, 'a');
        $this->wallet->credit($user, 2.25, TransactionType::TaskReward, 'b');
        $this->wallet->debit($user, 1.00, TransactionType::Withdrawal, 'c');

        $this->assertSame(2.75, $this->wallet->balance($user));
    }

    public function test_a_debit_is_stored_negative(): void
    {
        $user = User::factory()->create();
        $line = $this->wallet->debit($user, 4.00, TransactionType::Withdrawal, 'payout');

        $this->assertSame('-4.00', $line->amount);
        $this->assertFalse($line->isCredit());
        $this->assertSame('-$4.00', $line->signedAmount());
    }

    public function test_a_credit_must_be_positive(): void
    {
        $this->expectException(RuntimeException::class);
        $this->wallet->credit(User::factory()->create(), -1, TransactionType::Bonus, 'nope');
    }

    /*
    |--------------------------------------------------------------------------
    | Approval
    |--------------------------------------------------------------------------
    */

    public function test_approving_a_submission_credits_exactly_the_reward_once(): void
    {
        Storage::fake('local');

        $worker = User::factory()->create();
        $admin = User::factory()->create(['role' => \App\Enums\UserRole::Admin]);
        $task = Task::factory()->create(['reward_usd' => 1.75]);

        app(TaskService::class)->claim($worker, $task);

        $submissions = app(SubmissionService::class);
        $submission = $submissions->store(
            $worker,
            $task->fresh(),
            UploadedFile::fake()->createWithContent('rows.csv', "Date,Vendor\n02-14,Acme\n"),
        );

        $submissions->approve($submission, $admin);
        $this->assertSame(1.75, $this->wallet->balance($worker->fresh()));

        // A second click in the admin panel must not pay twice.
        $submissions->approve($submission->fresh(), $admin);
        $this->assertSame(1.75, $this->wallet->balance($worker->fresh()));

        $this->assertSame(SubmissionStatus::Approved, $submission->fresh()->status);
        $this->assertSame(TaskStatus::Completed, $task->fresh()->status);
    }

    public function test_rejecting_a_submission_pays_nothing_and_returns_the_task(): void
    {
        Storage::fake('local');

        $worker = User::factory()->create();
        $admin = User::factory()->create(['role' => \App\Enums\UserRole::Admin]);
        $task = Task::factory()->create(['reward_usd' => 1.75]);

        app(TaskService::class)->claim($worker, $task);

        $submissions = app(SubmissionService::class);
        $submission = $submissions->store(
            $worker,
            $task->fresh(),
            UploadedFile::fake()->createWithContent('rows.csv', "Date,Vendor\n02-14,Acme\n"),
        );

        $submissions->reject($submission, $admin, 'The amount column is missing.');

        $this->assertSame(0.0, $this->wallet->balance($worker->fresh()));
        $this->assertSame(SubmissionStatus::Rejected, $submission->fresh()->status);
        $this->assertSame('The amount column is missing.', $submission->fresh()->rejection_reason);
        // Handed back to the same worker to fix.
        $this->assertSame(TaskStatus::Assigned, $task->fresh()->status);
        $this->assertSame($worker->id, $task->fresh()->assigned_to_user_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Payouts
    |--------------------------------------------------------------------------
    */

    public function test_a_payout_below_the_minimum_is_refused(): void
    {
        $user = $this->workerWith(9.99);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('below_minimum');

        $this->wallet->requestWithdrawal($user, 9.99, WithdrawalMethod::WhishMoney, ['full_name' => 'A']);
    }

    public function test_a_payout_above_the_balance_is_refused(): void
    {
        $user = $this->workerWith(12.00);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('insufficient_funds');

        $this->wallet->requestWithdrawal($user, 25.00, WithdrawalMethod::WhishMoney, ['full_name' => 'A']);
    }

    public function test_requesting_a_payout_debits_the_balance_immediately(): void
    {
        $user = $this->workerWith(25.00);

        $withdrawal = $this->wallet->requestWithdrawal(
            $user, 10.00, WithdrawalMethod::WhishMoney, ['full_name' => 'Rania', 'phone_number' => '+961 71 000 000'],
        );

        $this->assertSame(WithdrawalStatus::Pending, $withdrawal->status);
        $this->assertSame(15.00, $this->wallet->balance($user->fresh()));
        $this->assertTrue($user->fresh()->hasPendingWithdrawal());
    }

    public function test_declining_a_payout_refunds_it_without_erasing_the_debit(): void
    {
        $user = $this->workerWith(25.00);

        $withdrawal = $this->wallet->requestWithdrawal(
            $user, 10.00, WithdrawalMethod::Cash, ['full_name' => 'Rania'],
        );

        $this->wallet->rejectWithdrawal($withdrawal, 'Wrong phone number.');

        $this->assertSame(WithdrawalStatus::Rejected, $withdrawal->fresh()->status);
        $this->assertSame(25.00, $this->wallet->balance($user->fresh()));

        // Both sides of the correction stay on the ledger as the audit trail.
        $lines = $withdrawal->transactions()->orderBy('id')->pluck('amount')->all();
        $this->assertSame(['-10.00', '10.00'], $lines);
    }

    public function test_a_settled_payout_cannot_be_decided_again(): void
    {
        $user = $this->workerWith(25.00);
        $withdrawal = $this->wallet->requestWithdrawal($user, 10.00, WithdrawalMethod::Cash, ['full_name' => 'R']);

        $this->wallet->rejectWithdrawal($withdrawal, 'declined');
        $this->wallet->completeWithdrawal($withdrawal->fresh());

        $this->assertSame(WithdrawalStatus::Rejected, $withdrawal->fresh()->status);
        $this->assertSame(25.00, $this->wallet->balance($user->fresh()));
    }

    public function test_completing_a_payout_moves_no_further_money(): void
    {
        $user = $this->workerWith(25.00);
        $withdrawal = $this->wallet->requestWithdrawal($user, 10.00, WithdrawalMethod::Cash, ['full_name' => 'R']);

        $this->wallet->completeWithdrawal($withdrawal, 'ref 8891');

        $this->assertSame(WithdrawalStatus::Completed, $withdrawal->fresh()->status);
        $this->assertSame(15.00, $this->wallet->balance($user->fresh()));
    }

    /*
    |--------------------------------------------------------------------------
    | HTTP surface
    |--------------------------------------------------------------------------
    */

    public function test_the_withdraw_form_refuses_a_worker_below_the_minimum(): void
    {
        $user = $this->workerWith(4.00);

        $this->actingAs($user)
            ->post(route('wallet.withdraw'), [
                'amount' => 4.00,
                'method' => WithdrawalMethod::Cash->value,
                'full_name' => 'Karim',
                'phone_number' => '+961 76 000 000',
            ])
            ->assertRedirect()
            ->assertSessionHas('warning');

        $this->assertSame(4.00, $this->wallet->balance($user->fresh()));
    }

    public function test_whish_payouts_require_a_phone_number(): void
    {
        $user = $this->workerWith(20.00);

        $this->actingAs($user)
            ->post(route('wallet.withdraw'), [
                'amount' => 10.00,
                'method' => WithdrawalMethod::WhishMoney->value,
                'full_name' => 'Karim',
            ])
            ->assertSessionHasErrors('phone_number');

        $this->assertSame(20.00, $this->wallet->balance($user->fresh()));
    }
}
