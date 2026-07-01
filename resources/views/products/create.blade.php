<x-app-layout>
    <x-slot name="header">
    {{ __('Nuevo Producto') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nombre')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sku" :value="__('SKU')" />
                                <x-text-input id="sku" class="block mt-1 w-full" type="text" name="sku" :value="old('sku')" disabled />
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Generado automáticamente al guardar') }}</p>
                                <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="barcode" :value="__('Código de Barras')" />
                                <x-text-input id="barcode" class="block mt-1 w-full" type="text" name="barcode" :value="old('barcode')" />
                                <x-input-error :messages="$errors->get('barcode')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="category_id" :value="__('Categoría')" />
                                <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Seleccione una categoría') }}</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="branch_id" :value="__('Sucursal')" />
                                <select id="branch_id" name="branch_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Seleccione una sucursal') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="unit_id" :value="__('Unidad de Medida')" />
                                <select id="unit_id" name="unit_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Seleccione una unidad') }}</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Descripción')" />
                                <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="cost_price" :value="__('Precio de Costo')" />
                                <x-text-input id="cost_price" class="block mt-1 w-full" type="number" step="0.01" name="cost_price" :value="old('cost_price', '0')" />
                                <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="selling_price" :value="__('Precio de Venta')" />
                                <x-text-input id="selling_price" class="block mt-1 w-full" type="number" step="0.01" name="selling_price" :value="old('selling_price', '0')" required />
                                <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="tax_percentage" :value="__('IVA (%)')" />
                                <x-text-input id="tax_percentage" class="block mt-1 w-full" type="number" step="0.01" min="0" max="100" name="tax_percentage" :value="old('tax_percentage', '0')" />
                                <x-input-error :messages="$errors->get('tax_percentage')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="stock" :value="__('Stock Inicial')" />
                                <x-text-input id="stock" class="block mt-1 w-full" type="number" step="0.001" name="stock" :value="old('stock', '0')" />
                                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="min_stock" :value="__('Stock Mínimo')" />
                                <x-text-input id="min_stock" class="block mt-1 w-full" type="number" step="0.001" name="min_stock" :value="old('min_stock', '0')" />
                                <x-input-error :messages="$errors->get('min_stock')" class="mt-2" />
                            </div>

                            <div>
                                <label class="inline-flex items-center gap-3 mt-6">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Producto Activo') }}</span>
                                </label>
                            </div>

                            <div>
                                <label class="inline-flex items-center gap-3 mt-6">
                                    <input type="hidden" name="available_for_sale" value="0">
                                    <input type="checkbox" name="available_for_sale" value="1" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('available_for_sale', '1') ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mostrar en carrito') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end mt-6 gap-2 sm:space-x-3 sm:gap-0">
                            <a href="{{ route('products.index') }}" class="text-center inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="w-full sm:w-auto justify-center">{{ __('Guardar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>