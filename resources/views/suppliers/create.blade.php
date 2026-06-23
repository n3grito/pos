<x-app-layout>
    <x-slot name="header">
    {{ __('Nuevo Proveedor') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('suppliers.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nombre de la Empresa')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="contact_name" :value="__('Nombre de Contacto')" />
                                <x-text-input id="contact_name" class="block mt-1 w-full" type="text" name="contact_name" :value="old('contact_name')" />
                                <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Correo Electrónico')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Teléfono')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Dirección')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="tax_id" :value="__('RUC / NIT')" />
                                <x-text-input id="tax_id" class="block mt-1 w-full" type="text" name="tax_id" :value="old('tax_id')" />
                                <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
