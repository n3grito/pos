<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorCodeMail;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:6,1')->only(['sendCode', 'verifyCode']);
    }

    public function showForm()
    {
        if (!GeneralSetting::get('2fa_enabled', false)) {
            return redirect()->route('dashboard');
        }

        if (!auth()->user()->two_factor_enabled) {
            return redirect()->route('dashboard');
        }

        if (session('two_factor_verified')) {
            return redirect()->intended(route('dashboard'));
        }

        if (!session('two_factor_code_sent')) {
            $this->generateAndSendCode(auth()->user());
            session(['two_factor_code_sent' => true]);
            session()->flash('status', 'Código enviado a tu correo electrónico.');
        }

        return view('auth.two-factor');
    }

    public function sendCode()
    {
        $user = auth()->user();
        $key = '2fa-send:' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['code' => "Intenta de nuevo en {$seconds} segundos."]);
        }
        RateLimiter::hit($key, 60);

        $this->generateAndSendCode($user);

        session(['two_factor_code_sent' => true]);

        return back()->with('status', 'Código enviado a tu correo electrónico.');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = auth()->user();

        if (!$user->two_factor_code || !$user->two_factor_expires_at) {
            return back()->withErrors(['code' => 'No hay un código pendiente. Solicita uno nuevo.']);
        }

        if (now()->gt($user->two_factor_expires_at)) {
            $this->clearCode($user);
            return back()->withErrors(['code' => 'El código ha expirado. Solicita uno nuevo.']);
        }

        if (!password_verify($request->code, $user->two_factor_code)) {
            return back()->withErrors(['code' => 'Código incorrecto.']);
        }

        $this->clearCode($user);
        session(['two_factor_verified' => true]);
        session(['two_factor_verified_at' => time()]);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function enable()
    {
        if (!GeneralSetting::get('2fa_enabled', false)) {
            return redirect()->route('dashboard');
        }

        $user = auth()->user();
        $user->forceFill(['two_factor_enabled' => true])->save();

        session()->forget(['two_factor_verified', 'two_factor_verified_at', 'two_factor_code_sent']);

        $this->generateAndSendCode($user);
        session(['two_factor_code_sent' => true]);

        return redirect()->route('two-factor.show');
    }

    public function disable()
    {
        $user = auth()->user();
        $user->forceFill([
            'two_factor_enabled' => false,
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        session()->forget(['two_factor_verified', 'two_factor_verified_at']);

        toast('Autenticación de dos factores desactivada.', 'success');
        return redirect()->route('profile.edit');
    }

    protected function generateAndSendCode($user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'two_factor_code' => bcrypt($code),
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        $this->sendMail($user, $code);
    }

    protected function clearCode($user): void
    {
        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();
    }

    protected function sendMail($user, string $code): void
    {
        try {
            $mailSetting = \App\Models\MailSetting::first();
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

            Mail::to($user->email)->send(new TwoFactorCodeMail($code, $user->name));
        } catch (\Throwable $e) {
            // Silent fail
        }
    }
}
