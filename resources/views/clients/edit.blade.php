<x-app-layout>
    <x-slot name="header">
    {{ __('Editar Cliente') }}: {{ $client->name }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('clients.update', $client) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nombre')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $client->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Correo Electrónico')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $client->email)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Teléfono')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $client->phone)" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="document_type" :value="__('Tipo de Documento')" />
                                <select id="document_type" name="document_type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="DNI" {{ old('document_type', $client->document_type) === 'DNI' ? 'selected' : '' }}>DNI</option>
                                    <option value="RUC" {{ old('document_type', $client->document_type) === 'RUC' ? 'selected' : '' }}>RUC</option>
                                    <option value="CE" {{ old('document_type', $client->document_type) === 'CE' ? 'selected' : '' }}>Carné de Extranjería</option>
                                </select>
                                <x-input-error :messages="$errors->get('document_type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="document_number" :value="__('Número de Documento')" />
                                <x-text-input id="document_number" class="block mt-1 w-full" type="text" name="document_number" :value="old('document_number', $client->document_number)" />
                                <x-input-error :messages="$errors->get('document_number')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Dirección')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $client->address)" />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('clients.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Actualizar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
