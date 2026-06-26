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
        <title>@yield('title') - {{ config('app.name') }}</title>
        @viteSafe(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center">
        <div class="text-center px-6 max-w-lg">
            @php $brand = \App\Models\ReceiptSetting::firstOrNew([]); @endphp
            @if ($brand->logo_path)
                <img src="{{ logo_url($brand->logo_path) }}" alt="{{ config('app.name') }}" class="h-16 w-auto mx-auto mb-6 opacity-80">
            @endif
            <div class="text-8xl font-bold text-red-500 dark:text-red-400 mb-4">@yield('code')</div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-2">@yield('message')</h1>
            <a href="{{ auth()->check() ? route('dashboard') : '/' }}" class="mt-8 inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Volver al inicio
            </a>
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
