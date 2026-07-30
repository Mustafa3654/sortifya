<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'People';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Account')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(120),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone_number')
                        ->tel()
                        ->maxLength(32)
                        ->helperText('Where Whish Money payouts are sent.'),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn (?string $state) => filled($state))
                        ->required(fn (string $operation) => $operation === 'create')
                        ->helperText('Leave blank to keep the current password.'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Access')
                ->schema([
                    Forms\Components\Select::make('role')
                        ->options(UserRole::class)
                        ->required()
                        ->default(UserRole::User)
                        ->native(false)
                        ->helperText('Admins can reach /admin. Workers cannot.'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Account is open')
                        ->default(true)
                        ->helperText('Turning this off signs the person out and blocks new claims.'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (User $record) => $record->email),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Phone')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                // Summed in one query for the whole page rather than N+1.
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->state(fn (User $record) => '$'.number_format((float) ($record->transactions_sum_amount ?? 0), 2))
                    ->alignEnd()
                    ->sortable(query: fn (Builder $query, string $direction) => $query
                        ->orderBy('transactions_sum_amount', $direction)),

                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('Uploads')
                    ->counts('submissions')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Open')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withSum('transactions as transactions_sum_amount', 'amount'))
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')->options(UserRole::class),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Account open')
                    ->placeholder('Everyone')
                    ->trueLabel('Open only')
                    ->falseLabel('Suspended only'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn (User $record) => $record->is_active ? 'Suspend' : 'Reopen')
                    ->icon(fn (User $record) => $record->is_active ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open')
                    ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalDescription(fn (User $record) => $record->is_active
                        ? 'They are signed out immediately and cannot claim tasks. Their balance is untouched.'
                        : 'They can sign in and claim tasks again.')
                    ->action(function (User $record) {
                        $record->update(['is_active' => ! $record->is_active]);

                        Notification::make()
                            ->success()
                            ->title($record->is_active ? 'Account reopened' : 'Account suspended')
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No accounts yet');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
