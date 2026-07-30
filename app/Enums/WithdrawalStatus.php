<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WithdrawalStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Completed = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => __('sortifya.status.pending'),
            self::Approved => __('sortifya.status.approved'),
            self::Rejected => __('sortifya.status.rejected'),
            self::Completed => __('sortifya.status.completed'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'info',
            self::Rejected => 'danger',
            self::Completed => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-m-clock',
            self::Approved => 'heroicon-m-check',
            self::Rejected => 'heroicon-m-x-circle',
            self::Completed => 'heroicon-m-check-badge',
        };
    }

    public function chipClass(): string
    {
        return match ($this) {
            self::Pending => 'chip-wait',
            self::Approved => 'chip-wait',
            self::Rejected => 'chip-bad',
            self::Completed => 'chip-ok',
        };
    }

    /** Once money has moved (or been refunded) the request is closed. */
    public function isSettled(): bool
    {
        return in_array($this, [self::Completed, self::Rejected], true);
    }
}
