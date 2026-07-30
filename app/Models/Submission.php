<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Submission extends Model
{
    /** @use HasFactory<\Database\Factories\SubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'uploaded_excel_path',
        'parsed_preview_data',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'parsed_preview_data' => 'array',
            'status' => SubmissionStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** The reward line this submission produced, if it was approved. */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    |
    | parsed_preview_data is stored as { headers: [...], rows: [[...]] }.
    | Both accessors tolerate a null or malformed blob, because a bad
    | upload must never take the admin table down with it.
    |
    */

    /** @return array<int, string> */
    public function previewHeaders(): array
    {
        return array_values((array) ($this->parsed_preview_data['headers'] ?? []));
    }

    /** @return array<int, array<int, string>> */
    public function previewRows(): array
    {
        return array_values((array) ($this->parsed_preview_data['rows'] ?? []));
    }

    public function previewRowCount(): int
    {
        return (int) ($this->parsed_preview_data['total_rows'] ?? count($this->previewRows()));
    }

    public function hasPreview(): bool
    {
        return $this->previewHeaders() !== [] || $this->previewRows() !== [];
    }

    public function fileName(): string
    {
        return basename($this->uploaded_excel_path);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', SubmissionStatus::Pending->value);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', SubmissionStatus::Approved->value);
    }
}
