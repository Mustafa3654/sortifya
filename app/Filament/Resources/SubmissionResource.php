<?php

namespace App\Filament\Resources;

use App\Enums\SubmissionStatus;
use App\Filament\Resources\SubmissionResource\Pages;
use App\Models\Submission;
use App\Services\SubmissionService;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Work';

    protected static ?int $navigationSort = 2;

    /** The review queue is the one number an admin acts on every day. */
    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', SubmissionStatus::Pending->value)->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /*
    |--------------------------------------------------------------------------
    | Review view
    |--------------------------------------------------------------------------
    */

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Submission')
                ->schema([
                    Infolists\Components\TextEntry::make('task.title_en')->label('Task'),
                    Infolists\Components\TextEntry::make('user.name')->label('Worker'),
                    Infolists\Components\TextEntry::make('task.reward_usd')->label('Reward')->money('USD'),
                    Infolists\Components\TextEntry::make('status')->badge(),
                    Infolists\Components\TextEntry::make('created_at')->label('Uploaded')->dateTime('d M Y, H:i'),
                    Infolists\Components\TextEntry::make('reviewer.name')->label('Reviewed by')->placeholder('—'),
                ])
                ->columns(3),

            Infolists\Components\Section::make('Spreadsheet preview')
                ->description('The first rows, lifted at upload time. Download the file for anything past this.')
                ->schema([
                    Infolists\Components\ViewEntry::make('parsed_preview_data')
                        ->view('filament.submission-preview')
                        ->hiddenLabel()
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Outcome')
                ->schema([
                    Infolists\Components\TextEntry::make('rejection_reason')
                        ->label('Why it was returned')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])
                ->visible(fn (Submission $record) => $record->status === SubmissionStatus::Rejected),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('task.title_en')
                    ->label('Task')
                    ->searchable()
                    ->weight('semibold')
                    ->limit(45)
                    ->wrap(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Worker')
                    ->searchable()
                    ->description(fn (Submission $record) => $record->user?->email),

                Tables\Columns\TextColumn::make('task.reward_usd')
                    ->label('Reward')
                    ->money('USD')
                    ->alignEnd()
                    ->sortable(),

                // Row count comes from the preview blob, so it costs no I/O.
                Tables\Columns\TextColumn::make('rows')
                    ->label('Rows')
                    ->state(fn (Submission $record) => $record->previewRowCount() ?: '—')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SubmissionStatus::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Review')
                    ->icon('heroicon-m-eye'),

                Tables\Actions\Action::make('download')
                    ->label('File')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('gray')
                    ->visible(fn (Submission $record) => Storage::disk(config('sortifya.uploads.submissions_disk'))
                        ->exists($record->uploaded_excel_path))
                    ->action(fn (Submission $record) => Storage::disk(config('sortifya.uploads.submissions_disk'))
                        ->download($record->uploaded_excel_path, $record->fileName())),

                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Submission $record) => $record->status !== SubmissionStatus::Approved)
                    ->requiresConfirmation()
                    ->modalHeading('Approve and pay')
                    ->modalDescription(fn (Submission $record) => sprintf(
                        'Credits $%s to %s and retires the task.',
                        number_format((float) $record->task->reward_usd, 2),
                        $record->user?->name ?? 'the worker',
                    ))
                    ->action(function (Submission $record) {
                        app(SubmissionService::class)->approve($record, auth()->user());

                        Notification::make()
                            ->success()
                            ->title('Approved')
                            ->body('The reward is on the worker’s balance.')
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Return')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn (Submission $record) => $record->status === SubmissionStatus::Pending)
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('What needs fixing')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('The worker sees this word for word. Be specific about which columns are wrong.'),

                        Forms\Components\Toggle::make('keep_with_worker')
                            ->label('Give it back to the same worker')
                            ->default(true)
                            ->helperText('Off returns the task to the open queue for anyone to claim.'),
                    ])
                    ->action(function (Submission $record, array $data) {
                        app(SubmissionService::class)->reject(
                            $record,
                            auth()->user(),
                            $data['reason'],
                            (bool) ($data['keep_with_worker'] ?? true),
                        );

                        Notification::make()
                            ->warning()
                            ->title('Returned to the worker')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approveMany')
                        ->label('Approve selected')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Each approval credits its own reward. This cannot be undone from here.')
                        ->action(function ($records) {
                            $service = app(SubmissionService::class);
                            $records->each(fn (Submission $r) => $service->approve($r, auth()->user()));

                            Notification::make()
                                ->success()
                                ->title(sprintf('Approved %d submission%s', $records->count(), $records->count() === 1 ? '' : 's'))
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('Nothing to review')
            ->emptyStateDescription('Uploaded spreadsheets land here the moment a worker submits one.')
            ->emptyStateIcon('heroicon-o-inbox');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubmissions::route('/'),
            'view' => Pages\ViewSubmission::route('/{record}'),
        ];
    }

    /** Reviewers never create a submission by hand. */
    public static function canCreate(): bool
    {
        return false;
    }
}
