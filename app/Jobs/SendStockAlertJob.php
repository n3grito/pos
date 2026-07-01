<?php

namespace App\Jobs;

use App\Models\MailSetting;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendStockAlertJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        try {
            $mailSetting = MailSetting::first();
            if (!$mailSetting || !$mailSetting->from_address) {
                return;
            }

            $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
                ->where('min_stock', '>', 0)
                ->get();

            if ($lowStockProducts->isEmpty()) {
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

            $admins = User::role('Admin')->get();

            foreach ($admins as $admin) {
                Mail::raw(
                    'Productos con stock bajo:' . PHP_EOL . PHP_EOL .
                    $lowStockProducts->map(fn($p) => "- {$p->name}: {$p->stock} unidades")->implode(PHP_EOL) .
                    PHP_EOL . PHP_EOL . 'Por favor, revise el inventario.',
                    function ($message) use ($admin) {
                        $message->to($admin->email, $admin->name)
                            ->subject('Alerta de Stock Bajo - ' . config('app.name'));
                    }
                );
            }
        } catch (\Throwable $e) {
            Log::error('SendStockAlertJob failed: ' . $e->getMessage());
        }
    }
}
