<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustChangePasswordMiddleware
{
    protected array $except = [
        'password.change.form',
        'password.change.update',
        'two-factor.show',
        'two-factor.send',
        'two-factor.verify',
        'two-factor.enable',
        'two-factor.disable',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!auth()->check()) {
            return $response;
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->must_change_password) {
            return $response;
        }

        $routeName = $request->route()?->getName();

        if (in_array($routeName, $this->except, true)) {
            return $response;
        }

        if ($request->isMethod('GET')) {
            return redirect()->route('password.change.form');
        }

        abort(403, 'Debes cambiar tu contraseña antes de continuar.');
    }
}
