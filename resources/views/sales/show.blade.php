<x-app-layout>
    <x-slot name="header">
    {{ __('Venta') }}: {{ $sale->invoice_number }}
</x-slot>

    <x-content-wrapper>
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Información de la Venta') }}</h3>
                        </div>
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('Imprimir Recibo') }}
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Factura') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $sale->invoice_number }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Cliente') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $sale->client_name ?? ($sale->client->name ?? __('Cliente General')) }}</p>
                            @if ($sale->client_nit)
                                <p class="text-xs text-gray-500 dark:text-gray-400">NIT: {{ $sale->client_nit }}</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Usuario') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $sale->user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Almacén') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $sale->warehouse->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-semibold">{{ currency($sale->total) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Método de Pago') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $sale->payment_method === 'cash' ? __('Efectivo') : ($sale->payment_method === 'card' ? __('Tarjeta') : ($sale->payment_method === 'transfer' ? __('Transferencia') : __('Crédito'))) }}
                            </p>
                        </div>
                        @if ($sale->payment_method === 'cash' && $sale->amount_paid !== null)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Recibido') }}</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ currency($sale->amount_paid) }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Cambio') }}</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ currency($sale->change) }}</p>
                            </div>
                        @endif
                        @if (in_array($sale->payment_method, ['card', 'transfer']) && $sale->payment_reference)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Código de Transacción') }}</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $sale->payment_reference }}</p>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estado') }}</h4>
                            <p class="mt-1 text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400' : '' }}
                                    {{ $sale->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400' : '' }}">
                                    {{ $sale->status === 'completed' ? __('Completada') : __('Cancelada') }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Fecha') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Detalle de Productos') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Producto / Servicio') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('SKU') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cantidad') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Precio Unitario') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($sale->details as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            @if ($item->service)
                                                {{ $item->service->name }}
                                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400">Srv</span>
                                            @else
                                                {{ $item->product->name ?? '-' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->product->sku ?? ($item->service ? '-' : '-') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($item->price) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($item->quantity * $item->price) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay productos en esta venta') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                @if ((float) $sale->tax > 0)
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Subtotal') }}</td>
                                    <td class="px-6 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ currency($sale->subtotal) }}</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('IVA') }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-100">{{ currency($sale->tax) }}</td>
                                </tr>
                                @endif
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Total') }}</td>
                                    <td class="px-6 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">{{ currency($sale->total) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Printable Receipt -->
            @php
                $receiptSettings = \App\Models\ReceiptSetting::firstOrNew([]);
            @endphp
            <div id="receipt" class="hidden">
                <div class="bg-white p-8 max-w-sm mx-auto" style="font-family: monospace; font-size: 12px;">
                    <div class="text-center mb-2">
                        <h2 class="text-lg font-bold">{{ $receiptSettings->store_name ?? config('app.name') }}</h2>
                        @if ($receiptSettings->address)
                            <p class="text-xs text-gray-600">{{ $receiptSettings->address }}</p>
                        @endif
                        @if ($receiptSettings->phone)
                            <p class="text-xs text-gray-600">Tel: {{ $receiptSettings->phone }}</p>
                        @endif
                        <p class="mt-1 text-xs">{{ __('Recibo de Venta') }}</p>
                    </div>
                    <hr class="border-t border-dashed border-gray-400 mb-2">
                    <div class="mb-2 text-xs">
                        <p><strong>{{ __('Factura') }}:</strong> {{ $sale->invoice_number }}</p>
                        <p><strong>{{ __('Fecha') }}:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>{{ __('Cliente') }}:</strong> {{ $sale->client_name ?? ($sale->client->name ?? __('Cliente General')) }}</p>
                        @if ($receiptSettings->show_nit && $sale->client_nit)
                            <p><strong>NIT:</strong> {{ $sale->client_nit }}</p>
                        @endif
                        @if ($receiptSettings->show_seller)
                            <p><strong>{{ __('Vendedor') }}:</strong> {{ $sale->user->name ?? '-' }}</p>
                        @endif
                    </div>
                    <hr class="border-t border-dashed border-gray-400 mb-2">
                    <table class="w-full mb-2 text-xs">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('Producto') }}</th>
                                <th class="text-center">{{ __('Cant') }}</th>
                                <th class="text-right">{{ __('Precio') }}</th>
                                <th class="text-right">{{ __('Subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->details as $item)
                                <tr>
                                    <td>
                                        @if ($item->service)
                                            {{ $item->service->name }}
                                        @else
                                            {{ $item->product->name ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->quantity * $item->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr class="border-t border-dashed border-gray-400 mb-1">
                    <div class="text-right">
                        <p class="font-bold text-sm">{{ __('Total') }}: {{ number_format($sale->total, 2) }}</p>
                        @if ($sale->payment_method === 'cash' && $sale->amount_paid !== null)
                            <p class="text-xs">{{ __('Recibido') }}: {{ number_format($sale->amount_paid, 2) }}</p>
                            <p class="text-xs">{{ __('Cambio') }}: {{ number_format($sale->change, 2) }}</p>
                        @endif
                        @if (in_array($sale->payment_method, ['card', 'transfer']) && $sale->payment_reference)
                            <p class="text-xs">{{ __('Código') }}: {{ $sale->payment_reference }}</p>
                        @endif
                    </div>
                    <hr class="border-t border-dashed border-gray-400 my-2">
                    <div class="text-center text-xs text-gray-600">
                        <p>{{ __('Método de Pago') }}: {{ $sale->payment_method === 'cash' ? __('Efectivo') : ($sale->payment_method === 'card' ? __('Tarjeta') : ($sale->payment_method === 'transfer' ? __('Transferencia') : __('Crédito'))) }}</p>
                        <p class="mt-1">{{ $receiptSettings->footer_text ?? __('¡Gracias por su compra!') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex space-x-3">
                <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Volver') }}
                </a>
            </div>
    </x-content-wrapper>

    @push('styles')
    <style>
        @@media print {
            body * { visibility: hidden; }
            #receipt, #receipt * { visibility: visible; }
            #receipt { position: absolute; left: 0; top: 0; display: block !important; }
            .hidden { display: block !important; }
        }
    </style>
    @endpush
</x-app-layout>
