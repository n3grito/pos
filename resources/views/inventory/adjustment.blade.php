<x-app-layout>
    <x-slot name="header">
    {{ __('Ajuste de Inventario') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('inventory.adjustment.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                <x-input-label for="product_id" :value="__('Producto')" />
                                <select id="product_id" name="product_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Seleccione un producto') }}</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', $selectedProductId ?? '') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->sku }}) — Stock: {{ $product->stock }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="type" :value="__('Tipo de Ajuste')" />
                                <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Seleccione un tipo') }}</option>
                                    <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>{{ __('Entrada (añadir stock)') }}</option>
                                    <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>{{ __('Salida (reducir stock)') }}</option>
                                    <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>{{ __('Ajuste (fijar cantidad exacta)') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="quantity" :value="__('Cantidad')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" step="0.001" name="quantity" :value="old('quantity', 1)" min="0.001" required />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('Para "Entrada" o "Salida": cantidad a mover. Para "Ajuste": el stock final deseado.') }}
                                </p>
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Notas')" />
                                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('Motivo del ajuste...') }}">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Realizar Ajuste') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
