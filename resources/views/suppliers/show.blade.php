<x-app-layout>
    <x-slot name="header">
    {{ __('Proveedor') }}: {{ $supplier->name }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nombre de la Empresa') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nombre de Contacto') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->contact_name ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Correo Electrónico') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->email ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Teléfono') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->phone ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Dirección') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->address ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('RUC / NIT') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->tax_id ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Creado') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Actualizado') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                            {{ __('Editar') }}
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
