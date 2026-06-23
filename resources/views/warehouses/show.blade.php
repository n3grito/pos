<x-app-layout>
    <x-slot name="header">{{ $warehouse->name }}</x-slot>
    <x-content-wrapper>
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Información del Almacén') }}</h3>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Nombre') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $warehouse->name }}</dd>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Descripción') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $warehouse->description ?? '—' }}</dd>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Dirección') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $warehouse->address ?? '—' }}</dd>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Teléfono') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $warehouse->phone ?? '—' }}</dd>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Estado') }}</dt>
                        <dd>
                            @if ($warehouse->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-400">Activo</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-400">Inactivo</span>
                            @endif
                        </dd>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Total Productos') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $warehouse->stock()->sum('quantity') }}</dd>
                    </dl>
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('purchases.create') }}" class="px-4 py-2 text-sm font-medium text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/50 rounded-lg hover:bg-green-100 dark:hover:bg-green-900 transition-colors">Nueva Compra</a>
                        <a href="{{ route('warehouses.transfer') }}" class="px-4 py-2 text-sm font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors">Entrada a Inventario</a>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Stock en Almacén') }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="text-left px-6 py-3 font-semibold">Producto</th>
                                    <th class="text-center px-6 py-3 font-semibold">SKU</th>
                                    <th class="text-left px-6 py-3 font-semibold">Categoría</th>
                                    <th class="text-right px-6 py-3 font-semibold">Precio Costo</th>
                                    <th class="text-right px-6 py-3 font-semibold">Precio Venta</th>
                                    <th class="text-right px-6 py-3 font-semibold">Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($warehouse->stock as $ws)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $ws->product->name }}</td>
                                        <td class="px-6 py-3 text-center text-gray-500 dark:text-gray-400">{{ $ws->product->sku }}</td>
                                        <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $ws->product->category->name ?? '—' }}</td>
                                        <td class="px-6 py-3 text-right text-gray-500 dark:text-gray-400">{{ currency($ws->product->cost_price) }}</td>
                                        <td class="px-6 py-3 text-right text-gray-500 dark:text-gray-400">{{ currency($ws->product->selling_price) }}</td>
                                        <td class="px-6 py-3 text-right font-medium {{ $ws->quantity > 0 ? 'text-gray-800 dark:text-gray-200' : 'text-red-500 dark:text-red-400' }}">{{ $ws->quantity }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">Sin productos en este almacén.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Últimos Movimientos') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="text-left px-6 py-3 font-semibold">Fecha</th>
                                <th class="text-left px-6 py-3 font-semibold">Tipo</th>
                                <th class="text-left px-6 py-3 font-semibold">Producto</th>
                                <th class="text-right px-6 py-3 font-semibold">Cant.</th>
                                <th class="text-left px-6 py-3 font-semibold">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($warehouse->inventoryMovements as $m)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 text-xs">
                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-3">
                                        @if ($m->type === 'warehouse_entry' || $m->type === 'transfer')
                                            <span class="text-green-600 dark:text-green-400 font-medium">Entrada</span>
                                        @else
                                            <span class="text-gray-600 dark:text-gray-400">{{ $m->type }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $m->product->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-right font-medium">{{ $m->quantity }}</td>
                                    <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $m->user->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">Sin movimientos recientes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('inventory.movements') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Ver todos los movimientos →</a>
                </div>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
