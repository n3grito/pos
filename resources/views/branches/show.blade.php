<x-app-layout>
    <x-slot name="header">
    {{ __('Sucursal') }}: {{ $branch->name }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nombre') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $branch->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Teléfono') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $branch->phone ?? __('N/A') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Dirección') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $branch->address ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Correo Electrónico') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $branch->email ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estado') }}</h4>
                            <p class="mt-1 text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $branch->status === 'active' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200' }}">
                                    {{ $branch->status === 'active' ? __('Activa') : __('Inactiva') }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Creado') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $branch->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Actualizado') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $branch->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('branches.edit', $branch) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600">
                            {{ __('Editar') }}
                        </a>
                        <a href="{{ route('branches.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>