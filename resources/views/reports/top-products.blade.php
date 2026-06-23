<x-app-layout>
    <x-slot name="header">
    {{ __('Productos Más Vendidos') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <form method="GET" action="{{ route('reports.top-products') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="from" :value="__('Fecha Inicio')" />
                            <x-text-input id="from" class="block mt-1 w-full" type="date" name="from" :value="request('from', now()->startOfMonth()->format('Y-m-d'))" />
                        </div>
                        <div>
                            <x-input-label for="to" :value="__('Fecha Fin')" />
                            <x-text-input id="to" class="block mt-1 w-full" type="date" name="to" :value="request('to', now()->format('Y-m-d'))" />
                        </div>
                        <div class="flex items-end space-x-2">
                            <x-primary-button>{{ __('Filtrar') }}</x-primary-button>
                            <a href="{{ route('reports.top-products') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Limpiar') }}
                            </a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Producto') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('SKU') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Categoría') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cantidad Vendida') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total Ventas') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($topProducts as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->product->name ?? $item->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->product->sku ?? $item->sku ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->product->category->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->total_quantity ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($item->total_revenue ?? 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay datos en el período seleccionado') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>