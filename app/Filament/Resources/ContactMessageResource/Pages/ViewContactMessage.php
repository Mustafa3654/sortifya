<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    /** Opening a message is reading it; mark it read without a second click. */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        /** @var ContactMessage $message */
        $message = $this->getRecord();

        if ($message->isNew()) {
            $message->update(['status' => 'handled']);
        }
    }

    protected function getHeaderActions(): array
    {
        /** @var ContactMessage $record */
        $record = $this->getRecord();

        return [
            Actions\Action::make('reply')
                ->label('Reply by email')
                ->icon('heroicon-m-arrow-uturn-left')
                ->url(fn () => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: '.$record->subject))
                ->openUrlInNewTab(),

            Actions\Action::make('reopen')
                ->label('Mark unhandled')
                ->icon('heroicon-m-arrow-path')
                ->color('gray')
                ->action(function () use ($record) {
                    $record->update(['status' => 'new']);

                    Notification::make()->success()->title('Marked unhandled')->send();

                    $this->redirect(ContactMessageResource::getUrl('index'));
                }),
        ];
    }
}
