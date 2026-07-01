<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Productos Más Vendidos') }}</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        h1 { font-size: 14pt; color: #4f46e5; text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; font-size: 8pt; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 5px 4px; font-size: 7.5pt; text-align: left; text-transform: uppercase; }
        td { padding: 4px; border-bottom: 1px solid #e5e7eb; font-size: 8pt; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: 10px; width: 100%; text-align: center; font-size: 7pt; color: #999; }
    </style>
</head>
<body>
    <h1>{{ __('Productos Más Vendidos') }}</h1>
    <p class="subtitle">{{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Producto') }}</th>
                <th>{{ __('SKU') }}</th>
                <th>{{ __('Categoría') }}</th>
                <th class="text-right">{{ __('Cantidad') }}</th>
                <th class="text-right">{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($topProducts as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->product?->name ?? '-' }}</td>
                <td>{{ $item->product?->sku ?? '-' }}</td>
                <td>{{ $item->product?->category?->name ?? '-' }}</td>
                <td class="text-right">{{ $item->total_quantity }}</td>
                <td class="text-right">${{ number_format($item->total_revenue, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">{{ __('Generado el') }} {{ now()->format('d/m/Y H:i') }} | {{ config('app.name') }}</div>
</body>
</html>
