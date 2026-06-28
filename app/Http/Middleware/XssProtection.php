<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssProtection
{
    protected array $patterns = [
        '/<script\b[^>]*>(.*?)<\/script>/is',
        '/javascript\s*:/i',
        '/on\w+\s*=\s*["\']?(?:[^"\'\s>]+)["\']?/i',
        '/<[^>]*\bon\w+\s*=[^>]*>/i',
        '/document\.(cookie|domain|write|location)/i',
        '/window\.(location|name|status)/i',
        '/alert\s*\(/i',
        '/eval\s*\(/i',
        '/prompt\s*\(/i',
        '/confirm\s*\(/i',
        '/fromCharCode/i',
        '/<embed\b/i',
        '/<object\b/i',
        '/<iframe\b/i',
        '/<link\b[^>]*href=["\'](?:javascript|data:text\/html)/i',
        '/data:\s*text\/html/i',
        '/vbscript\s*:/i',
        '/expression\s*\(/i',
        '/<svg\b[^>]*>\s*<script/i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        $inputs = $request->all();

        foreach ($inputs as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            foreach ($this->patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    ActivityLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'xss_attempt',
                        'severity' => 'critical',
                        'notable' => true,
                        'description' => 'Intento de XSS detectado en el campo: ' . $key,
                        'model_type' => null,
                        'model_id' => null,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);

                    $this->notifySupport($request, $key, $value);

                    if ($request->expectsJson()) {
                        return response()->json(['message' => 'Entrada no válida detectada.'], 422);
                    }

                    return redirect()->back()->withInput()->withErrors([
                        $key => 'El campo contiene contenido no permitido.',
                    ]);
                }
            }
        }

        return $next($request);
    }

    protected function notifySupport(Request $request, string $field, string $value): void
    {
        try {
            $mailSetting = \App\Models\MailSetting::first();
            if (!$mailSetting || !$mailSetting->from_address) {
                return;
            }

            $settingsController = app(\App\Http\Controllers\SettingsController::class);
            $settingsController->applyMailConfig();

            $supportEmail = 'soporte@tallerssh.cu';

            \Illuminate\Support\Facades\Mail::mailer('smtp')->send(
                new \App\Mail\SystemErrorMail([
                    'level' => 'CRITICAL',
                    'short_message' => 'Intento de XSS detectado',
                    'message' => 'Se detectó un intento de XSS en el campo "' . $field . '".',
                    'trace' => 'Patrón activado en: ' . htmlspecialchars(substr($value, 0, 500)),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'input' => [$field => '(bloqueado por XSS)'],
                ])
            );
        } catch (\Throwable $e) {
            // Silent fail: notification should not break the app
        }
    }
}
