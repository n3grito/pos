<?php

namespace App\Http\Middleware;

use App\Models\GeneralSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class SetTimezone
{
    public function handle(Request $request, Closure $next): Response
    {
        $timezone = GeneralSetting::get('timezone', 'America/Havana');
        $timezone = is_string($timezone) ? $timezone : 'America/Havana';

        Config::set('app.timezone', $timezone);
        date_default_timezone_set($timezone);

        return $next($request);
    }
}
