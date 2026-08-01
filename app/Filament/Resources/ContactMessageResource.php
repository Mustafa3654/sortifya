<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'People';

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $modelLabel = 'message';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $unread = static::getModel()::where('status', 'new')->count();

        return $unread > 0 ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make()
                ->schema([
                    Infolists\Components\TextEntry::make('name')->label('From'),
                    Infolists\Components\TextEntry::make('email')->copyable(),
                    Infolists\Components\TextEntry::make('user.name')
                        ->label('Account')
                        ->placeholder('Not signed in'),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Received')
                        ->dateTime('d M Y, H:i'),
                    Infolists\Components\TextEntry::make('status')->badge()
                        ->color(fn (string $state) => $state === 'new' ? 'warning' : 'success'),
                    Infolists\Components\IconEntry::make('was_emailed')
                        ->label('Notification sent')
                        ->boolean(),
                ])
                ->columns(3),

            Infolists\Components\Section::make('Message')
                ->schema([
                    Infolists\Components\TextEntry::make('subject')->weight('bold'),
                    Infolists\Components\TextEntry::make('message')
                        ->prose()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('status')
                    ->label('')
                    ->icon(fn (string $state) => $state === 'new' ? 'heroicon-s-envelope' : 'heroicon-o-envelope-open')
                    ->color(fn (string $state) => $state === 'new' ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->weight(fn (ContactMessage $record) => $record->isNew() ? 'semibold' : 'normal')
                    ->description(fn (ContactMessage $record) => $record->excerpt())
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('name')
                    ->label('From')
                    ->searchable()
                    ->description(fn (ContactMessage $record) => $record->email),

                // A false here means the row is the only copy of the message.
                Tables\Columns\IconColumn::make('was_emailed')
                    ->label('Emailed')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['new' => 'New', 'handled' => 'Handled']),

                Tables\Filters\Filter::make('undelivered')
                    ->label('Notification failed')
                    ->query(fn ($query) => $query->where('was_emailed', false))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('gray')
                    ->url(fn (ContactMessage $record) => 'mailto:'.$record->email
                        .'?subject='.rawurlencode('Re: '.$record->subject))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('toggleHandled')
                    ->label(fn (ContactMessage $record) => $record->isNew() ? 'Mark handled' : 'Reopen')
                    ->icon(fn (ContactMessage $record) => $record->isNew() ? 'heroicon-m-check' : 'heroicon-m-arrow-path')
                    ->color(fn (ContactMessage $record) => $record->isNew() ? 'success' : 'gray')
                    ->action(function (ContactMessage $record) {
                        $record->update(['status' => $record->isNew() ? 'handled' : 'new']);

                        Notification::make()->success()->title('Updated')->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markHandled')
                        ->label('Mark handled')
                        ->icon('heroicon-m-check')
                        ->action(fn ($records) => $records->each->update(['status' => 'handled'])),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No messages')
            ->emptyStateDescription('Anything sent through the contact page lands here.')
            ->emptyStateIcon('heroicon-o-envelope');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
