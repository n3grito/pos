<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Sin conexión') }} — {{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f3f4f6; color: #374151; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; }
        .card { background: #fff; border-radius: 16px; padding: 48px 32px; text-align: center; max-width: 400px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .icon { width: 64px; height: 64px; margin: 0 auto 24px; background: #eef2ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        h1 { font-size: 20px; margin-bottom: 8px; }
        p { font-size: 14px; color: #6b7280; line-height: 1.5; }
        .btn { display: inline-block; margin-top: 24px; padding: 10px 24px; background: #4F46E5; color: #fff; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; border: none; cursor: pointer; }
        .btn:hover { background: #4338ca; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="32" height="32" fill="none" stroke="#4F46E5" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728m-2.829-2.829a5 5 0 000-7.07m-4.243 4.243a1 1 0 010-1.414"/>
            </svg>
        </div>
        <h1>{{ __('Sin conexión') }}</h1>
        <p>{{ __('No tienes conexión a Internet. Algunas funciones no están disponibles hasta que te reconectes.') }}</p>
        <p style="margin-top: 12px; font-size: 13px;">{{ __('Las ventas realizadas sin conexión se sincronizarán automáticamente.') }}</p>
        <button class="btn" onclick="window.location.reload()">{{ __('Reintentar') }}</button>
    </div>
</body>
</html>
