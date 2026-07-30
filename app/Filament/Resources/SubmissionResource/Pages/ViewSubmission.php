<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Enums\SubmissionStatus;
use App\Filament\Resources\SubmissionResource;
use App\Models\Submission;
use App\Services\SubmissionService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Submission $record */
        $record = $this->getRecord();

        return [
            Actions\Action::make('download')
                ->label('Download file')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('gray')
                ->visible(fn () => Storage::disk(config('sortifya.uploads.submissions_disk'))
                    ->exists($record->uploaded_excel_path))
                ->action(fn () => Storage::disk(config('sortifya.uploads.submissions_disk'))
                    ->download($record->uploaded_excel_path, $record->fileName())),

            Actions\Action::make('approve')
                ->label('Approve and pay')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(fn () => $record->status !== SubmissionStatus::Approved)
                ->requiresConfirmation()
                ->modalDescription(fn () => sprintf(
                    'Credits $%s to %s and retires the task.',
                    number_format((float) $record->task->reward_usd, 2),
                    $record->user?->name ?? 'the worker',
                ))
                ->action(function () use ($record) {
                    app(SubmissionService::class)->approve($record, auth()->user());

                    Notification::make()->success()->title('Approved')->send();

                    $this->redirect(SubmissionResource::getUrl('index'));
                }),

            Actions\Action::make('reject')
                ->label('Return for fixes')
                ->icon('heroicon-m-arrow-uturn-left')
                ->color('danger')
                ->visible(fn () => $record->status === SubmissionStatus::Pending)
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('What needs fixing')
                        ->required()
                        ->rows(3)
                        ->maxLength(1000)
                        ->helperText('The worker sees this word for word.'),

                    Forms\Components\Toggle::make('keep_with_worker')
                        ->label('Give it back to the same worker')
                        ->default(true),
                ])
                ->action(function (array $data) use ($record) {
                    app(SubmissionService::class)->reject(
                        $record,
                        auth()->user(),
                        $data['reason'],
                        (bool) ($data['keep_with_worker'] ?? true),
                    );

                    Notification::make()->warning()->title('Returned to the worker')->send();

                    $this->redirect(SubmissionResource::getUrl('index'));
                }),
        ];
    }
}
