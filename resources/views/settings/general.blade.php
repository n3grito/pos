<x-app-layout>
    <x-slot name="header">
        {{ __('Configuración General') }}
    </x-slot>

    <x-content-wrapper>
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/50 border border-green-200 text-green-700 dark:text-green-400 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Company Info Form -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Información del Negocio') }}</h3>

                    <form method="POST" action="{{ route('settings.general.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="company_name" :value="__('Nombre de la Empresa')" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Se muestra como subtítulo en el sidebar') }}</p>
                                <x-text-input id="company_name" name="company_name" type="text" class="block mt-1 w-full" :value="old('company_name', $receipt->company_name)" placeholder="Mi Empresa S.A." />
                                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="store_name" :value="__('Nombre del Negocio (recibo)')" />
                                <x-text-input id="store_name" name="store_name" type="text" class="block mt-1 w-full" :value="old('store_name', $receipt->store_name ?? config('app.name'))" />
                                <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('Teléfono')" />
                                <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $receipt->phone)" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="address" :value="__('Dirección')" />
                                <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address', $receipt->address)" />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="footer_text" :value="__('Mensaje al pie del recibo')" />
                                <textarea id="footer_text" name="footer_text" rows="2" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">{{ old('footer_text', $receipt->footer_text ?? '¡Gracias por su compra!') }}</textarea>
                                <x-input-error :messages="$errors->get('footer_text')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="logo" :value="__('Logo de la Empresa')" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Máximo 2MB. PNG, JPG, SVG o WEBP.') }}</p>
                                <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/svg+xml,image/webp" class="block mt-1 w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/50 file:text-indigo-700 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800/50" />
                                <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                                @if ($receipt->logo_path)
                                    <div class="mt-2 flex items-center space-x-3">
                                        <img src="{{ asset('storage/' . $receipt->logo_path) }}" alt="Logo" class="h-10 w-auto rounded border border-gray-200 dark:border-gray-700">
                                        <label class="inline-flex items-center text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 cursor-pointer">
                                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 dark:border-gray-600 text-red-600 shadow-sm focus:ring-red-500 mr-1">
                                            {{ __('Eliminar logo') }}
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <x-primary-button>{{ __('Guardar configuración') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar Preview + Registration -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Vista previa del sidebar') }}</h3>
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800/50 max-w-xs mx-auto">
                            <div class="flex items-center space-x-3">
                                @if ($receipt->logo_path)
                                    <img src="{{ asset('storage/' . $receipt->logo_path) }}" alt="Logo" class="h-10 w-auto">
                                @else
                                    <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg">
                                        {{ substr(old('company_name', $receipt->company_name ?? config('app.name')), 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ config('app.name') }}</div>
                                    @if (old('company_name', $receipt->company_name))
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ old('company_name', $receipt->company_name) }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Registro de Usuarios') }}</h3>
                        <form method="POST" action="{{ route('settings.general.update') }}">
                            @csrf
                            <div class="space-y-6">
                                <div>
                                    <label class="inline-flex items-center gap-3">
                                        <input type="hidden" name="registration_enabled" value="0">
                                        <input type="checkbox" name="registration_enabled" value="1" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $registrationEnabled ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Permitir registro de nuevos usuarios') }}</span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 ml-7">{{ __('Cuando está desactivado, la página de registro y el enlace en la pantalla de bienvenida no estarán disponibles.') }}</p>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <x-primary-button>{{ __('Guardar configuración') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
