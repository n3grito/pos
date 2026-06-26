<x-app-layout>
    <x-slot name="header">{{ __('Kanban - Ventas') }}</x-slot>
    <x-content-wrapper>
        <div class="mb-4 flex items-center justify-between">
            <form method="GET" action="{{ route('sales.kanban') }}" class="flex items-center gap-2">
                <x-text-input type="date" name="from" :value="request('from')" class="text-sm" />
                <span class="text-gray-400">-</span>
                <x-text-input type="date" name="to" :value="request('to')" class="text-sm" />
                <x-primary-button class="text-xs">{{ __('Filtrar') }}</x-primary-button>
                <a href="{{ route('sales.kanban') }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">{{ __('Limpiar') }}</a>
            </form>
            <a href="{{ route('sales.index') }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                {{ __('Vista Tabla') }}
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 rounded-xl">
                <div class="px-4 py-3 bg-green-50 dark:bg-green-900/30 border-b border-green-200 dark:border-green-800 rounded-t-xl flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-green-700 dark:text-green-400">{{ __('Completadas') }}</h3>
                    <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/50 px-2 py-0.5 rounded-full">{{ $completed->count() }}</span>
                </div>
                <div class="p-3 space-y-2 min-h-[200px]">
                    @forelse ($completed as $sale)
                        <a href="{{ route('sales.show', $sale) }}" class="block p-3 bg-white dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $sale->invoice_number }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $sale->created_at->format('d/m H:i') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $sale->client->name ?? __('Cliente General') }} · {{ currency($sale->total) }}
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-8">{{ __('No hay ventas completadas') }}</p>
                    @endforelse
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-red-200 dark:border-red-800 rounded-xl">
                <div class="px-4 py-3 bg-red-50 dark:bg-red-900/30 border-b border-red-200 dark:border-red-800 rounded-t-xl flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-red-700 dark:text-red-400">{{ __('Canceladas') }}</h3>
                    <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/50 px-2 py-0.5 rounded-full">{{ $cancelled->count() }}</span>
                </div>
                <div class="p-3 space-y-2 min-h-[200px]">
                    @forelse ($cancelled as $sale)
                        <a href="{{ route('sales.show', $sale) }}" class="block p-3 bg-white dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-lg hover:shadow-md transition-shadow opacity-75">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $sale->invoice_number }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $sale->created_at->format('d/m H:i') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $sale->client->name ?? __('Cliente General') }} · {{ currency($sale->total) }}
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-8">{{ __('No hay ventas canceladas') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
