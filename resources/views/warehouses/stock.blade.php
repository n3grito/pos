<x-app-layout>
    <x-slot name="header">{{ __('Stock por Almacén') }}</x-slot>
    <x-content-wrapper>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:space-x-4">
                    <div class="w-full sm:w-64">
                        <select name="warehouse_id" onchange="this.form.submit()" class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Seleccionar almacén</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" {{ $selectedWarehouseId == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($selectedWarehouseId)
                        <a href="{{ route('warehouses.stock') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Limpiar filtro</a>
                    @endif
                </form>
            </div>

            @if ($selectedWarehouseId)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="text-left px-6 py-3 font-semibold">Producto</th>
                                <th class="text-left px-6 py-3 font-semibold">SKU</th>
                                <th class="text-left px-6 py-3 font-semibold">Código Barras</th>
                                <th class="text-right px-6 py-3 font-semibold">Stock</th>
                                <th class="text-left px-6 py-3 font-semibold">Categoría</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($stock as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $item->product->name }}</td>
                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $item->product->sku }}</td>
                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $item->product->barcode ?? '—' }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="font-medium {{ $item->quantity > 0 ? 'text-gray-800 dark:text-gray-200' : 'text-red-500 dark:text-red-400' }}">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $item->product->category->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">Sin stock en este almacén.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center text-gray-400 dark:text-gray-500">Seleccione un almacén para ver su stock.</div>
            @endif
        </div>
    </x-content-wrapper>
</x-app-layout>
