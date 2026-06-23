<x-app-layout>
    <x-slot name="header">
    {{ __('Nueva Moneda') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('currencies.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nombre')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="{{ __('Peso Cubano') }}" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="code" :value="__('Código')" />
                                <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" placeholder="CUP" required maxlength="10" />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="symbol" :value="__('Símbolo')" />
                                <x-text-input id="symbol" class="block mt-1 w-full" type="text" name="symbol" :value="old('symbol')" placeholder="$" required maxlength="10" />
                                <x-input-error :messages="$errors->get('symbol')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="exchange_rate" :value="__('Tasa de Cambio')" />
                                <x-text-input id="exchange_rate" class="block mt-1 w-full" type="number" step="0.0001" name="exchange_rate" :value="old('exchange_rate', 1)" required />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Valor relativo a la moneda base (ej: 1 USD = 120 CUP)') }}</p>
                                <x-input-error :messages="$errors->get('exchange_rate')" class="mt-2" />
                            </div>

                            <div>
                                <label class="inline-flex items-center mt-4">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Moneda activa') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('currencies.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
