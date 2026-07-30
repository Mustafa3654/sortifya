<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\TransactionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * The worker's ledger, read-only apart from a manual bonus.
 *
 * Existing lines cannot be edited or deleted — a mistake is corrected by
 * writing an opposing line, which is what keeps the balance reconstructible.
 */
class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Ledger';

    protected static ?string $icon = 'heroicon-o-list-bullet';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')
                ->label('Amount')
                ->numeric()
                ->prefix('$')
                ->required()
                ->step(0.01)
                ->minValue(0.01)
                ->helperText('Positive. Bonuses only — use the payout screen to move money out.'),

            Forms\Components\TextInput::make('description')
                ->required()
                ->maxLength(255)
                ->helperText('The worker sees this in their wallet.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->wrap()
                    ->limit(60),

                Tables\Columns\TextColumn::make('type')
                    ->badge(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->state(fn ($record) => $record->signedAmount())
                    ->alignEnd()
                    ->weight('semibold')
                    ->color(fn ($record) => $record->isCredit() ? 'success' : 'danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(TransactionType::class)
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add bonus')
                    ->icon('heroicon-m-gift')
                    ->modalHeading('Credit a bonus')
                    ->using(function (array $data) {
                        return app(\App\Services\WalletService::class)->credit(
                            $this->getOwnerRecord(),
                            (float) $data['amount'],
                            TransactionType::Bonus,
                            $data['description'],
                        );
                    })
                    ->after(fn () => Notification::make()->success()->title('Bonus credited')->send()),
            ])
            ->actions([])
            ->bulkActions([])
            ->emptyStateHeading('No ledger lines yet')
            ->emptyStateDescription('Approved tasks and payouts both write here.');
    }
}
