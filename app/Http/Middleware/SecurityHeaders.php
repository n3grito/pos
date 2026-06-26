<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);
        view()->share('cspNonce', $nonce);

        $csp = "default-src 'self'; "
             . "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval'; "
             . "style-src 'self' 'unsafe-inline'; "
             . "img-src 'self' data:; "
             . "font-src 'self'; "
             . "connect-src 'self'; "
             . "form-action 'self'; "
             . "frame-ancestors 'none'; "
             . "base-uri 'self'; "
             . "object-src 'none'";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
