<x-app-layout>
    <x-slot name="header">
    {{ __('Configuración de Correo') }}
</x-slot>

    <x-content-wrapper class="space-y-6">

            <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Configuración SMTP') }}</h3>

                    <form method="POST" action="{{ route('settings.mail.update') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="mailer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mailer</label>
                            <select id="mailer" name="mailer" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="smtp" {{ $mailConfig['mailer'] == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ $mailConfig['mailer'] == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="mailgun" {{ $mailConfig['mailer'] == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                <option value="ses" {{ $mailConfig['mailer'] == 'ses' ? 'selected' : '' }}>SES</option>
                                <option value="postmark" {{ $mailConfig['mailer'] == 'postmark' ? 'selected' : '' }}>Postmark</option>
                                <option value="log" {{ $mailConfig['mailer'] == 'log' ? 'selected' : '' }}>Log</option>
                                <option value="array" {{ $mailConfig['mailer'] == 'array' ? 'selected' : '' }}>Array</option>
                            </select>
                            @error('mailer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="host" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Host</label>
                            <input id="host" name="host" type="text" value="{{ old('host', $mailConfig['host']) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('host') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Puerto</label>
                            <input id="port" name="port" type="number" value="{{ old('port', $mailConfig['port']) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('port') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Usuario</label>
                            <input id="username" name="username" type="text" value="{{ old('username', $mailConfig['username']) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                            <input id="password" name="password" type="password" value="{{ old('password') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ $mailConfig['password'] === '********' ? 'Contraseña actual guardada' : 'Ingrese la contraseña' }}">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dejar en blanco para mantener la contraseña actual.</p>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Encriptación</label>
                            <select id="encryption" name="encryption" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="null" {{ is_null($mailConfig['encryption']) || $mailConfig['encryption'] == 'null' ? 'selected' : '' }}>Sin encriptación</option>
                                <option value="tls" {{ $mailConfig['encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ $mailConfig['encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                            @error('encryption') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección Desde (From)</label>
                            <input id="from_address" name="from_address" type="email" value="{{ old('from_address', $mailConfig['from_address']) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('from_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Desde (From)</label>
                            <input id="from_name" name="from_name" type="text" value="{{ old('from_name', $mailConfig['from_name']) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('from_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Guardar Configuración') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Prueba de Correo') }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ __('Envíe un correo de prueba para verificar la configuración SMTP.') }}</p>

                    <form method="POST" action="{{ route('settings.mail.test') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo Electrónico de Prueba</label>
                            <input id="email" name="email" type="email" placeholder="correo@ejemplo.com" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Enviar Correo de Prueba') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
