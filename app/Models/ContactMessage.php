<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    /** @use HasFactory<\Database\Factories\ContactMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'subject',
        'message',
        'status',
        'was_emailed',
        'ip_address',
        'locale',
    ];

    protected function casts(): array
    {
        return [
            'was_emailed' => 'boolean',
        ];
    }

    /** Set when the sender was signed in; null for a visitor. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function scopeUnhandled(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }

    /** A one-line preview for the admin table. */
    public function excerpt(int $length = 70): string
    {
        return str($this->message)->squish()->limit($length)->toString();
    }
}
