<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user) {
            return $user->hasRole('Admin') ? true : null;
        });

        Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            ActivityLog::create([
                'user_id' => $event->user->id,
                'action' => 'login',
                'description' => 'Inicio de sesión desde ' . request()->ip(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if ($event->user) {
                ActivityLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'logout',
                    'description' => 'Cierre de sesión',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            ActivityLog::create([
                'action' => 'login_failed',
                'description' => 'Intento fallido de inicio de sesión: ' . ($event->credentials['email'] ?? 'desconocido'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        try {
            $mailSetting = MailSetting::first();
            if ($mailSetting) {
                Config::set('mail.default', $mailSetting->mailer);
                Config::set('mail.mailers.smtp.host', $mailSetting->host);
                Config::set('mail.mailers.smtp.port', $mailSetting->port);
                Config::set('mail.mailers.smtp.username', $mailSetting->username);
                Config::set('mail.mailers.smtp.password', $mailSetting->encrypted_password ? Crypt::decryptString($mailSetting->encrypted_password) : '');
                Config::set('mail.mailers.smtp.encryption', $mailSetting->encryption === 'null' ? null : $mailSetting->encryption);
                Config::set('mail.from.address', $mailSetting->from_address);
                Config::set('mail.from.name', $mailSetting->from_name);
            }
        } catch (\Exception $e) {
            // Table may not exist yet (before migration)
        }
    }
}
