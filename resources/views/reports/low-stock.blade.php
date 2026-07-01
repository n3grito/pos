<x-app-layout>
    <x-slot name="header">{{ __('Productos con Bajo Stock') }}</x-slot>
    <x-content-wrapper>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700 bg-red-50 dark:bg-red-900/10">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Productos críticos') }}</div>
                        <div class="mt-1 text-xl font-semibold text-red-600">{{ $totalLowStock }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Unidades faltantes') }}</div>
                        <div class="mt-1 text-xl font-semibold text-amber-600">{{ $totalMissing }}</div>
                    </div>
                </div>
            </div>

            <div class="px-4 md:px-6 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Inventario') }}</h3>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export-pdf', 'low-stock') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100">PDF</a>
                    <a href="{{ route('reports.export-csv', 'low-stock') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium rounded-lg hover:bg-green-100">CSV</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Producto') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('SKU') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Categoría') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Sucursal') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Stock') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Mínimo') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Faltan') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($products as $p)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $p->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $p->sku }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $p->category?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $p->branch?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold {{ $p->stock <= 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $p->stock }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500">{{ $p->min_stock }}</td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-red-600">{{ max(0, $p->min_stock - $p->stock) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-green-600">{{ __('¡Todo en orden! No hay productos con bajo stock.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
