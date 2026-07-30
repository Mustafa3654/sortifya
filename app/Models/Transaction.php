<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * One immutable line in a worker's ledger.
 *
 * Nothing in the application updates or deletes a transaction. A mistake is
 * corrected by writing an opposing line, which is why a rejected payout adds
 * a refund rather than removing the original debit.
 */
class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'reference_type',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type' => TransactionType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The Submission or Withdrawal that produced this line. */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function isCredit(): bool
    {
        return (float) $this->amount > 0;
    }

    /** "+$1.25" / "-$10.00" — the sign is always shown. */
    public function signedAmount(): string
    {
        $value = (float) $this->amount;

        return sprintf('%s$%s', $value >= 0 ? '+' : '-', number_format(abs($value), 2));
    }

    public function scopeCredits(Builder $query): Builder
    {
        return $query->where('amount', '>', 0);
    }

    public function scopeDebits(Builder $query): Builder
    {
        return $query->where('amount', '<', 0);
    }
}
