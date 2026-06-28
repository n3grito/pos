<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->locale
            ?? session('locale')
            ?? config('app.locale');

        if ($locale !== session('locale')) {
            session(['locale' => $locale]);
        }

        if (in_array($locale, config('app.available_locales', ['es_MX']), true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
