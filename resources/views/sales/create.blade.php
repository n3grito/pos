<x-app-layout>
    <x-slot name="header">
    {{ __('Nueva Venta') }}
</x-slot>

    <x-content-wrapper>
            <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left: Items Selection -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div class="p-4">
                            <div class="mb-3">
                                <x-text-input id="itemSearch" class="block w-full" type="text" placeholder="{{ __('Buscar producto o servicio...') }}" />
                            </div>

                            <div class="max-h-[calc(100vh-280px)] overflow-y-auto">
                                <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-4 xl:grid-cols-5 gap-1.5" id="itemsList">
                                    @foreach ($items as $item)
                                        <div class="item-card border border-gray-200 dark:border-gray-600 rounded-md p-1.5 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-sm transition-all cursor-pointer active:scale-95 select-none"
                                             data-type="{{ $item->type }}"
                                             data-name="{{ strtolower($item->name) }}"
                                             data-sku="{{ $item->sku ?? '' }}"
                                             data-id="{{ $item->id }}"
                                             data-price="{{ $item->price }}"
                                             data-stock="{{ $item->stock ?? 999999 }}"
                                             data-products-json="{{ $item->products_json ?? '[]' }}">
                                            <div class="font-semibold text-[11px] text-gray-900 dark:text-gray-100 leading-tight truncate">{{ $item->name }}</div>
                                            @if ($item->type === 'product')
                                                <div class="text-[9px] text-gray-400 truncate">{{ $item->sku }}</div>
                                                <div class="flex items-center justify-between mt-0.5">
                                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ currency($item->price) }}</span>
                                                    <span class="text-[9px] px-1 py-0.5 rounded-full leading-none {{ $item->stock <= 0 ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400' : ($item->stock <= $item->min_stock ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400' : 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400') }}">
                                                        {{ $item->stock }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="text-[9px] text-blue-500 font-medium truncate">Srv · {{ $item->components_count }} prod.</div>
                                                <div class="flex items-center justify-between mt-0.5">
                                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ currency($item->price) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Cart & Payment -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Carrito de Venta') }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="client_id" :value="__('Cliente')" />
                                    <select id="client_id" name="client_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">{{ __('Cliente General') }}</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="payment_method" :value="__('Método de Pago')" />
                                    <select id="payment_method" name="payment_method" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="cash">{{ __('Efectivo') }}</option>
                                        <option value="card">{{ __('Tarjeta') }}</option>
                                        <option value="transfer">{{ __('Transferencia') }}</option>
                                        <option value="credit">{{ __('Crédito') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="cash_register_session_id" :value="__('Sesión de Caja')" />
                                    <select id="cash_register_session_id" name="cash_register_session_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">{{ __('Sin sesión') }}</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session->id }}">{{ $session->cashRegister->name }} - {{ $session->opening_date?->format('d/m/Y H:i') ?? $session->created_at->format('d/m/Y H:i') }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('cash_register_session_id')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Payment fields (dynamic) -->
                            <div id="cashFields" class="mb-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 hidden">
                                <div>
                                    <x-input-label for="amount_paid" :value="__('Monto recibido')" />
                                    <x-text-input id="amount_paid" name="amount_paid" type="number" step="0.01" min="0" class="block mt-1 w-full" placeholder="0.00" value="{{ old('amount_paid') }}" />
                                    <x-input-error :messages="$errors->get('amount_paid')" class="mt-2" />
                                </div>
                                <div id="changeDisplay" class="mt-2 text-sm hidden">
                                    <span class="font-semibold">{{ __('Cambio') }}:</span>
                                    <span id="changeAmount" class="font-bold text-lg text-green-600 dark:text-green-400">$0.00</span>
                                </div>
                            </div>

                            <div id="cardTransferFields" class="mb-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 hidden">
                                <div class="space-y-3">
                                    <div>
                                        <x-input-label for="client_name" :value="__('Nombre del Cliente')" />
                                        <x-text-input id="client_name" name="client_name" type="text" class="block mt-1 w-full" placeholder="{{ __('Nombre y apellidos') }}" value="{{ old('client_name') }}" />
                                        <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="client_nit" :value="__('NIT (11 dígitos)')" />
                                        <x-text-input id="client_nit" name="client_nit" type="text" maxlength="11" class="block mt-1 w-full" placeholder="00000000000" value="{{ old('client_nit') }}" inputmode="numeric" />
                                        <x-input-error :messages="$errors->get('client_nit')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="payment_reference" :value="__('Código de Transferencia / Autorización')" />
                                        <x-text-input id="payment_reference" name="payment_reference" type="text" class="block mt-1 w-full" placeholder="{{ __('Código de transacción') }}" value="{{ old('payment_reference') }}" />
                                        <x-input-error :messages="$errors->get('payment_reference')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Cart Items -->
                            <div class="overflow-x-auto mb-4 max-h-64 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Producto') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Cant.') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Precio') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Subtotal') }}</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="cartItems">
                                        <tr id="emptyCart">
                                            <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('Carrito vacío') }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                                            <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Total') }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100" id="cartTotal">0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <input type="hidden" name="items" id="itemsInput" value="[]">

                            <div class="flex justify-end space-x-3">
                                <button type="button" id="clearCart" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                    {{ __('Limpiar') }}
                                </button>
                                <x-primary-button id="submitSale">{{ __('Completar Venta') }}</x-primary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    </x-content-wrapper>

    @push('scripts')
    <script>
        let cart = [];

        document.getElementById('itemSearch').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.item-card').forEach(function(card) {
                const name = card.getAttribute('data-name');
                const sku = card.getAttribute('data-sku');
                card.style.display = (!query || name.includes(query) || (sku && sku.includes(query))) ? '' : 'none';
            });
        });

        document.querySelectorAll('.item-card').forEach(function(card) {
            card.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                const id = parseInt(this.getAttribute('data-id'));
                const name = this.getAttribute('data-name');
                const price = parseFloat(this.getAttribute('data-price'));

                if (type === 'service') {
                    const products = JSON.parse(this.getAttribute('data-products-json') || '[]');
                    if (!products.length) {
                        alert('{{ __("Este servicio no tiene productos asignados") }}');
                        return;
                    }
                    const existing = cart.find(item => item.type === 'service' && item.service_id === id);
                    if (existing) {
                        existing.quantity++;
                    } else {
                        cart.push({
                            type: 'service',
                            service_id: id,
                            name: name,
                            price: price,
                            quantity: 1,
                            products: products,
                        });
                    }
                } else {
                    const stock = parseFloat(this.getAttribute('data-stock'));
                    const existing = cart.find(item => item.type === 'product' && item.product_id === id);
                    if (existing) {
                        if (existing.quantity < stock) {
                            existing.quantity++;
                        } else {
                            alert('{{ __("Stock insuficiente") }}');
                        }
                    } else {
                        if (stock > 0) {
                            cart.push({ type: 'product', product_id: id, name: name, price: price, quantity: 1, stock: stock });
                        } else {
                            alert('{{ __("Producto sin stock") }}');
                        }
                    }
                }
                renderCart();
            });
        });

        function renderCart() {
            const tbody = document.getElementById('cartItems');
            tbody.innerHTML = '';

            if (cart.length === 0) {
                tbody.innerHTML = '<tr id="emptyCart"><td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">{{ __("Carrito vacío") }}</td></tr>';
                document.getElementById('cartTotal').textContent = '0.00';
                document.getElementById('itemsInput').value = '[]';
                updateChangeDisplay(0);
                return;
            }

            let total = 0;
            cart.forEach(function(item, index) {
                const subtotal = item.quantity * item.price;
                total += subtotal;
                const tr = document.createElement('tr');

                if (item.type === 'service') {
                    tr.innerHTML = `
                        <td class="px-4 py-2 text-sm text-gray-900">
                            ${item.name}
                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium bg-blue-100 text-blue-700">Srv</span>
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" step="0.001" class="block w-full sm:w-16 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm cart-qty" value="${item.quantity}" min="0.001" max="999999" data-index="${index}" />
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">${item.price.toFixed(2)}</td>
                        <td class="px-4 py-2 text-sm text-gray-700 font-medium">${subtotal.toFixed(2)}</td>
                        <td class="px-4 py-2">
                            <button type="button" class="remove-item text-red-600 hover:text-red-900 text-sm font-medium" data-index="${index}">x</button>
                        </td>
                    `;
                } else {
                    tr.innerHTML = `
                        <td class="px-4 py-2 text-sm text-gray-900">${item.name}</td>
                        <td class="px-4 py-2">
                            <input type="number" step="0.001" class="block w-full sm:w-16 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm cart-qty" value="${item.quantity}" min="0.001" max="${item.stock}" data-index="${index}" />
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">${item.price.toFixed(2)}</td>
                        <td class="px-4 py-2 text-sm text-gray-700 font-medium">${subtotal.toFixed(2)}</td>
                        <td class="px-4 py-2">
                            <button type="button" class="remove-item text-red-600 hover:text-red-900 text-sm font-medium" data-index="${index}">x</button>
                        </td>
                    `;
                }
                tbody.appendChild(tr);
            });

            document.getElementById('cartTotal').textContent = total.toFixed(2);
            document.getElementById('itemsInput').value = JSON.stringify(cart.map(i => {
                if (i.type === 'service') {
                    return { service_id: i.service_id, quantity: i.quantity, price: i.price, products: i.products };
                }
                return { product_id: i.product_id, quantity: i.quantity, price: i.price };
            }));

            updateChangeDisplay(total);

            document.querySelectorAll('.cart-qty').forEach(function(input) {
                input.addEventListener('change', function() {
                    const idx = parseInt(this.getAttribute('data-index'));
                    const item = cart[idx];
                    let maxQty = item.type === 'service' ? 999999 : item.stock;
                    const val = parseFloat(this.value);
                    if (val > 0 && val <= maxQty) {
                        cart[idx].quantity = val;
                    } else {
                        this.value = cart[idx].quantity;
                        if (val > maxQty) alert('{{ __("Stock insuficiente") }}');
                    }
                    renderCart();
                });
            });

            document.querySelectorAll('.remove-item').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const idx = parseInt(this.getAttribute('data-index'));
                    cart.splice(idx, 1);
                    renderCart();
                });
            });
        }

        // Payment method toggling
        const methodSelect = document.getElementById('payment_method');
        const cashFields = document.getElementById('cashFields');
        const cardTransferFields = document.getElementById('cardTransferFields');
        const amountPaidInput = document.getElementById('amount_paid');
        const changeDisplay = document.getElementById('changeDisplay');
        const changeAmount = document.getElementById('changeAmount');

        function togglePaymentFields() {
            const method = methodSelect.value;
            cashFields.classList.toggle('hidden', method !== 'cash');
            cardTransferFields.classList.toggle('hidden', !['card', 'transfer'].includes(method));

            document.getElementById('client_name').required = ['card', 'transfer'].includes(method);
            document.getElementById('client_nit').required = ['card', 'transfer'].includes(method);
            document.getElementById('payment_reference').required = ['card', 'transfer'].includes(method);

            if (method !== 'cash') {
                changeDisplay.classList.add('hidden');
            } else {
                updateChangeDisplay(parseFloat(document.getElementById('cartTotal').textContent));
            }
        }

        function updateChangeDisplay(total) {
            const method = methodSelect.value;
            if (method !== 'cash') {
                changeDisplay.classList.add('hidden');
                return;
            }
            const paid = parseFloat(amountPaidInput.value) || 0;
            const change = paid - total;
            if (paid > 0) {
                changeDisplay.classList.remove('hidden');
                if (change >= 0) {
                    changeAmount.textContent = '$' + change.toFixed(2);
                    changeAmount.className = 'font-bold text-lg text-green-600';
                } else {
                    changeAmount.textContent = '$' + Math.abs(change).toFixed(2) + ' ({{ __("falta") }})';
                    changeAmount.className = 'font-bold text-lg text-red-600';
                }
            } else {
                changeDisplay.classList.add('hidden');
            }
        }

        methodSelect.addEventListener('change', togglePaymentFields);
        amountPaidInput.addEventListener('input', function() {
            updateChangeDisplay(parseFloat(document.getElementById('cartTotal').textContent));
        });

        // Client name NIT validation
        document.getElementById('client_nit').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 11);
        });

        // Initial state
        togglePaymentFields();

        document.getElementById('clearCart').addEventListener('click', function() {
            cart = [];
            renderCart();
        });

        document.getElementById('saleForm').addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('{{ __("Agregue al menos un producto al carrito") }}');
                return;
            }

            const method = methodSelect.value;
            if (method === 'cash') {
                const total = parseFloat(document.getElementById('cartTotal').textContent);
                const paid = parseFloat(amountPaidInput.value) || 0;
                if (paid < total) {
                    e.preventDefault();
                    alert('{{ __("El monto recibido es menor que el total") }}');
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
