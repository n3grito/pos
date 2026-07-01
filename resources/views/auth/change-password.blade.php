<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Cambio obligatorio de contraseña</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Por seguridad, debes cambiar tu contraseña antes de continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('password.change.update') }}" class="space-y-4">
        @csrf

        <div>
            <x-password-input name="password" label="Nueva contraseña" required autocomplete="new-password" autofocus />
        </div>

        <div>
            <x-password-input name="password_confirmation" label="Confirmar contraseña" required autocomplete="new-password" />
        </div>

        <x-primary-button class="w-full justify-center py-3">
            Cambiar contraseña
        </x-primary-button>
    </form>

    <div class="mt-4 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">
                Cerrar sesión
            </button>
        </form>
    </div>


</x-guest-layout>
