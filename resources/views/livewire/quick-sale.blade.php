<div
    x-data="{ show: @entangle('show') }"
    x-on:keydown.escape="if(show) show = false"
>
    <button @click="$wire.open()" class="relative inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition-colors gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
        </svg>
        {{ __('Venta Rápida') }}
    </button>

    <div x-show="show" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="fixed inset-0 bg-gray-900/70 dark:bg-gray-950/80 backdrop-blur-sm" @click="show = false"></div>
        <div class="relative w-full max-w-4xl mx-4 max-h-[90vh] flex flex-col bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    {{ __('Venta Rápida') }}
                </h2>
                <button @click="show = false" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                @if ($statusMessage)
                    <div class="mb-4 p-3 rounded-lg text-sm {{ str_starts_with($statusMessage, 'Error') ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' }}">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $statusMessage }}</span>
                            <button wire:click="$set('statusMessage', '')" class="ml-auto text-xs hover:underline">X</button>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <div class="lg:col-span-3 space-y-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <input type="text" wire:model.live="barcode" wire:keydown.enter="addByBarcode"
                                    placeholder="{{ __('Código de barras (ENTER)') }}"
                                    class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm dark:bg-gray-700 dark:text-gray-300 text-sm" />
                            </div>
                            <button wire:click="addByBarcode" class="p-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </button>
                        </div>

                        <div class="relative">
                            <input type="text" wire:model.live="search" placeholder="{{ __('Buscar producto por nombre, SKU...') }}"
                                class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm dark:bg-gray-700 dark:text-gray-300 text-sm" />
                            @if (strlen($search) >= 1 && $products->isNotEmpty())
                                <div class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-10 max-h-60 overflow-y-auto">
                                    @foreach ($products as $product)
                                        <button type="button" wire:click="selectProduct({{ $product->id }})"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 text-left transition-colors border-b border-gray-100 dark:border-gray-600 last:border-0">
                                            <div class="w-8 h-8 rounded bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xs font-bold shrink-0">
                                                {{ substr($product->name, 0, 2) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $product->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    SKU: {{ $product->sku }} @if($product->barcode) · {{ $product->barcode }} @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ currency($product->selling_price) }}</div>
                                                <div class="text-xs {{ $product->stock <= 0 ? 'text-red-500' : ($product->stock <= $product->min_stock ? 'text-yellow-500' : 'text-green-500') }}">
                                                    {{ $product->stock }} {{ __('disp.') }}
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 overflow-y-auto max-h-48">
                            @php
                                $topProducts = \App\Services\CacheService::topSaleProducts();
                            @endphp
                            @foreach ($topProducts as $product)
                                <button type="button" wire:click="selectProduct({{ $product['id'] }})"
                                    class="p-2 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-indigo-400 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all text-left active:scale-95">
                                    <div class="text-xs font-medium text-gray-900 dark:text-gray-100 truncate">{{ $product['name'] }}</div>
                                    <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ currency($product['selling_price']) }}</div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-3">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                {{ __('Carrito') }}
                                <span class="text-xs font-normal text-gray-400">({{ count($cart) }} {{ __('items') }})</span>
                            </h3>

                            <div class="max-h-48 overflow-y-auto space-y-1.5">
                                @forelse ($cart as $index => $item)
                                    <div class="flex items-center gap-2 p-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-medium text-gray-900 dark:text-gray-100 truncate">{{ $item['name'] }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ currency($item['price']) }} c/u</div>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                                class="w-6 h-6 flex items-center justify-center rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 text-xs font-bold">-</button>
                                            <input type="number" value="{{ $item['quantity'] }}" min="0.001" max="{{ $item['stock'] }}" step="0.001"
                                                wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                class="w-12 text-center text-xs border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300" />
                                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                                class="w-6 h-6 flex items-center justify-center rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 text-xs font-bold">+</button>
                                        </div>
                                        <div class="text-right min-w-[60px]">
                                            <div class="text-xs font-bold text-gray-900 dark:text-gray-100">{{ currency($item['subtotal']) }}</div>
                                        </div>
                                        <button wire:click="removeItem({{ $index }})" class="p-1 text-gray-400 hover:text-red-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-sm text-gray-400 dark:text-gray-500">
                                        <svg class="mx-auto w-8 h-8 mb-1 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                        {{ __('Carrito vacío') }}
                                    </div>
                                @endforelse
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-600 pt-3 space-y-2">
                                <div>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('Cliente') }}</span>
                                    <div class="flex items-center gap-1 mt-1">
                                        <select wire:model.live="clientId" class="flex-1 text-xs border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">
                                            <option value="">{{ __('Sin cliente') }}</option>
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }} {{ $client->points > 0 ? '(' . number_format($client->points) . ' pts)' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @if ($clientId)
                                    @php
                                        $selectedClient = $clients->firstWhere('id', $clientId);
                                    @endphp
                                    @if ($selectedClient && $selectedClient->points > 0)
                                        <div class="flex justify-between items-center text-purple-600 dark:text-purple-400">
                                            <span class="text-xs font-semibold">{{ __('Puntos disponibles') }}: {{ number_format($selectedClient->points) }} ({{ currency($selectedClient->points / \App\Services\LoyaltyService::REDEEM_RATE) }})</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs text-gray-600 dark:text-gray-400 shrink-0">{{ __('Canjear') }}</span>
                                            <input type="number" min="0" max="{{ $selectedClient->points }}" wire:model.live="pointsToRedeem"
                                                class="w-full text-xs border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300" placeholder="0" />
                                        </div>
                                    @endif
                                @endif

                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('Método de Pago') }}</span>
                                    <select wire:model.live="paymentMethod" class="text-xs border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">
                                        <option value="cash">{{ __('Efectivo') }}</option>
                                        <option value="card">{{ __('Tarjeta') }}</option>
                                        <option value="transfer">{{ __('Transferencia') }}</option>
                                    </select>
                                </div>

                                @if ($paymentMethod === 'cash')
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Recibido') }}</span>
                                        <input type="number" step="0.01" min="0" wire:model.live="amountPaid" placeholder="0.00"
                                            class="w-28 text-right text-xs border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300" />
                                    </div>
                                    @if ($amountPaid !== null && $amountPaid > 0)
                                        <div class="flex justify-between items-center {{ $change >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            <span class="text-xs font-semibold">{{ $change >= 0 ? __('Cambio') : __('Falta') }}</span>
                                            <span class="text-sm font-bold">{{ currency(abs($change ?? 0)) }}</span>
                                        </div>
                                    @endif
                                @endif

                                @if (in_array($paymentMethod, ['card', 'transfer']))
                                    <input type="text" wire:model="clientName" placeholder="{{ __('Nombre del cliente') }}"
                                        class="w-full text-xs border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300" />
                                    <input type="text" wire:model="clientNit" maxlength="11" placeholder="{{ __('NIT (11 dígitos)') }}"
                                        class="w-full text-xs border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300" />
                                    <input type="text" wire:model="paymentReference" placeholder="{{ __('Referencia de pago') }}"
                                        class="w-full text-xs border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300" />
                                @endif

                                @if ($appliedPromotionId && $promotionDiscount > 0)
                                    <div class="flex justify-between items-center text-green-600 dark:text-green-400">
                                        <span class="text-xs font-semibold">{{ __('Promoción') }}: {{ $appliedPromotionName }}</span>
                                        <span class="text-xs font-bold">-{{ currency($promotionDiscount) }}</span>
                                    </div>
                                @endif

                                @if ($clientId && $pointsToRedeem > 0)
                                    <div class="flex justify-between items-center text-purple-600 dark:text-purple-400">
                                        <span class="text-xs font-semibold">{{ __('Puntos canjeados') }}: {{ $pointsToRedeem }}</span>
                                        <span class="text-xs font-bold">-{{ currency($pointsDiscount) }}</span>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-600">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ __('Total') }}</span>
                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ currency($total ?? 0) }}</span>
                                </div>

                                <button x-data="{
                                    async doCheckout() {
                                        if (!navigator.onLine) {
                                            const data = await $wire.getCheckoutData();
                                            await pwa.queueSale(data);
                                            $wire.resetCart();
                                            window.dispatchEvent(new CustomEvent('notify', { detail: { message: '{{ __('Venta guardada sin conexión') }}', type: 'info' } }));
                                            return;
                                        }
                                        $wire.checkout();
                                    }
                                }" x-on:click.prevent="doCheckout()" wire:loading.attr="disabled"
                                    class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-lg transition-colors text-sm disabled:opacity-50 flex items-center justify-center gap-2">
                                    <svg wire:loading wire:target="checkout" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <span wire:loading.remove wire:target="checkout">{{ __('Completar Venta') }}</span>
                                    <span wire:loading wire:target="checkout">{{ __('Procesando...') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500">
                <span><kbd class="px-1 py-0.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 font-mono text-[10px]">ENTER</kbd> {{ __('Agregar por código de barras') }}</span>
                <span><kbd class="px-1 py-0.5 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 font-mono text-[10px]">ESC</kbd> {{ __('Cerrar') }}</span>
            </div>
        </div>
    </div>
</div>
