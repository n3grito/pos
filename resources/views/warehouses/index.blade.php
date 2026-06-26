<x-app-layout>
    <x-slot name="header">{{ __('Almacenes') }}</x-slot>
    <x-content-wrapper>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Lista de Almacenes') }}</h3>
                @can('warehouse.create')
                <a href="{{ route('warehouses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Nuevo Almacén
                </a>
                @endcan
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-600 dark:text-gray-400">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold">Nombre</th>
                            <th class="text-left px-6 py-3 font-semibold">Dirección</th>
                            <th class="text-left px-6 py-3 font-semibold">Teléfono</th>
                            <th class="text-center px-6 py-3 font-semibold">Productos</th>
                            <th class="text-center px-6 py-3 font-semibold">Estado</th>
                            <th class="text-right px-6 py-3 font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($warehouses as $warehouse)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">{{ $warehouse->name }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $warehouse->address ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $warehouse->phone ?? '—' }}</td>
                                <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">{{ $warehouse->stock()->sum('quantity') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($warehouse->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-400">Activo</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-400">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        @can('warehouse.view')
                                        <a href="{{ route('warehouses.show', $warehouse) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900 transition-colors">Ver</a>
                                        @endcan
                                        @can('warehouse.update')
                                        <a href="{{ route('warehouses.edit', $warehouse) }}" class="px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/50 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900 transition-colors">Editar</a>
                                        @endcan
                                        @can('warehouse.delete')
                                        <form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}" onsubmit="return confirm('¿Eliminar este almacén?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/50 rounded-lg hover:bg-red-100 dark:hover:bg-red-900 transition-colors">Eliminar</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">No hay almacenes registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($warehouses->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">{{ $warehouses->links() }}</div>
            @endif
        </div>
    </x-content-wrapper>
</x-app-layout>
