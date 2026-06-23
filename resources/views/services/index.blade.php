<x-app-layout>
    <x-slot name="header">{{ __('Servicios') }}</x-slot>
    <x-content-wrapper>
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg">{{ session('success') }}</div>
        @endif
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Lista de Servicios') }}</h3>
                <div class="flex items-center space-x-2">
                    @can('service.create')
                    <a href="{{ route('services.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Nuevo Servicio
                    </a>
                    @endcan
                    <a href="{{ route('services.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        {{ __('Exportar') }}
                    </a>
                    <form action="{{ route('services.import') }}" method="POST" enctype="multipart/form-data" class="inline-flex items-center">
                        @csrf
                        <label class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-gray-700 transition-colors">
                            {{ __('Importar') }}
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold">{{ __('Nombre') }}</th>
                            <th class="text-left px-6 py-3 font-semibold">{{ __('Categoría') }}</th>
                            <th class="text-right px-6 py-3 font-semibold">{{ __('Precio Venta') }}</th>
                            <th class="text-right px-6 py-3 font-semibold">{{ __('IVA') }}</th>
                            <th class="text-center px-6 py-3 font-semibold">{{ __('Productos') }}</th>
                            <th class="text-center px-6 py-3 font-semibold">{{ __('Estado') }}</th>
                            <th class="text-right px-6 py-3 font-semibold">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($services as $service)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">{{ $service->name }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $service->category->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-gray-200">{{ currency($service->selling_price) }}</td>
                                <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400">{{ $service->tax_percentage }}%</td>
                                <td class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">{{ $service->products->count() }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($service->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200">{{ __('Activo') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200">{{ __('Inactivo') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('services.show', $service) }}" class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/50 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/70">{{ __('Ver') }}</a>
                                        @can('service.update')
                                        <a href="{{ route('services.edit', $service) }}" class="px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/50 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/70">{{ __('Editar') }}</a>
                                        @endcan
                                        @can('service.delete')
                                        <form method="POST" action="{{ route('services.destroy', $service) }}" onsubmit="return confirm('{{ __('¿Eliminar este servicio?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/50 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/70">{{ __('Eliminar') }}</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">{{ __('No hay servicios registrados.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($services->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">{{ $services->links() }}</div>
            @endif
        </div>
    </x-content-wrapper>
</x-app-layout>
