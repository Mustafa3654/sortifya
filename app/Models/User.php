<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use App\Enums\TaskStatus;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Enums\WithdrawalStatus;
use App\Notifications\ResetPasswordNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /** Tasks currently locked to this user. */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to_user_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Money
    |--------------------------------------------------------------------------
    |
    | Balances are always derived from the ledger. Nothing caches a total,
    | so a displayed figure can never drift from the rows behind it.
    |
    */

    /** Spendable balance: every signed ledger line, summed. */
    public function balance(): float
    {
        return round((float) $this->transactions()->sum('amount'), 2);
    }

    /** Rewards for work that is uploaded but not yet reviewed. */
    public function pendingEarnings(): float
    {
        return round((float) $this->submissions()
            ->where('submissions.status', SubmissionStatus::Pending->value)
            ->join('tasks', 'tasks.id', '=', 'submissions.task_id')
            ->sum('tasks.reward_usd'), 2);
    }

    public function lifetimeEarned(): float
    {
        return round((float) $this->transactions()->where('amount', '>', 0)->sum('amount'), 2);
    }

    public function lifetimeWithdrawn(): float
    {
        return round(abs((float) $this->transactions()
            ->where('type', TransactionType::Withdrawal->value)
            ->sum('amount')), 2);
    }

    /** A worker may only have one payout in flight at a time. */
    public function hasPendingWithdrawal(): bool
    {
        return $this->withdrawals()
            ->whereIn('status', [WithdrawalStatus::Pending->value, WithdrawalStatus::Approved->value])
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Roles & access
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /** Gate for /admin. A suspended admin loses the panel too. */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() && $this->is_active;
    }

    public function scopeWorkers(Builder $query): Builder
    {
        return $query->where('role', UserRole::User->value);
    }

    /*
    |--------------------------------------------------------------------------
    | Workload
    |--------------------------------------------------------------------------
    */

    /** The task this user holds right now, if the hold is still live. */
    public function activeTask(): ?Task
    {
        return $this->assignedTasks()
            ->where('status', TaskStatus::Assigned->value)
            ->where('expires_at', '>', now())
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    /** Sends the reset mail in whichever language the request was made in. */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token, app()->getLocale()));
    }
}
