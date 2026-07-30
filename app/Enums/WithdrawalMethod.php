<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WithdrawalMethod: string implements HasIcon, HasLabel
{
    case WhishMoney = 'whish_money';
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::WhishMoney => __('sortifya.wallet.method_whish'),
            self::Cash => __('sortifya.wallet.method_cash'),
            self::BankTransfer => __('sortifya.wallet.method_bank'),
            self::Other => __('sortifya.wallet.method_other'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::WhishMoney => 'heroicon-m-device-phone-mobile',
            self::Cash => 'heroicon-m-banknotes',
            self::BankTransfer => 'heroicon-m-building-library',
            self::Other => 'heroicon-m-ellipsis-horizontal-circle',
        };
    }

    public function lucide(): string
    {
        return match ($this) {
            self::WhishMoney => 'smartphone',
            self::Cash => 'banknote',
            self::BankTransfer => 'landmark',
            self::Other => 'circle-ellipsis',
        };
    }

    /** Methods that need a phone number to actually pay someone. */
    public function requiresPhone(): bool
    {
        return in_array($this, [self::WhishMoney, self::Cash], true);
    }
}
