<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasIcon, HasLabel
{
    case Admin = 'admin';
    case User = 'user';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::User => 'Worker',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Admin => 'warning',
            self::User => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Admin => 'heroicon-m-shield-check',
            self::User => 'heroicon-m-user',
        };
    }
}
