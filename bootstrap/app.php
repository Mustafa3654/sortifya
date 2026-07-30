<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Language is resolved before anything renders, on every web request.
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'active' => EnsureUserIsActive::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Holds are 45 minutes; sweeping every 5 keeps the queue honest
        // without hammering the table.
        $schedule->command('tasks:release-expired')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
