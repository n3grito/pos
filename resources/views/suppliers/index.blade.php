<x-app-layout>
    <x-slot name="header">
    {{ __('Proveedores') }}
</x-slot>

    <x-content-wrapper>
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Listado de Proveedores') }}</h3>
                    <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                        {{ __('Nuevo Proveedor') }}
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nombre') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Contacto') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Email') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Teléfono') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($suppliers as $supplier)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $supplier->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $supplier->contact_name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $supplier->email ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $supplier->phone ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">{{ __('Ver') }}</a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">{{ __('Editar') }}</a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Está seguro?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">{{ __('Eliminar') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay proveedores registrados') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($suppliers->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            </div>
    </x-content-wrapper>
</x-app-layout>
