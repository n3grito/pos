<x-app-layout>
    <x-slot name="header">
    {{ __('Editar Usuario') }}: {{ $user->name }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nombre')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Contraseña (dejar vacío para mantener actual)')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="branch_id" :value="__('Sucursal')" />
                                <select id="branch_id" name="branch_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">{{ __('Seleccione una sucursal') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="warehouse_id" :value="__('Almacén')" />
                                <select id="warehouse_id" name="warehouse_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">{{ __('Seleccione un almacén') }}</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $user->warehouse_id) == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="is_active" :value="__('Estado')" />
                                <label class="inline-flex items-center mt-2">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Activo') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label :value="__('Roles')" />
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach ($roles as $role)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Actualizar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
