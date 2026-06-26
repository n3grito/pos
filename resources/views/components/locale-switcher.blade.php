@php
    $locales = [
        'es_MX' => 'ES',
        'en_US' => 'US',
        'en_GB' => 'GB',
    ];
    $current = app()->getLocale();
@endphp
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center gap-1 px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
        <span class="font-semibold uppercase">{{ substr($current, -2) }}</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" @click.outside="open = false" x-transition x-cloak class="absolute right-0 mt-1 w-36 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg overflow-hidden">
        @foreach ($locales as $key => $flag)
            <a href="{{ route('locale.switch', $key) }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium {{ $current === $key ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                <span class="font-semibold uppercase">{{ $flag }}</span>
                {{ $key === 'es_MX' ? 'Español' : ($key === 'en_US' ? 'English US' : 'English UK') }}
            </a>
        @endforeach
    </div>
</div>
