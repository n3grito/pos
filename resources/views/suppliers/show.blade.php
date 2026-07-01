<x-app-layout>
    <x-slot name="header">{{ __('Proveedor') }}: {{ $supplier->name }}</x-slot>

    <x-content-wrapper>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6">
                <div class="flex items-start gap-6 mb-6">
                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0 ring-2" style="border-color: {{ $supplier->type_color }}40">
                        @if ($supplier->photo_url)
                            <img src="{{ $supplier->photo_url }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m8-10a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $supplier->name }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $supplier->type_color }}15; color: {{ $supplier->type_color }}; border: 1px solid {{ $supplier->type_color }}30">
                                {{ $supplier->type_label }}
                            </span>
                            @if ($supplier->tax_id)
                                <span class="text-sm text-gray-500 dark:text-gray-400">NIT: {{ $supplier->tax_id }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Persona de Contacto') }}</h4>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $supplier->contact_person ?? __('N/A') }}</p>
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
