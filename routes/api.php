<?php

use App\Http\Controllers\Api\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Telegram
|--------------------------------------------------------------------------
|
| Stateless and CSRF-exempt by virtue of being an API route. Authenticity is
| proven by the X-Telegram-Bot-Api-Secret-Token header, checked inside the
| controller. Throttled so a leaked URL cannot be hammered.
|
*/

Route::post('/telegram/webhook', TelegramWebhookController::class)
    ->middleware('throttle:60,1')
    ->name('telegram.webhook');
