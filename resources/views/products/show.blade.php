<x-app-layout>
    <x-slot name="header">
    {{ __('Producto') }}: {{ $product->name }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nombre') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('SKU') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->sku }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Código de Barras') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->barcode ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Categoría') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->category->name ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Unidad') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->unit->name ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Sucursal') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->branch->name ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estado') }}</h4>
                            <p class="mt-1 text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200' }}">
                                    {{ $product->is_active ? __('Activo') : __('Inactivo') }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Disponible para Venta') }}</h4>
                            <p class="mt-1 text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->available_for_sale ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200' }}">
                                    {{ $product->available_for_sale ? __('Sí') : __('No') }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Precio de Costo') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ currency($product->cost_price) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Precio de Venta') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ currency($product->selling_price) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('IVA') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->tax_percentage }}%</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Stock') }}</h4>
                            <p class="mt-1 text-sm {{ $product->stock <= $product->min_stock ? 'text-red-600 font-semibold' : 'text-gray-900 dark:text-gray-100' }}">{{ $product->stock }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Stock Mínimo') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->min_stock }}</p>
                        </div>
                    </div>
                    @if ($product->description)
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Descripción') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $product->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stock Movement History -->
            <div class="mt-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Movimientos de Stock') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tipo') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cantidad') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Referencia') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Usuario') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fecha') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($product->inventoryMovements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $movement->type === 'in' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200' }}">
                                                {{ $movement->type === 'in' ? __('Entrada') : __('Salida') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->reference ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->user->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('Sin movimientos') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex space-x-3">
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                    {{ __('Volver') }}
                </a>
    </x-content-wrapper>
</x-app-layout>