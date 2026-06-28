<x-app-layout>
    <x-slot name="header">{{ __('Entrada a Inventario') }}</x-slot>
    <x-content-wrapper>

        <div class="max-w-2xl mx-auto space-y-6">

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Transfiere productos del almacén al inventario disponible para la venta. Los productos se descuentan del almacén y se añaden al stock de venta.') }}</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('warehouses.transfer.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="warehouse_id" :value="__('Almacén de origen')" />
                                <select id="warehouse_id" name="warehouse_id" required class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Seleccionar almacén</option>
                                    @foreach ($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="reference" :value="__('Referencia (opcional)')" />
                                <x-text-input id="reference" class="block mt-1 w-full" type="text" name="reference" :value="old('reference')" placeholder="Ej: ENT-001" />
                                <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Notas (opcional)')" />
                                <textarea id="notes" name="notes" rows="2" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Productos a transferir') }}</h4>
                                <button type="button" id="addRow" class="px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/50 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">+ Agregar Producto</button>
                            </div>
                            <div id="itemsContainer" class="space-y-2">
                                <div class="flex items-center space-x-2 item-row">
                                    <select name="items[0][product_id]" required class="flex-1 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">Seleccionar producto</option>
                                    </select>
                                    <input type="number" name="items[0][quantity]" min="0.001" step="0.001" required placeholder="Cant." class="w-full sm:w-24 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <button type="button" class="removeRow p-1.5 text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        </div>

                        <div class="mt-8 flex items-center justify-end space-x-3">
                            <a href="{{ route('inventory.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Cancelar</a>
                            <x-primary-button>{{ __('Entrada a Inventario') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-content-wrapper>

    @push('scripts')
    <script nonce="{{ $cspNonce }}">
        let rowIndex = 1;

        document.getElementById('warehouse_id').addEventListener('change', function () {
            const warehouseId = this.value;
            const selects = document.querySelectorAll('[name$="[product_id]"]');
            selects.forEach(sel => {
                sel.innerHTML = '<option value="">Cargando...</option>';
            });
            if (!warehouseId) return;
            fetch('{{ url('warehouses') }}/' + warehouseId + '/products')
                .then(r => r.json())
                .then(data => {
                    selects.forEach(sel => {
                        sel.innerHTML = '<option value="">Seleccionar producto</option>';
                        data.forEach(item => {
                            sel.innerHTML += '<option value="' + item.product_id + '">' + item.product.name + ' (' + item.product.sku + ') - Stock: ' + item.quantity + '</option>';
                        });
                    });
                });
        });

        document.getElementById('addRow').addEventListener('click', function () {
            const container = document.getElementById('itemsContainer');
            const template = container.querySelector('.item-row').cloneNode(true);
            template.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
                if (el.tagName === 'INPUT') el.value = '';
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
            });
            const whId = document.getElementById('warehouse_id').value;
            if (whId) {
                const sel = template.querySelector('select');
                if (sel) {
                    fetch('{{ url('warehouses') }}/' + whId + '/products')
                        .then(r => r.json())
                        .then(data => {
                            sel.innerHTML = '<option value="">Seleccionar producto</option>';
                            data.forEach(item => {
                                sel.innerHTML += '<option value="' + item.product_id + '">' + item.product.name + ' (' + item.product.sku + ') - Stock: ' + item.quantity + '</option>';
                            });
                        });
                }
            }
            container.appendChild(template);
            rowIndex++;
        });

        document.getElementById('itemsContainer').addEventListener('click', function (e) {
            if (e.target.closest('.removeRow')) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) e.target.closest('.item-row').remove();
            }
        });
    </script>
    @endpush
</x-app-layout>