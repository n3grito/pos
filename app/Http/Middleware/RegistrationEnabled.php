<?php

namespace App\Http\Middleware;

use App\Models\GeneralSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (GeneralSetting::get('registration_enabled', '1') !== '1') {
            abort(404);
        }

        return $next($request);
    }
}
