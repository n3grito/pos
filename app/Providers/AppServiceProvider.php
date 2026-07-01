<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\GeneralSetting;
use App\Models\MailSetting;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
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

        View::composer('*', function ($view) {
            $nonce = app()->bound('cspNonce') ? app('cspNonce') : '';
            $view->with('cspNonce', $nonce);
        });

        Blade::directive('viteSafe', function ($expression) {
            return "<?php if (file_exists(public_path('build/manifest.json'))) { echo app('Illuminate\\Foundation\\Vite')->__invoke({$expression}); } ?>";
        });

        try {
            $settings = CacheService::settings();
            if ($mail = $settings['mail'] ?? null) {
                Config::set('mail.default', $mail['mailer'] ?? 'log');
                Config::set('mail.mailers.smtp.host', $mail['host'] ?? '');
                Config::set('mail.mailers.smtp.port', $mail['port'] ?? 587);
                Config::set('mail.mailers.smtp.username', $mail['username'] ?? '');
                Config::set('mail.mailers.smtp.password', ($mail['encrypted_password'] ?? false) ? Crypt::decryptString($mail['encrypted_password']) : '');
                Config::set('mail.mailers.smtp.encryption', ($mail['encryption'] ?? 'null') === 'null' ? null : ($mail['encryption'] ?? null));
                Config::set('mail.from.address', $mail['from_address'] ?? 'noreply@example.com');
                Config::set('mail.from.name', $mail['from_name'] ?? config('app.name'));
            }
        } catch (\Exception $e) {
            // Table may not exist yet (before migration)
        }

        try {
            $timezone = CacheService::settings()['timezone'] ?? 'America/Havana';
            if (is_string($timezone) && in_array($timezone, timezone_identifiers_list())) {
                Config::set('app.timezone', $timezone);
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // Table may not exist yet (before migration)
        }

        // Invalidate caches on model changes
        $invalidate = fn() => CacheService::clearDashboard();
        Product::created($invalidate);
        Product::updated($invalidate);
        Product::deleted($invalidate);
        Sale::created($invalidate);
        Sale::updated($invalidate);
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
