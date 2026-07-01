<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Productos con Bajo Stock') }}</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        h1 { font-size: 14pt; color: #dc2626; text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; font-size: 8pt; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #dc2626; color: white; padding: 5px 4px; font-size: 7.5pt; text-align: left; text-transform: uppercase; }
        td { padding: 4px; border-bottom: 1px solid #e5e7eb; font-size: 8pt; }
        .text-right { text-align: right; }
        .critical td { font-weight: bold; color: #dc2626; }
        .footer { position: fixed; bottom: 10px; width: 100%; text-align: center; font-size: 7pt; color: #999; }
    </style>
</head>
<body>
    <h1>{{ __('Productos con Bajo Stock') }}</h1>
    <p class="subtitle">{{ now()->format('d/m/Y H:i') }} | {{ $products->count() }} {{ __('productos críticos') }}</p>

    <table>
        <thead>
            <tr>
                <th>{{ __('Producto') }}</th>
                <th>{{ __('SKU') }}</th>
                <th>{{ __('Categoría') }}</th>
                <th class="text-right">{{ __('Stock') }}</th>
                <th class="text-right">{{ __('Mínimo') }}</th>
                <th class="text-right">{{ __('Faltan') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
            <tr class="{{ $p->stock <= 0 ? 'critical' : '' }}">
                <td>{{ $p->name }}</td>
                <td>{{ $p->sku }}</td>
                <td>{{ $p->category?->name ?? '-' }}</td>
                <td class="text-right">{{ $p->stock }}</td>
                <td class="text-right">{{ $p->min_stock }}</td>
                <td class="text-right">{{ max(0, $p->min_stock - $p->stock) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">{{ __('Generado el') }} {{ now()->format('d/m/Y H:i') }} | {{ config('app.name') }}</div>
</body>
</html>
