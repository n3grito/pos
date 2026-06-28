<?php

use App\Http\Middleware\ExtendCashierSession;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\XssProtection;
use App\Mail\SystemErrorMail;
use App\Models\ActivityLog;
use App\Models\MailSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\HttpException;

function sendErrorNotification(string $level, string $shortMessage, string $fullMessage, ?string $trace = null, ?Request $request = null): void
{
    try {
        if (!RateLimiter::attempt('error-notifications', 10, function () {}, 60)) {
            return;
        }

        $mailSetting = MailSetting::first();
        if (!$mailSetting || !$mailSetting->from_address) {
            return;
        }

        Config::set('mail.default', $mailSetting->mailer);
        Config::set('mail.mailers.smtp.host', $mailSetting->host);
        Config::set('mail.mailers.smtp.port', $mailSetting->port);
        Config::set('mail.mailers.smtp.username', $mailSetting->username);
        Config::set('mail.mailers.smtp.password', $mailSetting->encrypted_password ? Crypt::decryptString($mailSetting->encrypted_password) : '');
        Config::set('mail.mailers.smtp.encryption', $mailSetting->encryption === 'null' ? null : $mailSetting->encryption);
        Config::set('mail.from.address', $mailSetting->from_address);
        Config::set('mail.from.name', $mailSetting->from_name);

        $req = $request ?? request();

        Mail::send(new SystemErrorMail([
            'level' => $level,
            'short_message' => $shortMessage,
            'message' => $fullMessage,
            'trace' => $trace,
            'url' => $req?->fullUrl() ?? '',
            'method' => $req?->method() ?? '',
            'ip' => $req?->ip() ?? '',
            'user_agent' => $req?->userAgent() ?? '',
            'input' => $req?->except(['password', 'password_confirmation', '_token']) ?? [],
            'user' => $req?->user() ? ['id' => $req->user()->id, 'name' => $req->user()->name, 'email' => $req->user()->email] : null,
        ]));
    } catch (\Throwable $e) {
        // Silent fail: notification should not break the app
    }
}

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
            XssProtection::class,
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

        $exceptions->reportable(function (\Throwable $e) {
            $request = request();

            if ($e instanceof HttpException && $e->getStatusCode() === 404) {
                return;
            }

            $level = match (true) {
                $e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
                     $e instanceof \Illuminate\Auth\Access\AuthorizationException => 'CRITICAL',
                $e instanceof \Illuminate\Session\TokenMismatchException,
                     $e instanceof \Illuminate\Auth\AuthenticationException => 'WARNING',
                default => 'ERROR',
            };

            sendErrorNotification(
                $level,
                class_basename($e) . ': ' . $e->getMessage(),
                $e->getMessage(),
                $e->getTraceAsString(),
                $request
            );
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            $status = $e->getStatusCode();
            if (in_array($status, [401, 403, 419], true)) {
                ActivityLog::create([
                    'user_id' => $request->user()?->id,
                    'action' => match ($status) { 401 => 'unauthorized', 403 => 'forbidden', 419 => 'session_expired', default => 'http_error' },
                    'severity' => 'warning',
                    'notable' => $status === 403,
                    'description' => "Acceso {$status} a: {$request->fullUrl()}",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        });
    })->create();
