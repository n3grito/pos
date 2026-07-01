<x-app-layout>
    <x-slot name="header">
    {{ __('Nueva Compra') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <x-input-label for="supplier_id" :value="__('Proveedor')" />
                                <select id="supplier_id" name="supplier_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Seleccione un proveedor') }}</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="warehouse_id" :value="__('Almacén')" />
                                <select id="warehouse_id" name="warehouse_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Seleccione un almacén') }}</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="date" :value="__('Fecha')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Productos') }}</h4>
                                <button type="button" id="addRow" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                    + {{ __('Agregar Producto') }}
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="productsTable">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Producto') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cantidad') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Costo Unitario') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Subtotal') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Acción') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="productsBody">
                                        <tr class="product-row">
                                            <td class="px-4 py-2">
                                                <select name="details[0][product_id]" class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm product-select" required>
                                                    <option value="">{{ __('Seleccione un producto') }}</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->cost_price }}">{{ $product->name }} ({{ $product->sku }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input type="number" step="0.001" name="details[0][quantity]" class="block w-full quantity-input" value="1" min="0.001" required />
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input type="number" step="0.01" name="details[0][cost_price]" class="block w-full cost-price-input" value="0" required />
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 font-medium subtotal-cell">0.00</td>
                                            <td class="px-4 py-2">
                                                <button type="button" class="remove-row text-red-600 dark:text-red-400 hover:text-red-900 text-sm font-medium">{{ __('Eliminar') }}</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                                            <td colspan="3" class="px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Total') }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100" id="totalAmount">0.00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end mt-6 gap-2 sm:space-x-3 sm:gap-0">
                            <a href="{{ route('purchases.index') }}" class="text-center inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="w-full sm:w-auto justify-center">{{ __('Guardar Compra') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>

    @push('scripts')
    @php $productsData = $products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'cost_price' => $p->cost_price])->values(); @endphp
    <script nonce="{{ $cspNonce }}">
        const productsList = @json($productsData);

        function productOptionsHtml() {
            let html = '<option value="">{{ __("Seleccione un producto") }}</option>';
            productsList.forEach(function(p) {
                html += '<option value="' + p.id + '" data-price="' + p.cost_price + '">' + p.name + ' (' + p.sku + ')</option>';
            });
            return html;
        }

        let rowIndex = 1;

        document.getElementById('addRow').addEventListener('click', function() {
            const tbody = document.getElementById('productsBody');
            const newRow = document.createElement('tr');
            newRow.className = 'product-row';
            newRow.innerHTML = `
                <td class="px-4 py-2">
                    <select name="details[${rowIndex}][product_id]" class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm product-select" required>
                        ${productOptionsHtml()}
                    </select>
                </td>
                <td class="px-4 py-2">
                    <input type="number" step="0.001" name="details[${rowIndex}][quantity]" class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm quantity-input" value="1" min="0.001" required />
                </td>
                <td class="px-4 py-2">
                    <input type="number" step="0.01" name="details[${rowIndex}][cost_price]" class="block w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm cost-price-input" value="0" required />
                </td>
                <td class="px-4 py-2 text-sm text-gray-700 font-medium subtotal-cell">0.00</td>
                <td class="px-4 py-2">
                    <button type="button" class="remove-row text-red-600 hover:text-red-900 text-sm font-medium">{{ __('Eliminar') }}</button>
                </td>
            `;
            tbody.appendChild(newRow);
            rowIndex++;

            attachRowEvents(newRow);
        });

        function attachRowEvents(row) {
            const qtyInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.cost-price-input');
            const select = row.querySelector('.product-select');

            function updateSubtotal() {
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const subtotal = qty * price;
                row.querySelector('.subtotal-cell').textContent = subtotal.toFixed(2);
                updateTotal();
            }

            qtyInput.addEventListener('input', updateSubtotal);
            priceInput.addEventListener('input', updateSubtotal);

            select.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const price = selected.getAttribute('data-price');
                if (price) {
                    priceInput.value = price;
                    updateSubtotal();
                }
            });

            row.querySelector('.remove-row').addEventListener('click', function() {
                if (document.querySelectorAll('.product-row').length > 1) {
                    row.remove();
                    updateTotal();
                }
            });
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-cell').forEach(function(cell) {
                total += parseFloat(cell.textContent) || 0;
            });
            document.getElementById('totalAmount').textContent = total.toFixed(2);
        }

        document.querySelectorAll('.product-row').forEach(function(row) {
            attachRowEvents(row);
        });
    </script>
    @endpush
</x-app-layout>