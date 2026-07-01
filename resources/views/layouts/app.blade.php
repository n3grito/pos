<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') || '{{ auth()->user()?->dark_mode ?? 'system' }}' }" x-init="
    if (darkMode === 'dark' || (darkMode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    $watch('darkMode', val => {
        localStorage.setItem('darkMode', val);
        if (val === 'dark' || (val === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    });
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (darkMode === 'system') {
            document.documentElement.classList.toggle('dark', e.matches);
        }
    });
    window.addEventListener('dark-mode-changed', e => { darkMode = e.detail.mode; });
    window.addEventListener('dark-mode-toggle', e => {
        darkMode = darkMode === 'light' ? 'dark' : darkMode === 'dark' ? 'system' : 'light';
    });
">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth <meta name="user-id" content="{{ auth()->id() }}"> @endauth

        <script nonce="{{ $cspNonce ?? '' }}">(function(){var m=localStorage.getItem('darkMode')||'system';if(m==='dark'||(m==='system'&&window.matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.classList.add('dark')})()</script>

        <title>{{ config('app.name') }}</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.svg') }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

        @php
            $favicon = \App\Models\ReceiptSetting::firstOrNew([]);
        @endphp
        @if ($favicon->logo_path)
            <link rel="icon" type="image/png" href="{{ logo_url($favicon->logo_path) }}">
        @else
            <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90' fill='%234f46e5'>{{ substr(config('app.name'), 0, 1) }}</text></svg>">
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div x-data="offlineIndicator" x-cloak>
            <div data-online-indicator class="hidden items-center justify-center gap-2 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-medium px-3 py-1.5 border-b border-amber-200 dark:border-amber-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m-2.829-2.829a5 5 0 000-7.07m-4.243 4.243a1 1 0 010-1.414"/></svg>
                <span>{{ __('Sin conexión') }}</span>
                <span x-show="queuedCount > 0" x-text="'— ' + queuedCount + ' ' + (queuedCount === 1 ? 'venta pendiente' : 'ventas pendientes')"></span>
            </div>
        </div>
        <div class="min-h-screen flex flex-col" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" x-init="
            $watch('sidebarOpen', val => {
                if (val) localStorage.removeItem('sidebarCollapsed');
                else localStorage.setItem('sidebarCollapsed', '1');
            });
            if (localStorage.getItem('sidebarCollapsed') && window.innerWidth >= 1024) sidebarOpen = false;
            let _st;
            window.addEventListener('resize', () => {
                clearTimeout(_st);
                _st = setTimeout(() => {
                    if (window.innerWidth < 1024) sidebarOpen = false;
                }, 100);
            });
        ">
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

            <x-sidebar />

            <div class="fixed top-0 right-0 z-30 h-14 lg:h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-[left] duration-300 ease-in-out" x-bind:class="sidebarOpen ? 'lg:left-64' : 'lg:left-0'">
                <div class="flex items-center justify-between h-full px-3 lg:px-8">
                    <div class="flex items-center min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-1.5 sm:p-2 mr-2 sm:mr-3 text-gray-400 dark:text-gray-500 rounded-md hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-600 dark:focus:text-gray-300 shrink-0">
                            <svg x-show="!sidebarOpen" class="w-5 h-5 sm:w-6 sm:h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="sidebarOpen" class="w-5 h-5 sm:w-6 sm:h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        @isset($header)
                            <h1 class="text-sm sm:text-base font-semibold text-gray-700 dark:text-gray-300 truncate">
                                {{ $header }}
                            </h1>
                        @endisset
                    </div>

                    <div class="flex items-center gap-0.5 sm:gap-1.5">
                        @livewire('status-indicators')
                        @livewire('global-search')
                        @can('sale.create')
                            @livewire('quick-sale')
                        @endcan
                        <livewire:dark-mode-toggle :key="auth()->user()?->id" />
                        @include('layouts.navigation')
                    </div>
                </div>
            </div>

            <main class="pt-14 lg:pt-16 flex-1 flex flex-col transition-[margin-left] duration-300 ease-in-out" x-bind:class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">
                <div class="flex-1">
                    {{ $slot }}
                </div>
                <footer class="px-6 py-3 text-center text-xs text-gray-400 dark:text-gray-500 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    v{{ config('app.version', '1.0.0') }} &copy; {{ date('Y') }} {{ config('app.name') }}
                </footer>
            </main>
        </div>

        <x-toast-notifications />

        @livewireScripts
        @stack('scripts')

        <div x-data="{ show: !localStorage.getItem('cookie_consent') }" x-show="show" x-cloak class="fixed bottom-0 left-0 right-0 z-50 bg-gray-900 dark:bg-gray-950 text-white px-4 py-3 flex items-center justify-between gap-4 text-sm">
            <p class="text-gray-300">{{ __('Este sitio utiliza cookies esenciales para su funcionamiento.') }} <a href="{{ route('privacy') }}" class="text-blue-400 hover:underline">{{ __('Más información') }}</a></p>
            <button @click="localStorage.setItem('cookie_consent', '1'); show = false" class="shrink-0 px-4 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg transition-colors">{{ __('Aceptar') }}</button>
        </div>

        @can('activity-log.view')
            <span id="activityPollerData" data-stream-url="{{ route('activity-logs.stream') }}" class="hidden"></span>
        @endcan
    </body>
</html>