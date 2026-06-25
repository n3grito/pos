<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\MailSetting;
use App\Models\ReceiptSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:setting.manage');
    }

    protected function applyMailConfig(): void
    {
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
    }

    public function mail()
    {
        $mailSetting = MailSetting::first();

        $mailConfig = [
            'mailer' => $mailSetting?->mailer ?? env('MAIL_MAILER', Config::get('mail.default')),
            'host' => $mailSetting?->host ?? env('MAIL_HOST', Config::get('mail.mailers.smtp.host')),
            'port' => $mailSetting?->port ?? env('MAIL_PORT', Config::get('mail.mailers.smtp.port')),
            'username' => $mailSetting?->username ?? env('MAIL_USERNAME', Config::get('mail.mailers.smtp.username')),
            'password' => $mailSetting?->encrypted_password ? '********' : '',
            'encryption' => $mailSetting?->encryption ?? env('MAIL_ENCRYPTION', ''),
            'from_address' => $mailSetting?->from_address ?? env('MAIL_FROM_ADDRESS', Config::get('mail.from.address')),
            'from_name' => $mailSetting?->from_name ?? env('MAIL_FROM_NAME', Config::get('mail.from.name')),
        ];

        return view('settings.mail', compact('mailConfig'));
    }

    public function updateMail(Request $request)
    {
        $validated = $request->validate([
            'mailer' => 'required|string|in:smtp,sendmail,mailgun,ses,postmark,log,array',
            'host' => 'required|string',
            'port' => 'required|numeric',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'encryption' => 'nullable|string|in:tls,ssl,null',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
        ]);

        $data = [
            'mailer' => $validated['mailer'],
            'host' => $validated['host'],
            'port' => $validated['port'],
            'username' => $validated['username'] ?? '',
            'encryption' => $validated['encryption'] === 'null' ? null : $validated['encryption'],
            'from_address' => $validated['from_address'],
            'from_name' => $validated['from_name'],
        ];

        if (!empty($validated['password']) && $validated['password'] !== '********') {
            $data['encrypted_password'] = Crypt::encryptString($validated['password']);
        }

        MailSetting::updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'Configuración de correo guardada correctamente.');
    }

    public function receipt()
    {
        $receipt = ReceiptSetting::firstOrNew();

        return view('settings.receipt', compact('receipt'));
    }

    public function updateReceipt(Request $request)
    {
        $validated = $request->validate([
            'show_seller' => 'boolean',
            'show_nit' => 'boolean',
        ]);

        $validated['show_seller'] = $request->boolean('show_seller');
        $validated['show_nit'] = $request->boolean('show_nit');

        ReceiptSetting::updateOrCreate(['id' => 1], $validated);

        return back()->with('success', 'Configuración de recibo guardada correctamente.');
    }

    public function general()
    {
        $registrationEnabled = GeneralSetting::get('registration_enabled', '1') === '1';
        $receipt = ReceiptSetting::firstOrNew([]);

        return view('settings.general', compact('registrationEnabled', 'receipt'));
    }

    public function updateGeneral(Request $request)
    {
        GeneralSetting::set('registration_enabled', $request->boolean('registration_enabled') ? '1' : '0');

        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'store_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'footer_text' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('logo')) {
            $request->validate(['logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048']);
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        if ($request->boolean('remove_logo')) {
            $validated['logo_path'] = null;
        }

        ReceiptSetting::updateOrCreate(['id' => 1], $validated);

        return back()->with('success', 'Configuración general guardada correctamente.');
    }

    public function testMail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $this->applyMailConfig();

        $email = $request->input('email');

        try {
            Mail::raw('Este es un correo de prueba del sistema POS. Si recibió este mensaje, la configuración de correo funciona correctamente.', function ($message) use ($email) {
                $message->to($email)->subject('Prueba de Configuración de Correo - POS System');
            });

            return back()->with('success', 'Correo de prueba enviado exitosamente a ' . $email . '.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar el correo de prueba: ' . $e->getMessage());
        }
    }
}
