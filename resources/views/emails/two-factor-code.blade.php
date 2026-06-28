<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><style>
body{font-family:sans-serif;line-height:1.6;color:#1f2937;max-width:480px;margin:0 auto;padding:20px;text-align:center}
.logo{font-size:24px;font-weight:700;color:#1f2937;margin-bottom:8px}
.code{font-size:42px;font-weight:700;letter-spacing:8px;color:#2563eb;background:#f3f4f6;padding:20px;border-radius:12px;margin:24px 0;font-family:monospace}
.note{font-size:13px;color:#6b7280;margin-top:20px;line-height:1.5}
.footer{font-size:12px;color:#9ca3af;margin-top:28px;border-top:1px solid #e5e7eb;padding-top:16px}
</style></head>
<body>
<div class="logo">{{ config('app.name') }}</div>
<p>Hola <strong>{{ $userName }}</strong>,</p>
<p>Ingresa el siguiente código para completar tu inicio de sesión:</p>
<div class="code">{{ $code }}</div>
<p class="note">Este código expira en <strong>10 minutos</strong>.<br>
Si no solicitaste este código, ignora este mensaje.</p>
<div class="footer">{{ config('app.name') }} · {{ now()->toDateTimeString() }}</div>
</body>
</html>
