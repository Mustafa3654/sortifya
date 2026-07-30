<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Enums\WithdrawalStatus;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The only thing in the application allowed to write to the ledger.
 *
 * Every method here appends. Nothing edits or deletes an existing line, so
 * a balance is always reconstructible by replaying the table.
 */
class WalletService
{
    /*
    |--------------------------------------------------------------------------
    | Reading
    |--------------------------------------------------------------------------
    */

    public function balance(User $user): float
    {
        return round((float) $user->transactions()->sum('amount'), 2);
    }

    public function minimumWithdrawal(): float
    {
        return (float) config('sortifya.minimum_withdrawal');
    }

    public function canWithdraw(User $user): bool
    {
        return $this->balance($user) >= $this->minimumWithdrawal();
    }

    /*
    |--------------------------------------------------------------------------
    | Writing
    |--------------------------------------------------------------------------
    */

    /** Adds money. `$amount` must be positive. */
    public function credit(
        User $user,
        float $amount,
        TransactionType $type,
        string $description,
        ?Model $reference = null,
    ): Transaction {
        if ($amount <= 0) {
            throw new RuntimeException('A credit must be a positive amount.');
        }

        return $this->write($user, abs($amount), $type, $description, $reference);
    }

    /** Removes money. `$amount` is given positive and stored negative. */
    public function debit(
        User $user,
        float $amount,
        TransactionType $type,
        string $description,
        ?Model $reference = null,
    ): Transaction {
        if ($amount <= 0) {
            throw new RuntimeException('A debit must be a positive amount.');
        }

        return $this->write($user, -abs($amount), $type, $description, $reference);
    }

    private function write(
        User $user,
        float $signedAmount,
        TransactionType $type,
        string $description,
        ?Model $reference,
    ): Transaction {
        return $user->transactions()->create([
            'amount' => round($signedAmount, 2),
            'type' => $type,
            'description' => $description,
            'reference_type' => $reference ? $reference::class : null,
            'reference_id' => $reference?->getKey(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Payouts
    |--------------------------------------------------------------------------
    */

    /**
     * Creates a payout request and debits the balance in the same breath, so
     * the money is reserved the moment the worker asks for it and cannot be
     * spent twice by two concurrent requests.
     *
     * @param  array<string, mixed>  $payoutDetails
     *
     * @throws RuntimeException when the balance moved between check and commit
     */
    public function requestWithdrawal(
        User $user,
        float $amount,
        \App\Enums\WithdrawalMethod $method,
        array $payoutDetails,
    ): Withdrawal {
        return DB::transaction(function () use ($user, $amount, $method, $payoutDetails) {
            // Re-read the balance inside the transaction with the user row
            // locked: two tabs must not both pass the affordability check.
            $locked = User::whereKey($user->getKey())->lockForUpdate()->firstOrFail();
            $balance = $this->balance($locked);

            if ($amount < $this->minimumWithdrawal()) {
                throw new RuntimeException('below_minimum');
            }

            if ($amount > $balance) {
                throw new RuntimeException('insufficient_funds');
            }

            $withdrawal = $locked->withdrawals()->create([
                'amount' => round($amount, 2),
                'method' => $method,
                'payout_details' => $payoutDetails,
                'status' => WithdrawalStatus::Pending,
            ]);

            $this->debit(
                $locked,
                $amount,
                TransactionType::Withdrawal,
                "Payout request #{$withdrawal->id} · {$method->value}",
                $withdrawal,
            );

            return $withdrawal;
        });
    }

    /**
     * Marks a payout as paid. The debit was already written when the request
     * was created, so completing it moves no money — it only closes the row.
     */
    public function completeWithdrawal(Withdrawal $withdrawal, ?string $note = null): Withdrawal
    {
        return DB::transaction(function () use ($withdrawal, $note) {
            $fresh = Withdrawal::whereKey($withdrawal->getKey())->lockForUpdate()->firstOrFail();

            if ($fresh->isSettled()) {
                return $fresh;
            }

            $fresh->update([
                'status' => WithdrawalStatus::Completed,
                'admin_note' => $note ?? $fresh->admin_note,
            ]);

            return $fresh;
        });
    }

    /**
     * Turns a payout down and gives the money back with a positive refund
     * line. The original debit stays on the ledger — the pair of rows is the
     * audit trail.
     */
    public function rejectWithdrawal(Withdrawal $withdrawal, ?string $reason = null): Withdrawal
    {
        return DB::transaction(function () use ($withdrawal, $reason) {
            $fresh = Withdrawal::whereKey($withdrawal->getKey())->lockForUpdate()->firstOrFail();

            if ($fresh->isSettled()) {
                return $fresh;
            }

            $fresh->update([
                'status' => WithdrawalStatus::Rejected,
                'admin_note' => $reason ?? $fresh->admin_note,
            ]);

            $this->credit(
                $fresh->user,
                (float) $fresh->amount,
                TransactionType::Refund,
                "Refund for declined payout #{$fresh->id}",
                $fresh,
            );

            return $fresh;
        });
    }
}
