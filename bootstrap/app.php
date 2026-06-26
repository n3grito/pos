<?php

use App\Http\Middleware\ExtendCashierSession;
use App\Http\Middleware\SecurityHeaders;
use App\Models\ActivityLog;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'registration.enabled' => \App\Http\Middleware\RegistrationEnabled::class,
            'locale' => \App\Http\Middleware\SetLocale::class,
            'cashier.session' => ExtendCashierSession::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            SecurityHeaders::class,
            ExtendCashierSession::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->call(function () {
            ActivityLog::where('created_at', '<', now()->subDays(90))->delete();
        })->daily()->description('Limpiar activity_logs mayores a 90 días');

        $schedule->command('model:prune', [
            '--model' => [\App\Models\ActivityLog::class],
        ])->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
