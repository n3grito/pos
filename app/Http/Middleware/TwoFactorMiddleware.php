<?php

namespace App\Http\Middleware;

use App\Models\GeneralSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    protected array $except = [
        'two-factor.show',
        'two-factor.send',
        'two-factor.verify',
        'two-factor.enable',
        'two-factor.disable',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && GeneralSetting::get('2fa_enabled', false)) {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            if ($user->two_factor_enabled) {
                $routeName = $request->route()?->getName();

                if (!in_array($routeName, $this->except, true)) {
                    if ($request->session()->has('two_factor_verified')) {
                        $verifiedAt = $request->session()->get('two_factor_verified_at', 0);
                        if (time() - $verifiedAt < 3600) {
                            return $next($request);
                        }
                        $request->session()->forget(['two_factor_verified', 'two_factor_verified_at']);
                    }

                    if ($request->isMethod('GET')) {
                        return redirect()->route('two-factor.show');
                    }

                    abort(403, 'Debes verificar tu identidad con el código de dos factores.');
                }
            }
        }

        return $next($request);
    }
}
