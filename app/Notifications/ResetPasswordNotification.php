<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

/**
 * Password reset mail, sent in the language the request was made in.
 *
 * Laravel's built-in notification always renders in the app locale, which is
 * whatever the queue worker happens to have. The locale is captured at the
 * moment the worker pressed the button and replayed here instead.
 */
class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        #[\SensitiveParameter] public string $token,
        public string $locale = 'en',
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return Lang::withLocale($this->locale, fn () => (new MailMessage)
            ->subject(__('sortifya.auth.reset_subject'))
            ->view('mail.password-reset', [
                'url' => $url,
                'name' => $notifiable->name,
                'minutes' => $minutes,
                'locale' => $this->locale,
                'dir' => config("sortifya.locales.{$this->locale}.dir", 'ltr'),
            ]));
    }
}
