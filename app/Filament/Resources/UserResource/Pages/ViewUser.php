<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Account')
                ->schema([
                    Infolists\Components\TextEntry::make('name'),
                    Infolists\Components\TextEntry::make('email')->copyable(),
                    Infolists\Components\TextEntry::make('phone_number')->label('Phone')->placeholder('—'),
                    Infolists\Components\TextEntry::make('role')->badge(),
                    Infolists\Components\IconEntry::make('is_active')->label('Account open')->boolean(),
                    Infolists\Components\TextEntry::make('created_at')->label('Joined')->dateTime('d M Y'),
                ])
                ->columns(3),

            Infolists\Components\Section::make('Money')
                ->description('All four figures are derived from the ledger below.')
                ->schema([
                    Infolists\Components\TextEntry::make('balance')
                        ->label('Available balance')
                        ->state(fn ($record) => '$'.number_format($record->balance(), 2))
                        ->weight('bold')
                        ->size(TextEntrySize::Large)
                        ->color('success'),

                    Infolists\Components\TextEntry::make('earned')
                        ->label('Earned all time')
                        ->state(fn ($record) => '$'.number_format($record->lifetimeEarned(), 2)),

                    Infolists\Components\TextEntry::make('withdrawn')
                        ->label('Paid out')
                        ->state(fn ($record) => '$'.number_format($record->lifetimeWithdrawn(), 2)),

                    Infolists\Components\TextEntry::make('pending')
                        ->label('Awaiting review')
                        ->state(fn ($record) => '$'.number_format($record->pendingEarnings(), 2))
                        ->color('warning'),
                ])
                ->columns(4),
        ]);
    }
}
