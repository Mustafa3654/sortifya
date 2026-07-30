<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Enums\TaskStatus;
use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New task'),
        ];
    }

    /** Tabs mirror how the queue is actually worked, not the enum order. */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'available' => Tab::make('Open')
                ->icon('heroicon-m-inbox-arrow-down')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TaskStatus::Available->value))
                ->badge(fn () => TaskResource::getModel()::where('status', TaskStatus::Available->value)->count()),

            'assigned' => Tab::make('Claimed')
                ->icon('heroicon-m-lock-closed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TaskStatus::Assigned->value)),

            'completed' => Tab::make('Completed')
                ->icon('heroicon-m-check-badge')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TaskStatus::Completed->value)),

            'archived' => Tab::make('Archived')
                ->icon('heroicon-m-archive-box')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TaskStatus::Archived->value)),
        ];
    }
}
