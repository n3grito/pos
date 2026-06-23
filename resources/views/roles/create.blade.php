<x-app-layout>
    <x-slot name="header">
    {{ __('Nuevo Rol') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf

                        <div class="mb-6">
                            <x-input-label for="name" :value="__('Nombre del Rol')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Permisos') }}</h3>

                            @foreach ($permissions as $module => $modulePermissions)
                                <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-lg p-4" x-data="{ allSelected: false }">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-md font-semibold text-gray-600 dark:text-gray-400 uppercase">{{ __(ucfirst($module)) }}</h4>
                                        <label class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-2" x-on:click="allSelected = !allSelected; document.querySelectorAll('.permission-{{ $module }}').forEach(cb => cb.checked = allSelected)">
                                            {{ __('Seleccionar todo') }}
                                        </label>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        @foreach ($modulePermissions as $permission)
                                            <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="permission-{{ $module }} rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-2" {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                                {{ __(str_replace('-', ' ', ucfirst(explode('.', $permission->name)[1]))) }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
