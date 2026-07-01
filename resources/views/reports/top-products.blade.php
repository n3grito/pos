<x-app-layout>
    <x-slot name="header">{{ __('Productos Más Vendidos') }}</x-slot>
    <x-content-wrapper>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <x-input-label for="from" :value="__('Fecha Inicio')" />
                        <x-text-input id="from" class="block mt-1 w-full" type="date" name="from" :value="request('from', now()->startOfMonth()->format('Y-m-d'))" />
                    </div>
                    <div>
                        <x-input-label for="to" :value="__('Fecha Fin')" />
                        <x-text-input id="to" class="block mt-1 w-full" type="date" name="to" :value="request('to', now()->format('Y-m-d'))" />
                    </div>
                    <div>
                        <x-input-label for="category_id" :value="__('Categoría')" />
                        <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 text-sm">
                            <option value="">{{ __('Todas') }}</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <x-primary-button class="w-full justify-center">{{ __('Filtrar') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="px-4 md:px-6 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Top Productos') }}</h3>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export-pdf', 'top-products') }}?{{ http_build_query(request()->query()) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100">PDF</a>
                    <a href="{{ route('reports.export-csv', 'top-products') }}?{{ http_build_query(request()->query()) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium rounded-lg hover:bg-green-100">CSV</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Producto') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('SKU') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Categoría') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Cantidad') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Veces Vendido') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($topProducts as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->product?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $item->product?->sku ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $item->product?->category?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900 dark:text-gray-100">{{ $item->total_quantity }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500">{{ $item->times_sold }}</td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-indigo-600 dark:text-indigo-400">{{ currency($item->total_revenue) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('Sin datos') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
