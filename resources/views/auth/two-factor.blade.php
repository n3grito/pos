<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Verificación en dos pasos</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Ingresa el código enviado a tu correo electrónico.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 p-3 rounded-md text-center">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="code" value="Código de verificación" />
            <x-text-input id="code" class="block mt-1 w-full text-center text-2xl tracking-[8px]" type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="000000" required autofocus autocomplete="off" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-3">
            Verificar identidad
        </x-primary-button>
    </form>

    <div class="mt-6 text-center">
        <form method="POST" action="{{ route('two-factor.send') }}">
            @csrf
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">¿No recibiste el código?</p>
            <button type="submit" class="text-sm text-blue-600 dark:text-blue-400 hover:underline focus:outline-none">
                Reenviar código
            </button>
        </form>
    </div>

    <div class="mt-4 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>
