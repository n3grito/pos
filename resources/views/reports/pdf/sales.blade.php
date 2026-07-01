<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Reporte de Ventas') }}</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        h1 { font-size: 14pt; color: #4f46e5; text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; font-size: 8pt; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4f46e5; color: white; padding: 5px 4px; font-size: 7.5pt; text-align: left; text-transform: uppercase; }
        td { padding: 4px; border-bottom: 1px solid #e5e7eb; font-size: 8pt; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-bottom: 10px; }
        .summary table th { background: #f3f4f6; color: #333; font-size: 8pt; }
        .total-row td { font-weight: bold; border-top: 2px solid #4f46e5; }
        .footer { position: fixed; bottom: 10px; width: 100%; text-align: center; font-size: 7pt; color: #999; }
    </style>
</head>
<body>
    <h1>{{ __('Reporte de Ventas') }}</h1>
    <p class="subtitle">{{ now()->format('d/m/Y H:i') }}</p>

    <div class="summary">
        <table>
            <tr>
                <th>{{ __('Ventas') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Impuesto') }}</th>
                <th>{{ __('Promedio') }}</th>
            </tr>
            <tr>
                <td class="text-center">{{ $totals->total_sales }}</td>
                <td class="text-center">${{ number_format($totals->total_revenue, 2) }}</td>
                <td class="text-center">${{ number_format($totals->total_tax, 2) }}</td>
                <td class="text-center">${{ number_format($totals->average, 2) }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('Factura') }}</th>
                <th>{{ __('Cliente') }}</th>
                <th class="text-right">{{ __('Total') }}</th>
                <th>{{ __('Método') }}</th>
                <th>{{ __('Fecha') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr>
                <td>{{ $sale->invoice_number }}</td>
                <td>{{ $sale->client?->name ?? $sale->client_name ?? 'General' }}</td>
                <td class="text-right">${{ number_format($sale->total, 2) }}</td>
                <td>{{ $sale->payment_method }}</td>
                <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">{{ __('Generado el') }} {{ now()->format('d/m/Y H:i') }} | {{ config('app.name') }}</div>
</body>
</html>
