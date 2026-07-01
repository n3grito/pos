<x-app-layout>
    <x-slot name="header">
    {{ __('Nuevo Grupo de Clientes') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('customer-groups.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nombre')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="discount_percentage" :value="__('Descuento (%)')" />
                                <x-text-input id="discount_percentage" class="block mt-1 w-full" type="number" step="0.01" min="0" max="100" name="discount_percentage" :value="old('discount_percentage', '0')" required />
                                <x-input-error :messages="$errors->get('discount_percentage')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="color" :value="__('Color')" />
                                <x-text-input id="color" class="block mt-1 w-full" type="color" name="color" :value="old('color', '#6366f1')" required />
                                <x-input-error :messages="$errors->get('color')" class="mt-2" />
                            </div>

                            <div class="flex items-center mt-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Grupo por defecto') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('customer-groups.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
