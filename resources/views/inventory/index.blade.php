<x-app-layout>
    <x-slot name="header">
    {{ __('Inventario / Almacén') }}
</x-slot>

    <x-content-wrapper>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Stock de Productos') }}</h3>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('inventory.adjustment') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-500">
                            + {{ __('Ajustar Stock') }}
                        </a>
                        <a href="{{ route('inventory.movements') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                            {{ __('Movimientos') }}
                        </a>
                    </div>
                </div>

                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <form method="GET" class="flex flex-wrap items-center gap-3">
                        <div class="w-full sm:flex-1 sm:min-w-[200px]">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Buscar producto, SKU o código...') }}" class="block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div class="w-full sm:w-40">
                            <select name="stock" class="block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('Todo el stock') }}</option>
                                <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>{{ __('Stock Bajo') }}</option>
                                <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>{{ __('Sin Stock') }}</option>
                                <option value="available" {{ request('stock') == 'available' ? 'selected' : '' }}>{{ __('Disponible') }}</option>
                            </select>
                        </div>
                        <div class="w-full sm:w-40">
                            <select name="sale" class="block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('Venta: Todas') }}</option>
                                <option value="yes" {{ request('sale') == 'yes' ? 'selected' : '' }}>{{ __('Vendible') }}</option>
                                <option value="no" {{ request('sale') == 'no' ? 'selected' : '' }}>{{ __('No vendible') }}</option>
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 dark:hover:bg-gray-400">
                            {{ __('Filtrar') }}
                        </button>
                        @if(request()->anyFilled(['search', 'stock', 'sale']))
                            <a href="{{ route('inventory.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">{{ __('Limpiar') }}</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Producto') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('SKU') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Categoría') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Stock') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Stock Mín.') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Venta') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Estado') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($products as $product)
                                <tr class="{{ !$product->available_for_sale ? 'opacity-60' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('inventory.show', $product) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">{{ $product->name }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center {{ $product->stock <= $product->min_stock ? 'text-red-600 dark:text-red-400' : ($product->stock == 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100') }}">
                                        {{ $product->stock }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->min_stock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @can('product.update')
                                        <form method="POST" action="{{ route('inventory.toggle-availability', $product) }}" class="inline" x-data @submit.prevent="if(confirm('{{ $product->available_for_sale ? __('¿Deshabilitar venta de :name?', ['name' => $product->name]) : __('¿Habilitar venta de :name?', ['name' => $product->name]) }}')) $el.submit()">
                                            @csrf
                                            <button type="submit" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $product->available_for_sale ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform duration-200 {{ $product->available_for_sale ? 'translate-x-[18px]' : 'translate-x-[3px]' }}"></span>
                                            </button>
                                        </form>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $product->available_for_sale ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400' }}">
                                                {{ $product->available_for_sale ? __('Sí') : __('No') }}
                                            </span>
                                        @endcan
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($product->stock == 0)
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400 rounded-full">{{ __('Sin Stock') }}</span>
                                        @elseif ($product->stock <= $product->min_stock)
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 rounded-full">{{ __('Stock Bajo') }}</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 rounded-full">{{ __('Disponible') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('inventory.show', $product) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">{{ __('Ver') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay productos en inventario') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($products->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
    </x-content-wrapper>
</x-app-layout>
