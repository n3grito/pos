<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);
        app()->instance('cspNonce', $nonce);
        view()->share('cspNonce', $nonce);
        Vite::useCspNonce($nonce);

        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $csp = "default-src 'self'; "
             . "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval' 'strict-dynamic'; "
             . "style-src 'self' 'unsafe-inline'; "
             . "img-src 'self' data:; "
             . "font-src 'self'; "
             . "connect-src 'self' ws://localhost:* http://localhost:*; "
             . "form-action 'self'; "
             . "frame-ancestors 'none'; "
             . "base-uri 'self'; "
             . "object-src 'none'; "
             . "report-uri " . route('csp.report', [], false) . "; "
             . "report-to csp-endpoint";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
