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

        @php
            $favicon = \App\Models\ReceiptSetting::firstOrNew([]);
        @endphp
        @if ($favicon->logo_path)
            <link rel="icon" type="image/png" href="{{ asset('storage/' . $favicon->logo_path) }}">
        @else
            <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90' fill='%234f46e5'>{{ substr(config('app.name'), 0, 1) }}</text></svg>">
        @endif

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500 dark:text-gray-400" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg dark:shadow-gray-700/30">
                {{ $slot }}
            </div>
        </div>

        <div class="fixed bottom-4 right-4">
            <button @click="darkMode = darkMode === 'light' ? 'dark' : darkMode === 'dark' ? 'system' : 'light'" class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-xs" title="Cambiar tema">
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
        </div>
    </body>
</html>