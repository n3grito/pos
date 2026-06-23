<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') || 'system' }" x-init="
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
">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <script>(function(){var m=localStorage.getItem('darkMode')||'system';if(m==='dark'||(m==='system'&&window.matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.classList.add('dark')})()</script>

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">

        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#4F46E5">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="mobile-web-app-capable" content="yes">

        @php
            $favicon = \App\Models\ReceiptSetting::firstOrNew([]);
        @endphp
        @if ($favicon->logo_path)
            <link rel="icon" type="image/png" href="{{ asset('storage/' . $favicon->logo_path) }}">
        @else
            <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90' fill='%234f46e5'>{{ substr(config('app.name'), 0, 1) }}</text></svg>">
        @endif
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen" x-data="{ sidebarOpen: false }">
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-50 dark:bg-opacity-70 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

            <x-sidebar />

            <div class="fixed top-0 right-0 z-30 h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 lg:left-64">
                <div class="flex items-center justify-between h-full px-4 lg:px-8">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 mr-3 text-gray-400 dark:text-gray-500 rounded-md lg:hidden hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-600 dark:focus:text-gray-300">
                            <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        @isset($header)
                            <h1 class="text-base font-semibold text-gray-700 dark:text-gray-300">
                                {{ $header }}
                            </h1>
                        @endisset
                    </div>

                    <div class="flex items-center space-x-2">
                        <button @click="darkMode = darkMode === 'light' ? 'dark' : darkMode === 'dark' ? 'system' : 'light'" class="p-2 text-gray-400 dark:text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Cambiar tema">
                            <template x-if="darkMode === 'light'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </template>
                            <template x-if="darkMode === 'dark'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            </template>
                            <template x-if="darkMode === 'system'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </template>
                        </button>
                        @include('layouts.navigation')
                    </div>
                </div>
            </div>

            <main class="pt-16 lg:ml-64">
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js');
            }
        </script>
    </body>
</html>