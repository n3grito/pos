<div
    x-data="{
        show: @entangle('show'),
        query: @entangle('query'),
        selectedIndex: @entangle('selectedIndex'),
        focused: false,
        resultsCount: 0,
        init() {
            this.$watch('show', val => {
                if (val) this.$nextTick(() => this.$refs.searchInput?.focus());
            });
            this.$watch('query', () => { this.selectedIndex = 0; });
        },
        onKeydown(e) {
            if (!this.show) return;
            const items = this.$el.querySelectorAll('[data-search-result]');
            if (e.key === 'ArrowDown') { e.preventDefault(); this.selectedIndex = Math.min(this.selectedIndex + 1, items.length - 1); this.scrollToItem(items); }
            if (e.key === 'ArrowUp') { e.preventDefault(); this.selectedIndex = Math.max(this.selectedIndex - 1, 0); this.scrollToItem(items); }
            if (e.key === 'Enter' && items[this.selectedIndex]) { items[this.selectedIndex].click(); }
        },
        scrollToItem(items) {
            if (items[this.selectedIndex]) items[this.selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }"
    x-on:keydown.window.prevent.cmd.k="show = true"
    x-on:keydown.window.prevent.ctrl.k="show = true"
    x-on:keydown.window="onKeydown($event)"
    x-on:keydown.escape.window="if(show){show=false; query=''}"
    x-cloak
>
    <button @click="show = true" class="p-2 text-gray-400 dark:text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative group" title="{{ __('Buscar (Ctrl+K)') }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 hidden group-hover:block text-[10px] bg-gray-800 text-white px-1.5 py-0.5 rounded whitespace-nowrap">Ctrl+K</span>
    </button>

    <div x-show="show" class="fixed inset-0 z-50 flex items-start justify-center pt-[15vh]" @keydown.escape="show = false; query = ''">
        <div class="fixed inset-0 bg-gray-900/60 dark:bg-gray-950/80 backdrop-blur-sm" @click="show = false; query = ''"></div>
        <div class="relative w-full max-w-xl mx-4 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden" @click.outside="show = false; query = ''">
            <div class="relative">
                <svg class="absolute left-5 top-4 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    x-ref="searchInput"
                    type="text"
                    x-model="query"
                    wire:model.live.debounce.250ms="query"
                    @focus="focused = true"
                    @blur="focused = false"
                    placeholder="{{ __('Buscar productos, clientes, ventas...') }}"
                    class="w-full pl-12 pr-12 py-4 bg-transparent border-0 border-b border-gray-200 dark:border-gray-700 focus:ring-0 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 text-base"
                />
                <div x-show="query.length > 0" class="absolute right-4 top-3.5">
                    <button @click="query = ''; $refs.searchInput.focus()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="max-h-96 overflow-y-auto p-2" wire:loading.class="opacity-50" wire:target="query">
                <div wire:loading wire:target="query" class="flex items-center justify-center py-6">
                    <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>

                <div wire:loading.remove wire:target="query">
                    @if (strlen($query) >= 2)
                        @php
                            $idx = 0;
                            $hasResults = $results['products']->isNotEmpty() || $results['clients']->isNotEmpty() || $results['sales']->isNotEmpty() || $results['suppliers']->isNotEmpty() || $categories->isNotEmpty();
                        @endphp

                        @if (!$hasResults)
                            <div class="p-6 text-center">
                                <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sin resultados para ":query"', ['query' => $query]) }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Intente con otros términos') }}</p>
                            </div>
                        @else
                            @if ($results['products']->isNotEmpty())
                                <div class="px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    {{ __('Productos') }}
                                </div>
                                @foreach ($results['products'] as $product)
                                    <button data-search-result type="button" wire:click="selectResult('product', {{ $product->id }})"
                                        class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg transition-colors text-left"
                                        x-bind:class="selectedIndex === {{ $idx++ }} ? 'bg-indigo-50 dark:bg-indigo-900/40' : 'hover:bg-gray-100 dark:hover:bg-gray-700'">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/50 dark:to-indigo-800/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xs font-bold shrink-0">
                                            {{ substr($product->name, 0, 2) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $product->name }}</div>
                                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                @if ($product->sku)<span>{{ $product->sku }}</span>@endif
                                                @if ($product->category)<span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700">{{ $product->category->name }}</span>@endif
                                                <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ currency($product->selling_price) }}</span>
                                            </div>
                                        </div>
                                        <span class="text-xs {{ $product->stock <= 0 ? 'text-red-500' : ($product->stock <= $product->min_stock ? 'text-yellow-500' : 'text-green-500') }} font-medium shrink-0">{{ $product->stock }}</span>
                                    </button>
                                @endforeach
                            @endif

                            @if ($results['sales']->isNotEmpty())
                                <div class="px-3 py-2 mt-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                    {{ __('Ventas') }}
                                </div>
                                @foreach ($results['sales'] as $sale)
                                    <button data-search-result type="button" wire:click="selectResult('sale', {{ $sale->id }})"
                                        class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg transition-colors text-left"
                                        x-bind:class="selectedIndex === {{ $idx++ }} ? 'bg-indigo-50 dark:bg-indigo-900/40' : 'hover:bg-gray-100 dark:hover:bg-gray-700'">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/50 dark:to-green-800/50 flex items-center justify-center text-green-600 dark:text-green-400 text-xs font-bold shrink-0">
                                            #{{ substr($sale->invoice_number, -4) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->invoice_number }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $sale->client?->name ?? $sale->client_name ?? __('Cliente General') }} · {{ currency($sale->total) }}</div>
                                        </div>
                                        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $sale->status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400' }}">
                                            {{ $sale->status }}
                                        </span>
                                    </button>
                                @endforeach
                            @endif

                            @if ($results['clients']->isNotEmpty())
                                <div class="px-3 py-2 mt-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ __('Clientes') }}
                                </div>
                                @foreach ($results['clients'] as $client)
                                    <button data-search-result type="button" wire:click="selectResult('client', {{ $client->id }})"
                                        class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg transition-colors text-left"
                                        x-bind:class="selectedIndex === {{ $idx++ }} ? 'bg-indigo-50 dark:bg-indigo-900/40' : 'hover:bg-gray-100 dark:hover:bg-gray-700'">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/50 dark:to-purple-800/50 flex items-center justify-center text-purple-600 dark:text-purple-400 text-xs font-bold shrink-0">
                                            {{ substr($client->name, 0, 2) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $client->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $client->nit ?? '' }} @if($client->phone) · {{ $client->phone }} @endif</div>
                                        </div>
                                    </button>
                                @endforeach
                            @endif

                            @if ($results['suppliers']->isNotEmpty())
                                <div class="px-3 py-2 mt-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    {{ __('Proveedores') }}
                                </div>
                                @foreach ($results['suppliers'] as $supplier)
                                    <button data-search-result type="button" wire:click="selectResult('supplier', {{ $supplier->id }})"
                                        class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg transition-colors text-left"
                                        x-bind:class="selectedIndex === {{ $idx++ }} ? 'bg-indigo-50 dark:bg-indigo-900/40' : 'hover:bg-gray-100 dark:hover:bg-gray-700'">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $supplier->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $supplier->contact_name ?? '' }}</div>
                                        </div>
                                    </button>
                                @endforeach
                            @endif

                            @if ($categories->isNotEmpty())
                                <div class="px-3 py-2 mt-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ __('Categorías') }}</div>
                                @foreach ($categories as $category)
                                    <div class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        {{ $category->name }}
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    @else
                        <div class="p-6 text-center">
                            <svg class="mx-auto w-12 h-12 text-gray-200 dark:text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Presione Ctrl+K para buscar') }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Busque productos por nombre, SKU o código de barras') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="px-4 py-2.5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                <div class="flex items-center gap-3">
                    <span><kbd class="px-1.5 py-0.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 font-mono text-[10px]">↑↓</kbd> {{ __('Navegar') }}</span>
                    <span><kbd class="px-1.5 py-0.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 font-mono text-[10px]">↵</kbd> {{ __('Seleccionar') }}</span>
                    <span><kbd class="px-1.5 py-0.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 font-mono text-[10px]">ESC</kbd> {{ __('Cerrar') }}</span>
                </div>
                @if ($totalCount > 5)
                    <span>{{ $totalCount }}+ {{ __('resultados') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
