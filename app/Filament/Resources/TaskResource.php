<?php

namespace App\Filament\Resources;

use App\Enums\TaskStatus;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Work';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title_en';

    /** Open tasks are the queue depth — worth showing at a glance. */
    public static function getNavigationBadge(): ?string
    {
        $open = static::getModel()::where('status', TaskStatus::Available->value)->count();

        return $open > 0 ? (string) $open : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Content')
                ->description('Both languages are required — a task must never appear half-translated.')
                ->schema([
                    Forms\Components\Tabs::make('Translations')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('English')
                                ->icon('heroicon-m-language')
                                ->schema([
                                    Forms\Components\TextInput::make('title_en')
                                        ->label('Title')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Invoice batch 042 — 38 rows'),

                                    Forms\Components\Textarea::make('description_en')
                                        ->label('Description')
                                        ->required()
                                        ->rows(4)
                                        ->helperText('Say what the columns are and anything easy to get wrong.'),
                                ]),

                            Forms\Components\Tabs\Tab::make('العربية')
                                ->icon('heroicon-m-language')
                                ->schema([
                                    Forms\Components\TextInput::make('title_ar')
                                        ->label('العنوان')
                                        ->required()
                                        ->maxLength(255)
                                        ->extraInputAttributes(['dir' => 'rtl']),

                                    Forms\Components\Textarea::make('description_ar')
                                        ->label('الوصف')
                                        ->required()
                                        ->rows(4)
                                        ->extraInputAttributes(['dir' => 'rtl']),
                                ]),
                        ])
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Files')
                ->schema([
                    Forms\Components\FileUpload::make('pdf_file_path')
                        ->label('Source PDF')
                        ->required()
                        ->disk(config('sortifya.uploads.tasks_disk'))
                        ->directory(config('sortifya.uploads.source_path'))
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(20480)
                        ->downloadable()
                        ->openable()
                        ->helperText('The scanned document the worker transcribes.'),

                    Forms\Components\FileUpload::make('sample_template_path')
                        ->label('Column template')
                        ->disk(config('sortifya.uploads.tasks_disk'))
                        ->directory(config('sortifya.uploads.template_path'))
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->maxSize(10240)
                        ->downloadable()
                        ->helperText('Optional, but submissions match far more often when you ship one.'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Reward & state')
                ->schema([
                    Forms\Components\TextInput::make('reward_usd')
                        ->label('Reward')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->step(0.01)
                        ->minValue(0.01)
                        ->maxValue(999999.99)
                        ->default(1.00),

                    Forms\Components\Select::make('status')
                        ->options(TaskStatus::class)
                        ->required()
                        ->default(TaskStatus::Available)
                        ->native(false)
                        ->live(),

                    Forms\Components\Select::make('assigned_to_user_id')
                        ->label('Held by')
                        ->relationship('assignee', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText('Set automatically when a worker claims the task.'),

                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Hold expires')
                        ->seconds(false)
                        ->helperText('Cleared when the task returns to the queue.'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('title_en')
                    ->label('Task')
                    ->searchable(['title_en', 'title_ar'])
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Task $record) => $record->title_ar)
                    ->wrap()
                    ->limit(60),

                Tables\Columns\TextColumn::make('reward_usd')
                    ->label('Reward')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Held by')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Hold ends')
                    ->since()
                    ->placeholder('—')
                    // Amber once the hold has lapsed but the sweeper has not run.
                    ->color(fn (Task $record) => $record->lockHasExpired() ? 'warning' : 'gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('Uploads')
                    ->counts('submissions')
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Posted')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TaskStatus::class)
                    ->multiple(),

                Tables\Filters\Filter::make('lapsed')
                    ->label('Hold expired')
                    ->query(fn (Builder $query) => $query->lapsed())
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('release')
                    ->label('Return to queue')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('warning')
                    ->visible(fn (Task $record) => $record->status === TaskStatus::Assigned)
                    ->requiresConfirmation()
                    ->modalDescription('The worker loses the hold immediately. Anything they typed stays on their computer.')
                    ->action(fn (Task $record) => app(\App\Services\TaskService::class)->release($record)),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-m-archive-box')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => TaskStatus::Archived])),
                ]),
            ])
            ->emptyStateHeading('No tasks yet')
            ->emptyStateDescription('Upload a source PDF and set a reward to put the first one in the queue.')
            ->emptyStateIcon('heroicon-o-document-plus');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
