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

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('General') }}</h3>

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
    </x-content-wrapper>
</x-app-layout>
