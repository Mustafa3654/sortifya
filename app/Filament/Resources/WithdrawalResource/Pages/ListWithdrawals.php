<?php

namespace App\Filament\Resources\WithdrawalResource\Pages;

use App\Enums\WithdrawalStatus;
use App\Filament\Resources\WithdrawalResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListWithdrawals extends ListRecords
{
    protected static string $resource = WithdrawalResource::class;

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Waiting')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereIn('status', [WithdrawalStatus::Pending->value, WithdrawalStatus::Approved->value])
                    ->reorder('created_at', 'asc'))
                ->badge(fn () => WithdrawalResource::getModel()::where('status', WithdrawalStatus::Pending->value)->count())
                ->badgeColor('warning'),

            'completed' => Tab::make('Paid')
                ->icon('heroicon-m-check-badge')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', WithdrawalStatus::Completed->value)),

            'rejected' => Tab::make('Declined')
                ->icon('heroicon-m-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', WithdrawalStatus::Rejected->value)),

            'all' => Tab::make('All'),
        ];
    }
}
