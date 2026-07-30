<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TransactionType: string implements HasColor, HasIcon, HasLabel
{
    case TaskReward = 'task_reward';
    case Withdrawal = 'withdrawal';
    case Refund = 'refund';
    case Bonus = 'bonus';

    public function getLabel(): string
    {
        return __('sortifya.wallet.type.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::TaskReward => 'success',
            self::Withdrawal => 'danger',
            self::Refund => 'info',
            self::Bonus => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::TaskReward => 'heroicon-m-arrow-down-left',
            self::Withdrawal => 'heroicon-m-arrow-up-right',
            self::Refund => 'heroicon-m-arrow-path',
            self::Bonus => 'heroicon-m-gift',
        };
    }

    /** Lucide icon name used by the wallet ledger in the worker UI. */
    public function lucide(): string
    {
        return match ($this) {
            self::TaskReward => 'arrow-down-left',
            self::Withdrawal => 'arrow-up-right',
            self::Refund => 'rotate-ccw',
            self::Bonus => 'gift',
        };
    }
}
