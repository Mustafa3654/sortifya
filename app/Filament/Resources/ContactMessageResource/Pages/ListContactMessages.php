<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    public function getTabs(): array
    {
        return [
            'new' => Tab::make('New')
                ->icon('heroicon-m-envelope')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'new'))
                ->badge(fn () => ContactMessageResource::getModel()::where('status', 'new')->count())
                ->badgeColor('warning'),

            'handled' => Tab::make('Handled')
                ->icon('heroicon-m-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'handled')),

            'all' => Tab::make('All'),
        ];
    }
}
