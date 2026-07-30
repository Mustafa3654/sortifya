<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Enums\SubmissionStatus;
use App\Filament\Resources\SubmissionResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSubmissions extends ListRecords
{
    protected static string $resource = SubmissionResource::class;

    public function getTabs(): array
    {
        return [
            // Oldest first: the queue is worked front to back.
            'pending' => Tab::make('In review')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', SubmissionStatus::Pending->value)
                    ->reorder('created_at', 'asc'))
                ->badge(fn () => SubmissionResource::getModel()::where('status', SubmissionStatus::Pending->value)->count())
                ->badgeColor('warning'),

            'approved' => Tab::make('Approved')
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', SubmissionStatus::Approved->value)),

            'rejected' => Tab::make('Returned')
                ->icon('heroicon-m-arrow-uturn-left')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', SubmissionStatus::Rejected->value)),

            'all' => Tab::make('All'),
        ];
    }
}
