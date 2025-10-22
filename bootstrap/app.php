<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\PublicAuthMiddleware;
use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetTimezone;
use App\Http\Middleware\TrackVisits;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        'public.auth' => PublicAuthMiddleware::class,
        'admin.auth' => AdminAuthMiddleware::class,
        'setTimezone' => SetTimezone::class,
        'role' => RoleMiddleware::class,
        'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
        'track.visits' => TrackVisits::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
->withSchedule(function (Schedule $schedule) {
        // Run chat cleanup daily at 2:00 AM
        $schedule->command('chat:cleanup')
                 ->monthlyOn(31, '00:00')
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->onSuccess(function () {
                     \Log::info('Daily chat cleanup completed successfully');
                 })
                 ->onFailure(function () {
                     \Log::error('Daily chat cleanup failed');
                 });
    })->create();
