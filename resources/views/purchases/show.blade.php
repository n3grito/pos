<x-app-layout>
    <x-slot name="header">
    {{ __('Compra') }}: {{ $purchase->invoice_number }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Factura') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->invoice_number }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Proveedor') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->supplier->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Usuario') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->user->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Almacén') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->warehouse->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-semibold">{{ currency($purchase->total) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estado') }}</h4>
                            <p class="mt-1 text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400' : '' }}
                                    {{ $purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-400' : '' }}
                                    {{ $purchase->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400' : '' }}">
                                    {{ $purchase->status === 'completed' ? __('Completada') : ($purchase->status === 'pending' ? __('Pendiente') : __('Cancelada')) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Fecha') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->date ? \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') : $purchase->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Creado') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->created_at->format('d/m/Y H:i') }}</p>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Producto') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('SKU') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cantidad') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Costo Unitario') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($purchase->details as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->product->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->product->sku ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($item->cost_price) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($item->quantity * $item->cost_price) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay productos en esta compra') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                @if ((float) ($purchase->tax ?? 0) > 0)
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Subtotal') }}</td>
                                    <td class="px-6 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ currency($purchase->subtotal) }}</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('IVA') }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-100">{{ currency($purchase->tax) }}</td>
                                </tr>
                                @endif
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Total') }}</td>
                                    <td class="px-6 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">{{ currency($purchase->total) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex space-x-3">
                @if ($purchase->status !== 'cancelled')
                    <a href="{{ route('purchases.edit', $purchase) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                        {{ __('Editar Estado') }}
                    </a>
                @endif
                <a href="{{ route('purchases.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Volver') }}
                </a>
    </x-content-wrapper>
</x-app-layout>