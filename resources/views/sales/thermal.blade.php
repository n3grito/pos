<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Recibo') }} - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
            size: 58mm auto;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', 'Lucida Console', monospace;
            font-size: 10px;
            width: 58mm;
            padding: 2mm 3mm;
            color: #000;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .left { text-align: left; }
        .bold { font-weight: bold; }
        .header { font-size: 12px; font-weight: bold; margin-bottom: 2mm; }
        .subheader { font-size: 9px; margin-bottom: 2mm; }
        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 1.5mm 0;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.5mm 0; vertical-align: top; }
        th { font-size: 9px; border-bottom: 1px dashed #000; }
        td { font-size: 10px; }
        .qty { text-align: center; width: 8mm; }
        .price { text-align: right; width: 14mm; }
        .subtotal { text-align: right; width: 14mm; }
        .info-line { font-size: 9px; margin: 0.5mm 0; }
        .totals { margin-top: 1mm; }
        .totals td { padding: 0.3mm 0; }
        .footer { font-size: 9px; margin-top: 2mm; text-align: center; }
        .barcode-placeholder {
            font-family: 'Courier New', monospace;
            font-size: 8px;
            letter-spacing: 1px;
            margin: 1.5mm 0;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <div class="header">{{ config('app.name') }}</div>
        <div class="subheader">{{ __('Recibo de Venta') }}</div>
        <hr>
        <div class="info-line"><span class="bold">{{ __('Factura') }}:</span> {{ $sale->invoice_number }}</div>
        <div class="info-line"><span class="bold">{{ __('Fecha') }}:</span> {{ $sale->created_at->format('d/m/Y H:i') }}</div>
        <div class="info-line"><span class="bold">{{ __('Cajero') }}:</span> {{ $sale->user->name ?? '-' }}</div>
        <div class="info-line"><span class="bold">{{ __('Cliente') }}:</span> {{ $sale->client_name ?? ($sale->client->name ?? __('Cliente General')) }}</div>
        @if ($sale->client_nit)
            <div class="info-line"><span class="bold">NIT:</span> {{ $sale->client_nit }}</div>
        @endif
        <hr>

        <table>
            <thead>
                <tr>
                    <th class="left">{{ __('Producto') }}</th>
                    <th class="qty">{{ __('Cant') }}</th>
                    <th class="price">{{ __('Precio') }}</th>
                    <th class="subtotal">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->details as $item)
                    <tr>
                        <td>{{ Str::limit($item->service ? $item->service->name : ($item->product->name ?? '-'), 18) }}</td>
                        <td class="qty">{{ $item->quantity }}</td>
                        <td class="price">{{ currency($item->price) }}</td>
                        <td class="subtotal">{{ currency($item->quantity * $item->price) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr>
        <table class="totals">
            <tr>
                <td class="left bold">{{ __('Subtotal') }}</td>
                <td class="right">{{ currency($sale->subtotal) }}</td>
            </tr>
            @if ((float) $sale->tax > 0)
                <tr>
                    <td class="left bold">{{ __('IVA') }}</td>
                    <td class="right">{{ currency($sale->tax) }}</td>
                </tr>
            @endif
            <tr>
                <td class="left bold" style="font-size: 12px;">{{ __('TOTAL') }}</td>
                <td class="right bold" style="font-size: 12px;">{{ currency($sale->total) }}</td>
            </tr>
            @if ($sale->payment_method === 'cash' && $sale->amount_paid !== null)
                <tr>
                    <td class="left">{{ __('Recibido') }}</td>
                    <td class="right">{{ currency($sale->amount_paid) }}</td>
                </tr>
                <tr>
                    <td class="left">{{ __('Cambio') }}</td>
                    <td class="right">{{ currency($sale->change) }}</td>
                </tr>
            @endif
        </table>

        <hr>
        <div class="info-line"><span class="bold">{{ __('Método de Pago') }}:</span>
            {{ $sale->payment_method === 'cash' ? __('Efectivo') : ($sale->payment_method === 'card' ? __('Tarjeta') : ($sale->payment_method === 'transfer' ? __('Transferencia') : __('Crédito'))) }}
        </div>
        @if (in_array($sale->payment_method, ['card', 'transfer']) && $sale->payment_reference)
            <div class="info-line"><span class="bold">{{ __('Código') }}:</span> {{ $sale->payment_reference }}</div>
        @endif

        <hr>
        <div class="footer">
            <div class="barcode-placeholder">{{ $sale->invoice_number }}</div>
            <div>{{ __('¡Gracias por su compra!') }}</div>
            <div style="font-size: 8px; margin-top: 1mm;">{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>

    <script>
        window.onafterprint = function() { window.close(); };
    </script>
</body>
</html>
