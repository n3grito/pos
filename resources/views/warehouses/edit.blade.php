<x-app-layout>
    <x-slot name="header">{{ __('Editar Almacén') }}</x-slot>
    <x-content-wrapper>
        <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6">
                <form method="POST" action="{{ route('warehouses.update', $warehouse) }}">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name" :value="__('Nombre del Almacén')" />
                            <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $warehouse->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="description" :value="__('Descripción')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $warehouse->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="address" :value="__('Dirección')" />
                            <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address', $warehouse->address)" />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('Teléfono')" />
                            <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $warehouse->phone)" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $warehouse->is_active ? 'checked' : '' }} />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Activo') }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-8 flex items-center justify-end space-x-3">
                        <a href="{{ route('warehouses.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancelar</a>
                        <x-primary-button>{{ __('Actualizar') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
