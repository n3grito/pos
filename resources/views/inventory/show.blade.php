<x-app-layout>
    <x-slot name="header">
    {{ __('Inventario') }}: {{ $product->name }}
</x-slot>

    <x-content-wrapper class="space-y-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Producto') }}</span>
                            <span class="block mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('SKU') }}</span>
                            <span class="block mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $product->sku }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Categoría') }}</span>
                            <span class="block mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $product->category->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Disponible para Venta') }}</span>
                            <span class="block mt-1">
                                @can('product.update')
                                <form method="POST" action="{{ route('inventory.toggle-availability', $product) }}" class="inline" x-data @submit.prevent="if(confirm('{{ $product->available_for_sale ? '¿Deshabilitar' : '¿Habilitar' }} {{ $product->name }} para la venta?')) $el.submit()">
                                    @csrf
                                    <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $product->available_for_sale ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 {{ $product->available_for_sale ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                    </button>
                                </form>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg {{ $product->available_for_sale ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400' }}">
                                        {{ $product->available_for_sale ? __('Sí') : __('No') }}
                                    </span>
                                @endcan
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Stock en Almacén') }}</span>
                            <div class="mt-1 space-y-1">
                                @forelse ($warehouseStocks as $ws)
                                    <span class="block text-sm {{ $ws->quantity > 0 ? 'text-gray-700 dark:text-gray-300' : 'text-red-500' }}">
                                        {{ $ws->warehouse->name }}: {{ $ws->quantity }}
                                    </span>
                                @empty
                                    <span class="block text-sm text-red-500">{{ __('Sin stock en almacenes') }}</span>
                                @endforelse
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Stock Disponible en Inventario') }}</span>
                            <span class="block mt-1 text-lg font-bold {{ $product->stock <= $product->min_stock ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">{{ $product->stock }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Stock Mínimo') }}</span>
                            <span class="block mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $product->min_stock }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Precio Costo') }}</span>
                            <span class="block mt-1 text-sm text-gray-700 dark:text-gray-300">{{ currency($product->cost_price) }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Precio Venta') }}</span>
                            <span class="block mt-1 text-sm text-gray-700 dark:text-gray-300">{{ currency($product->selling_price) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Movimientos de Inventario') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fecha') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tipo') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cantidad') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Referencia') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Usuario') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Notas') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($movements as $movement)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $typeLabels = [
                                                'in' => ['label' => 'Entrada', 'class' => 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400'],
                                                'out' => ['label' => 'Salida', 'class' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400'],
                                                'transfer' => ['label' => 'Transferencia', 'class' => 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400'],
                                                'adjustment' => ['label' => 'Ajuste', 'class' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400'],
                                                'warehouse_entry' => ['label' => 'Entrada Almacén', 'class' => 'bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-400'],
                                                'warehouse_exit' => ['label' => 'Salida Almacén', 'class' => 'bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-400'],
                                            ];
                                            $typeInfo = $typeLabels[$movement->type] ?? ['label' => ucfirst($movement->type), 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeInfo['class'] }}">{{ __($typeInfo['label']) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ in_array($movement->type, ['out', 'warehouse_exit']) ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                        {{ in_array($movement->type, ['out', 'warehouse_exit']) ? '-' : '+' }}{{ $movement->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->reference ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $movement->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay movimientos registrados') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($movements->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $movements->links() }}
                    </div>
                @endif
            </div>
    </x-content-wrapper>
</x-app-layout>
