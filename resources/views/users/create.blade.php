<x-app-layout>
    <x-slot name="header">
    {{ __('Nuevo Usuario') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nombre')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nit" :value="__('NIT')" />
                                <x-text-input id="nit" class="block mt-1 w-full" type="text" name="nit" :value="old('nit')" maxlength="11" placeholder="00000000000" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">11 dígitos numéricos, único por usuario.</p>
                                <x-input-error :messages="$errors->get('nit')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Dirección postal')" />
                                <textarea id="address" name="address" rows="2" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">{{ old('address') }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="phone_personal" :value="__('Número de teléfono')" />
                                <x-text-input id="phone_personal" class="block mt-1 w-full" type="text" name="phone_personal" :value="old('phone_personal')" />
                                <x-input-error :messages="$errors->get('phone_personal')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Contraseña')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="branch_id" :value="__('Sucursal')" />
                                <select id="branch_id" name="branch_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">{{ __('Seleccione una sucursal') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="warehouse_id" :value="__('Almacén')" />
                                <select id="warehouse_id" name="warehouse_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">{{ __('Seleccione un almacén') }}</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="is_active" :value="__('Estado')" />
                                <label class="inline-flex items-center mt-2">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
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
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        </div>

                        <div class="mt-6" x-data="{ showPermissions: false }">
                            <button type="button" @click="showPermissions = !showPermissions" class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                <svg :class="{'rotate-90': showPermissions}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                {{ __('Permisos directos') }}
                            </button>
                            <div x-show="showPermissions" class="mt-3">
                                @foreach ($permissions as $module => $modulePermissions)
                                    <div class="mb-4 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">{{ __(ucfirst($module)) }}</h4>
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                            @foreach ($modulePermissions as $permission)
                                                <label class="inline-flex items-center text-xs text-gray-700 dark:text-gray-300">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-1">
                                                    {{ __(str_replace('-', ' ', ucfirst(explode('.', $permission->name)[1]))) }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
