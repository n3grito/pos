<x-app-layout>
    <x-slot name="header">{{ __('Listado de Precios') }}</x-slot>
    <x-content-wrapper>
        @can('setting.manage')
        <div class="mb-4">
            <x-primary-button as="a" href="{{ route('price-lists.create') }}">{{ __('Nuevo') }}</x-primary-button>
        </div>
        @endcan
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Nombre') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Productos') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Por defecto') }}</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($priceLists as $list)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-200">{{ $list->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $list->products_count }}</td>
                            <td class="px-6 py-4">
                                @if ($list->is_default)
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-full">{{ __('Sí') }}</span>
                                @else
                                    <span class="text-sm text-gray-400">{{ __('No') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                @can('setting.manage')
                                <a href="{{ route('price-lists.edit', $list) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Editar') }}</a>
                                <form action="{{ route('price-lists.destroy', $list) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Está seguro?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 dark:text-red-400 hover:underline">{{ __('Eliminar') }}</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">{{ __('No hay productos registrados') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-content-wrapper>
</x-app-layout>
