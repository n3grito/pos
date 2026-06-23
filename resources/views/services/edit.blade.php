<x-app-layout>
    <x-slot name="header">{{ __('Editar Servicio') }}</x-slot>
    <x-content-wrapper>
        <div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-xl">
            <div class="p-6">
                <form method="POST" action="{{ route('services.update', $service) }}">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name" :value="__('Nombre del Servicio')" />
                            <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $service->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="description" :value="__('Descripción')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $service->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="selling_price" :value="__('Precio de Venta')" />
                                <x-text-input id="selling_price" name="selling_price" type="number" step="0.01" min="0" class="block mt-1 w-full" :value="old('selling_price', $service->selling_price)" required />
                                <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="tax_percentage" :value="__('IVA (%)')" />
                                <x-text-input id="tax_percentage" name="tax_percentage" type="number" step="0.01" min="0" max="100" class="block mt-1 w-full" :value="old('tax_percentage', $service->tax_percentage)" />
                                <x-input-error :messages="$errors->get('tax_percentage')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="category_id" :value="__('Categoría')" />
                                <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Sin categoría</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}" {{ old('category_id', $service->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $service->is_active ? 'checked' : '' }} />
                                <span class="ml-2 text-sm text-gray-700">{{ __('Activo') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-700">{{ __('Productos incluidos') }}</h4>
                            <button type="button" id="addProductRow" class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">+ Agregar Producto</button>
                        </div>
                        <div id="productsContainer" class="space-y-2">
                            @forelse ($service->products as $i => $p)
                            <div class="flex items-center space-x-2 product-row">
                                <select name="products[{{ $i }}][product_id]" required class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccionar producto</option>
                                    @foreach ($products as $prod)
                                        <option value="{{ $prod->id }}" {{ $p->id == $prod->id ? 'selected' : '' }}>{{ $prod->name }} ({{ $prod->sku }})</option>
                                    @endforeach
                                </select>
                                <input type="number" step="0.001" name="products[{{ $i }}][quantity]" min="0.001" required placeholder="Cant." class="w-20 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" value="{{ $p->pivot->quantity }}">
                                <button type="button" class="remove-row p-1.5 text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                            @empty
                            <div class="flex items-center space-x-2 product-row">
                                <select name="products[0][product_id]" required class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                    <option value="">Seleccionar producto</option>
                                    @foreach ($products as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->sku }})</option>
                                    @endforeach
                                </select>
                                <input type="number" step="0.001" name="products[0][quantity]" min="0.001" value="1" required placeholder="Cant." class="w-20 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <button type="button" class="remove-row p-1.5 text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                            @endforelse
                        </div>
                        <x-input-error :messages="$errors->get('products')" class="mt-2" />
                    </div>

                    <div class="mt-8 flex items-center justify-end space-x-3">
                        <a href="{{ route('services.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancelar</a>
                        <x-primary-button>{{ __('Actualizar Servicio') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </x-content-wrapper>

    @push('scripts')
    <script>
        let rowIdx = {{ max($service->products->count(), 1) }};
        document.getElementById('addProductRow').addEventListener('click', function () {
            const container = document.getElementById('productsContainer');
            const row = container.querySelector('.product-row').cloneNode(true);
            row.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, '[' + rowIdx + ']');
                if (el.tagName === 'INPUT') el.value = '1';
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
            });
            container.appendChild(row);
            rowIdx++;
        });
        document.getElementById('productsContainer').addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                const rows = document.querySelectorAll('.product-row');
                if (rows.length > 1) e.target.closest('.product-row').remove();
            }
        });
    </script>
    @endpush
</x-app-layout>
