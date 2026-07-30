<?php

namespace App\Models;

use App\Enums\WithdrawalMethod;
use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Withdrawal extends Model
{
    /** @use HasFactory<\Database\Factories\WithdrawalFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'method',
        'payout_details',
        'status',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'method' => WithdrawalMethod::class,
            'status' => WithdrawalStatus::class,
            'payout_details' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The debit, plus the refund line if this request was rejected. */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    /*
    |--------------------------------------------------------------------------
    | Payout details
    |--------------------------------------------------------------------------
    */

    public function payoutName(): ?string
    {
        return $this->payout_details['full_name'] ?? null;
    }

    public function payoutPhone(): ?string
    {
        return $this->payout_details['phone_number'] ?? null;
    }

    public function payoutNote(): ?string
    {
        return $this->payout_details['note'] ?? null;
    }

    /** One-line summary for the Telegram alert and the admin table. */
    public function payoutSummary(): string
    {
        return collect([$this->payoutName(), $this->payoutPhone()])
            ->filter()
            ->implode(' · ') ?: '—';
    }

    public function isSettled(): bool
    {
        return $this->status->isSettled();
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', WithdrawalStatus::Pending->value);
    }
}
