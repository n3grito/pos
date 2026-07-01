<div class="mb-6">
    <div class="flex items-center justify-between">
        <div></div>
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                {{ __('Widgets') }}
            </button>
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-50" x-cloak>
                <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Widgets del Dashboard') }}</h4>
                </div>
                <div class="p-2 space-y-1 max-h-80 overflow-y-auto">
                    @foreach ($widgets as $key => $widget)
                        <label class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150">
                            <input type="checkbox" wire:click="toggle('{{ $key }}')" {{ $widget['enabled'] ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700 dark:checked:bg-indigo-500">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __($widget['label']) }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
