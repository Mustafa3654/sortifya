<?php

namespace App\Filament\Resources\WithdrawalResource\Pages;

use App\Filament\Resources\WithdrawalResource;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewWithdrawal extends ViewRecord
{
    protected static string $resource = WithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Withdrawal $record */
        $record = $this->getRecord();

        return [
            Actions\Action::make('complete')
                ->label('Mark paid')
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->visible(fn () => ! $record->isSettled())
                ->requiresConfirmation()
                ->action(function () use ($record) {
                    app(WalletService::class)->completeWithdrawal($record);

                    Notification::make()->success()->title('Marked as paid')->send();
                }),

            Actions\Action::make('reject')
                ->label('Decline and refund')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn () => ! $record->isSettled())
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Reason')->required()->rows(3),
                ])
                ->action(function (array $data) use ($record) {
                    app(WalletService::class)->rejectWithdrawal($record, $data['reason']);

                    Notification::make()->warning()->title('Declined and refunded')->send();
                }),
        ];
    }
}
