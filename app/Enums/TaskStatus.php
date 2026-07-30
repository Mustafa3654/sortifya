<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TaskStatus: string implements HasColor, HasIcon, HasLabel
{
    case Available = 'available';
    case Assigned = 'assigned';
    case Completed = 'completed';
    case Archived = 'archived';

    /** Worker-facing label, translated. Filament reads the same strings. */
    public function getLabel(): string
    {
        return __('sortifya.status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::Assigned => 'warning',
            self::Completed => 'info',
            self::Archived => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Available => 'heroicon-m-inbox-arrow-down',
            self::Assigned => 'heroicon-m-lock-closed',
            self::Completed => 'heroicon-m-check-badge',
            self::Archived => 'heroicon-m-archive-box',
        };
    }

    /** Maps onto the .chip-* component classes in app.css. */
    public function chipClass(): string
    {
        return match ($this) {
            self::Available => 'chip-ok',
            self::Assigned => 'chip-wait',
            self::Completed => 'chip-mute',
            self::Archived => 'chip-mute',
        };
    }
}
