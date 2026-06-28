<x-app-layout>
    <x-slot name="header">
    {{ __('Profile') }}
</x-slot>

    <x-content-wrapper class="space-y-6">
            <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-w-xl">
                    @php $twoFactorEnabled = (bool) \App\Models\GeneralSetting::get('2fa_enabled', false); @endphp
                    @if($twoFactorEnabled)
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Autenticación de Dos Factores') }}</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Protege tu cuenta con un código de verificación enviado a tu correo electrónico.') }}</p>
                        </header>

                        <div class="mt-6">
                            @if(auth()->user()->two_factor_enabled)
                                <div class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm text-green-800 dark:text-green-200 font-medium">{{ __('2FA está activo en tu cuenta.') }}</span>
                                </div>
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('two-factor.disable') }}">
                                        @csrf
                                        <x-danger-button onclick="return confirm('¿Desactivar la verificación en dos pasos?')">{{ __('Desactivar 2FA') }}</x-danger-button>
                                    </form>
                                </div>
                            @else
                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('2FA no está activado.') }}</span>
                                </div>
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('two-factor.enable') }}">
                                        @csrf
                                        <x-primary-button>{{ __('Activar 2FA') }}</x-primary-button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </section>
                    @endif
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
