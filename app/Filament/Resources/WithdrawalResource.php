<?php

namespace App\Filament\Resources;

use App\Enums\WithdrawalMethod;
use App\Enums\WithdrawalStatus;
use App\Filament\Resources\WithdrawalResource\Pages;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Money';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Payouts';

    protected static ?string $modelLabel = 'payout';

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', WithdrawalStatus::Pending->value)->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Worker')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn (Withdrawal $record) => $record->user?->email),

                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('method')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('payout_to')
                    ->label('Pay to')
                    ->state(fn (Withdrawal $record) => $record->payoutSummary())
                    ->wrap()
                    ->searchable(false),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(WithdrawalStatus::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('method')
                    ->options(WithdrawalMethod::class)
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\Action::make('complete')
                    ->label('Mark paid')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (Withdrawal $record) => ! $record->isSettled())
                    ->form([
                        Forms\Components\Placeholder::make('summary')
                            ->label('Sending')
                            ->content(fn (Withdrawal $record) => sprintf(
                                '$%s by %s to %s',
                                number_format((float) $record->amount, 2),
                                $record->method->getLabel(),
                                $record->payoutSummary(),
                            )),

                        Forms\Components\TextInput::make('note')
                            ->label('Reference')
                            ->maxLength(255)
                            ->helperText('Optional — a transfer id or receipt number.'),
                    ])
                    ->modalHeading('Confirm the money has been sent')
                    // The debit was written when the worker requested it, so
                    // this only closes the row — it moves nothing.
                    ->modalDescription('The balance was already debited when this was requested. This records that it was paid.')
                    ->action(function (Withdrawal $record, array $data) {
                        app(WalletService::class)->completeWithdrawal($record, $data['note'] ?? null);

                        Notification::make()->success()->title('Marked as paid')->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Decline')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (Withdrawal $record) => ! $record->isSettled())
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason')
                            ->required()
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->modalHeading('Decline and refund')
                    ->modalDescription('Writes a positive refund line, putting the amount back on the worker’s balance.')
                    ->action(function (Withdrawal $record, array $data) {
                        app(WalletService::class)->rejectWithdrawal($record, $data['reason']);

                        Notification::make()
                            ->warning()
                            ->title('Declined and refunded')
                            ->body('The amount is back on the worker’s balance.')
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->emptyStateHeading('No payout requests')
            ->emptyStateDescription('Requests appear here as soon as a worker asks to withdraw.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Request')
                ->schema([
                    Forms\Components\TextInput::make('amount')->prefix('$')->disabled(),
                    Forms\Components\Select::make('method')->options(WithdrawalMethod::class)->disabled(),
                    Forms\Components\Select::make('status')->options(WithdrawalStatus::class)->disabled(),
                    Forms\Components\KeyValue::make('payout_details')
                        ->label('Payout details')
                        ->disabled()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('admin_note')
                        ->label('Admin note')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            'view' => Pages\ViewWithdrawal::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
