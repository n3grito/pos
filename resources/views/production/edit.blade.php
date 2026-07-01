<x-app-layout>
    <x-slot name="header">
    {{ __('Editar Orden de Producción #:id', ['id' => $production->id]) }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('production.update', $production) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="product_id" :value="__('Producto a Producir')" />
                                <select id="product_id" name="product_id" required class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Seleccione un producto') }}</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', $production->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="quantity" :value="__('Cantidad a Producir')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" step="0.001" min="0.001" name="quantity" :value="old('quantity', $production->quantity)" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="notes" :value="__('Notas')" />
                                <textarea id="notes" name="notes" rows="2" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $production->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Materia Prima / Insumos') }}</h4>
                                <button type="button" id="addMaterialRow" class="px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50">
                                    + {{ __('Agregar Insumo') }}
                                </button>
                            </div>
                            <div id="materialsContainer" class="space-y-2">
                                @forelse ($production->items as $i => $item)
                                <div class="flex items-center space-x-2 material-row">
                                    <select name="items[{{ $i }}][product_id]" required class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">{{ __('Seleccionar insumo') }}</option>
                                        @foreach ($rawMaterials as $mat)
                                            <option value="{{ $mat->id }}" {{ old("items.{$i}.product_id", $item->product_id) == $mat->id ? 'selected' : '' }}>
                                                {{ $mat->name }} ({{ $mat->sku }}) — Stock: {{ $mat->stock }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number" step="0.001" name="items[{{ $i }}][quantity]" min="0.001" value="{{ old("items.{$i}.quantity", $item->quantity) }}" required placeholder="{{ __('Cant.') }}"
                                        class="w-full sm:w-24 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <button type="button" class="remove-row p-1.5 text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                @empty
                                <div class="flex items-center space-x-2 material-row">
                                    <select name="items[0][product_id]" required class="flex-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">{{ __('Seleccionar insumo') }}</option>
                                        @foreach ($rawMaterials as $mat)
                                            <option value="{{ $mat->id }}">{{ $mat->name }} ({{ $mat->sku }}) — Stock: {{ $mat->stock }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" step="0.001" name="items[0][quantity]" min="0.001" value="1" required placeholder="{{ __('Cant.') }}"
                                        class="w-full sm:w-24 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <button type="button" class="remove-row p-1.5 text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                @endforelse
                            </div>
                            <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-8 space-x-3">
                            <a href="{{ route('production.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Actualizar Orden') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>

    @push('scripts')
    <script nonce="{{ $cspNonce }}">
        let rowIdx = {{ max($production->items->count(), 1) }};
        document.getElementById('addMaterialRow').addEventListener('click', function () {
            const container = document.getElementById('materialsContainer');
            const row = container.querySelector('.material-row').cloneNode(true);
            row.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, '[' + rowIdx + ']');
                if (el.tagName === 'INPUT') el.value = '1';
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
            });
            container.appendChild(row);
            rowIdx++;
        });
        document.getElementById('materialsContainer').addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                const rows = document.querySelectorAll('.material-row');
                if (rows.length > 1) e.target.closest('.material-row').remove();
            }
        });
    </script>
    @endpush
</x-app-layout>
