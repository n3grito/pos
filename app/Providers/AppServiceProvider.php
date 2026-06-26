<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\MailSetting;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
                'severity' => 'info',
                'notable' => false,
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
                    'severity' => 'info',
                    'notable' => false,
                    'description' => 'Cierre de sesión',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            ActivityLog::create([
                'action' => 'login_failed',
                'severity' => 'warning',
                'notable' => false,
                'description' => 'Intento fallido de inicio de sesión: ' . ($event->credentials['email'] ?? 'desconocido'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        $self = $this;

        Product::created(function ($model) use ($self) {
            $self->logSensitiveAction('product_created', 'critical', 'Producto creado: ' . $model->name, $model, auth()->user());
        });
        Product::updated(function ($model) use ($self) {
            $self->logSensitiveAction('product_updated', 'info', 'Producto actualizado: ' . $model->name, $model, auth()->user());
        });
        Product::deleted(function ($model) use ($self) {
            $self->logSensitiveAction('product_deleted', 'critical', 'Producto eliminado: ' . $model->name, $model, auth()->user());
        });

        Sale::created(function ($model) use ($self) {
            $self->logSensitiveAction('sale_created', 'critical', 'Venta #' . $model->id . ' creada por $' . number_format($model->total ?? 0, 2), $model, auth()->user());
        });
        Sale::updated(function ($model) use ($self) {
            $self->logSensitiveAction('sale_updated', 'warning', 'Venta #' . $model->id . ' actualizada', $model, auth()->user());
        });

        Purchase::created(function ($model) use ($self) {
            $self->logSensitiveAction('purchase_created', 'critical', 'Compra #' . $model->id . ' registrada', $model, auth()->user());
        });
        Purchase::updated(function ($model) use ($self) {
            $self->logSensitiveAction('purchase_updated', 'warning', 'Compra #' . $model->id . ' actualizada', $model, auth()->user());
        });

        User::created(function ($model) use ($self) {
            $self->logSensitiveAction('user_created', 'critical', 'Usuario creado: ' . $model->email, $model, auth()->user());
        });
        User::updated(function ($model) use ($self) {
            $self->logSensitiveAction('user_updated', 'warning', 'Usuario actualizado: ' . $model->email, $model, auth()->user());
        });
        User::deleted(function ($model) use ($self) {
            $self->logSensitiveAction('user_deleted', 'critical', 'Usuario eliminado: ' . $model->email, $model, auth()->user());
        });

        RateLimiter::for('login', function () {
            return Limit::perMinute(5)->by(request()->input('email') . '|' . request()->ip());
        });

        RateLimiter::for('global', function () {
            return Limit::perMinute(120)->by(request()->ip());
        });

        RateLimiter::for('sensitive', function () {
            return Limit::perMinute(10)->by(auth()->id() ?: request()->ip());
        });

        RateLimiter::for('exports', function () {
            return Limit::perMinute(3)->by(auth()->id() ?: request()->ip());
        });

        RateLimiter::for('api', function () {
            return Limit::perMinute(60)->by(request()->ip());
        });

        Blade::directive('viteSafe', function ($expression) {
            return "<?php if (file_exists(public_path('build/manifest.json'))) { echo app('Illuminate\\Foundation\\Vite')->__invoke({$expression}); } ?>";
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

    private function logSensitiveAction(string $action, string $severity, string $description, $model, $user = null): void
    {
        try {
            ActivityLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'severity' => $severity,
                'notable' => in_array($severity, ['critical', 'warning']),
                'description' => $description,
                'model_type' => get_class($model),
                'model_id' => $model->id ?? null,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silent fail - logging should not break the app
        }
    }
}
