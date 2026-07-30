<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_ar',
        'description_en',
        'description_ar',
        'pdf_file_path',
        'sample_template_path',
        'reward_usd',
        'status',
        'assigned_to_user_id',
        'assigned_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'reward_usd' => 'decimal:2',
            'status' => TaskStatus::class,
            'assigned_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function latestSubmission(): ?Submission
    {
        return $this->submissions()->latest()->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Localised content
    |--------------------------------------------------------------------------
    |
    | Titles live in two columns rather than a JSON blob so both are
    | required at authoring time and neither can silently go missing.
    |
    */

    public function title(?string $locale = null): string
    {
        return ($locale ?? app()->getLocale()) === 'ar'
            ? $this->title_ar
            : $this->title_en;
    }

    public function description(?string $locale = null): string
    {
        return ($locale ?? app()->getLocale()) === 'ar'
            ? $this->description_ar
            : $this->description_en;
    }

    /*
    |--------------------------------------------------------------------------
    | Lock state
    |--------------------------------------------------------------------------
    */

    public function isAvailable(): bool
    {
        return $this->status === TaskStatus::Available;
    }

    public function isHeldBy(?User $user): bool
    {
        return $user !== null
            && $this->status === TaskStatus::Assigned
            && $this->assigned_to_user_id === $user->id;
    }

    /** True once the 45-minute hold has run out but before the sweeper ran. */
    public function lockHasExpired(): bool
    {
        return $this->status === TaskStatus::Assigned
            && $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    public function secondsRemaining(): int
    {
        if ($this->expires_at === null) {
            return 0;
        }

        // Carbon 3 returns a float here; round before narrowing to int.
        return max(0, (int) round(now()->diffInSeconds($this->expires_at, false)));
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::Available->value);
    }

    public function scopeHeldBy(Builder $query, User $user): Builder
    {
        return $query->where('assigned_to_user_id', $user->id)
            ->where('status', TaskStatus::Assigned->value);
    }

    /** Assigned tasks whose hold has lapsed — the sweeper's work list. */
    public function scopeLapsed(Builder $query): Builder
    {
        return $query->where('status', TaskStatus::Assigned->value)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }
}
