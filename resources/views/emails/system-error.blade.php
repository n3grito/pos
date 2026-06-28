<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><style>
body{font-family:sans-serif;line-height:1.6;color:#1f2937;max-width:640px;margin:0 auto;padding:20px}
.header{background:#dc2626;color:#fff;padding:12px 20px;border-radius:8px 8px 0 0}
.header h1{margin:0;font-size:18px}
.body{padding:20px;border:1px solid #e5e7eb;border-top:0;border-radius:0 0 8px 8px}
.section{margin-bottom:16px}
.section h2{font-size:14px;text-transform:uppercase;color:#6b7280;margin:0 0 6px}
.section pre{background:#f3f4f6;padding:10px;border-radius:6px;font-size:12px;overflow-x:auto;white-space:pre-wrap;word-break:break-all}
.section code{background:#f3f4f6;padding:1px 4px;border-radius:3px;font-size:12px}
table{width:100%;border-collapse:collapse;font-size:13px}
td{padding:4px 8px;border-bottom:1px solid #e5e7eb;vertical-align:top}
td:first-child{font-weight:600;white-space:nowrap;color:#6b7280;width:100px}
.footer{text-align:center;font-size:11px;color:#9ca3af;margin-top:16px}
.badge{display:inline-block;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;text-transform:uppercase}
.badge-error{background:#fef2f2;color:#dc2626}
.badge-warning{background:#fffbeb;color:#d97706}
.badge-critical{background:#fef2f2;color:#991b1b}
</style></head>
<body>
<div class="header"><h1>{{ config('app.name') }} — {{ $level }}</h1></div>
<div class="body">
    <p><span class="badge badge-{{ $levelClass }}">{{ $level }}</span> &nbsp;{{ $shortMessage }}</p>

    <div class="section">
        <h2>Mensaje</h2>
        <pre>{{ $fullMessage }}</pre>
    </div>

    @if ($stackTrace)
    <div class="section">
        <h2>Stack Trace</h2>
        <pre>{{ $stackTrace }}</pre>
    </div>
    @endif

    <div class="section">
        <h2>Request</h2>
        <table>
            <tr><td>URL</td><td>{{ $url }}</td></tr>
            <tr><td>Método</td><td>{{ $method }}</td></tr>
            <tr><td>IP</td><td>{{ $ip }}</td></tr>
            <tr><td>User-Agent</td><td><code>{{ $userAgent }}</code></td></tr>
            <tr><td>Fecha/Hora</td><td>{{ $timestamp }}</td></tr>
        </table>
    </div>

    @if ($user)
    <div class="section">
        <h2>Usuario</h2>
        <table>
            <tr><td>ID</td><td>{{ $user['id'] }}</td></tr>
            <tr><td>Nombre</td><td>{{ $user['name'] }}</td></tr>
            <tr><td>Email</td><td>{{ $user['email'] }}</td></tr>
        </table>
    </div>
    @endif

    @if (!empty($input))
    <div class="section">
        <h2>Input</h2>
        <pre>{{ json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
    @endif
</div>
<div class="footer">{{ config('app.name') }} · {{ $timestamp }}</div>
</body>
</html>
