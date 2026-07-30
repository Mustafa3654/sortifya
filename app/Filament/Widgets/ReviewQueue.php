<?php

namespace App\Filament\Widgets;

use App\Enums\SubmissionStatus;
use App\Filament\Resources\SubmissionResource;
use App\Models\Submission;
use App\Services\SubmissionService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * The review queue, on the dashboard.
 *
 * Reviewing submissions is the job an admin opens this panel to do, so it is
 * actionable from the landing screen rather than two clicks away. Oldest
 * first — the queue is worked front to back.
 */
class ReviewQueue extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    // Five rows off an indexed column; rendering inline beats a deferred load.
    protected static bool $isLazy = false;

    protected static ?string $heading = 'Waiting on review';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Submission::query()
                    ->with(['task', 'user'])
                    ->where('status', SubmissionStatus::Pending->value)
                    ->oldest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('task.title_en')
                    ->label('Task')
                    ->weight('semibold')
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Worker')
                    ->description(fn (Submission $record) => $record->user?->email),

                Tables\Columns\TextColumn::make('task.reward_usd')
                    ->label('Reward')
                    ->money('USD')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('rows')
                    ->label('Rows')
                    ->state(fn (Submission $record) => $record->previewRowCount() ?: '—')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waiting')
                    ->since()
                    ->color(fn (Submission $record) => $record->created_at->lt(now()->subDay()) ? 'warning' : 'gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Submission $record) => SubmissionResource::getUrl('view', ['record' => $record])),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve and pay')
                    ->modalDescription(fn (Submission $record) => sprintf(
                        'Credits $%s to %s and retires the task.',
                        number_format((float) $record->task->reward_usd, 2),
                        $record->user?->name ?? 'the worker',
                    ))
                    ->action(function (Submission $record) {
                        app(SubmissionService::class)->approve($record, auth()->user());

                        Notification::make()->success()->title('Approved')->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Return')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('What needs fixing')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('The worker sees this word for word.'),
                    ])
                    ->action(function (Submission $record, array $data) {
                        app(SubmissionService::class)->reject($record, auth()->user(), $data['reason']);

                        Notification::make()->warning()->title('Returned to the worker')->send();
                    }),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Nothing waiting')
            ->emptyStateDescription('Every submission has been reviewed.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
