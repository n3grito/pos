<x-app-layout>
    <x-slot name="header">
    {{ __('Nueva Promoción') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('promotions.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <x-input-label for="name" :value="__('Nombre')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Descripción')" />
                                <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="type" :value="__('Tipo')" />
                                <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">
                                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>{{ __('Porcentaje') }}</option>
                                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>{{ __('Monto Fijo') }}</option>
                                    <option value="bogo" {{ old('type') === 'bogo' ? 'selected' : '' }}>{{ __('BOGO') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="value" :value="__('Valor')" />
                                <x-text-input id="value" class="block mt-1 w-full" type="number" step="0.01" min="0" name="value" :value="old('value')" required />
                                <x-input-error :messages="$errors->get('value')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="min_amount" :value="__('Monto Mínimo')" />
                                <x-text-input id="min_amount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="min_amount" :value="old('min_amount', '0')" />
                                <x-input-error :messages="$errors->get('min_amount')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="min_quantity" :value="__('Cantidad Mínima')" />
                                <x-text-input id="min_quantity" class="block mt-1 w-full" type="number" min="0" name="min_quantity" :value="old('min_quantity', '0')" />
                                <x-input-error :messages="$errors->get('min_quantity')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="max_discount" :value="__('Descuento Máximo')" />
                                <x-text-input id="max_discount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="max_discount" :value="old('max_discount')" />
                                <x-input-error :messages="$errors->get('max_discount')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="applies_to" :value="__('Aplica a')" />
                                <select id="applies_to" name="applies_to" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300">
                                    <option value="all" {{ old('applies_to') === 'all' ? 'selected' : '' }}>{{ __('Todos los productos') }}</option>
                                    <option value="products" {{ old('applies_to') === 'products' ? 'selected' : '' }}>{{ __('Productos específicos') }}</option>
                                    <option value="groups" {{ old('applies_to') === 'groups' ? 'selected' : '' }}>{{ __('Grupos de clientes') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('applies_to')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="start_date" :value="__('Fecha de Inicio')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="end_date" :value="__('Fecha de Fin')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date', date('Y-m-d', strtotime('+30 days')))" required />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="usage_limit" :value="__('Límite de Usos')" />
                                <x-text-input id="usage_limit" class="block mt-1 w-full" type="number" min="0" name="usage_limit" :value="old('usage_limit')" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Dejar vacío para usos ilimitados') }}</p>
                                <x-input-error :messages="$errors->get('usage_limit')" class="mt-2" />
                            </div>

                            <div class="flex items-center mt-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Activa') }}</span>
                                </label>
                            </div>
                        </div>

                        <div id="products-section" class="mt-6 {{ old('applies_to') === 'products' ? '' : 'hidden' }}">
                            <x-input-label :value="__('Productos')" />
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-60 overflow-y-auto p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                                @foreach ($products as $product)
                                    <label class="flex items-center space-x-2 text-sm">
                                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $product->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div id="groups-section" class="mt-6 {{ old('applies_to') === 'groups' ? '' : 'hidden' }}">
                            <x-input-label :value="__('Grupos de Clientes')" />
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                                @foreach ($groups as $group)
                                    <label class="flex items-center space-x-2 text-sm">
                                        <input type="checkbox" name="group_ids[]" value="{{ $group->id }}" {{ in_array($group->id, old('group_ids', [])) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700">
                                        <span class="text-gray-700 dark:text-gray-300" style="color: {{ $group->color }}">{{ $group->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('promotions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>

    @push('scripts')
    <script>
        document.getElementById('applies_to').addEventListener('change', function() {
            document.getElementById('products-section').classList.toggle('hidden', this.value !== 'products');
            document.getElementById('groups-section').classList.toggle('hidden', this.value !== 'groups');
        });
    </script>
    @endpush
</x-app-layout>
