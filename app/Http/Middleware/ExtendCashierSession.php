<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExtendCashierSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && $request->user()->hasRole('Cashier')) {
            $sessionLifetime = (int) config('session.lifetime');
            $extendedLifetime = max(480, $sessionLifetime);

            if ($request->hasSession()) {
                $request->session()->set('cashier_extended', true);
            }

            $response->headers->set(
                'Set-Cookie',
                session_name() . '=' . session_id() . '; '
                    . 'expires=' . gmdate('D, d M Y H:i:s', time() + $extendedLifetime * 60) . ' GMT; '
                    . 'path=' . (config('session.path') ?: '/') . '; '
                    . (config('session.secure') ? 'secure; ' : '')
                    . (config('session.http_only') ? 'httponly; ' : '')
                    . 'samesite=' . (config('session.same_site') ?: 'lax'),
                false
            );
        }

        return $response;
    }
}
