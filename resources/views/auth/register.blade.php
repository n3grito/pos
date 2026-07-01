<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="nit" :value="__('NIT')" />
            <x-text-input id="nit" class="block mt-1 w-full" type="text" name="nit" :value="old('nit')" autocomplete="off" />
            <x-input-error :messages="$errors->get('nit')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="address" :value="__('Dirección particular')" />
            <textarea id="address" name="address" rows="2" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone_personal" :value="__('Número de teléfono')" />
            <x-text-input id="phone_personal" class="block mt-1 w-full" type="text" name="phone_personal" :value="old('phone_personal')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone_personal')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-password-input name="password" :label="__('Contraseña')" required autocomplete="new-password" />
        </div>

        <div class="mt-4">
            <x-password-input name="password_confirmation" :label="__('Confirmar Contraseña')" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('¿Ya estás registrado?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>


</x-guest-layout>
