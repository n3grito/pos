<x-app-layout>
    <x-slot name="header">
    {{ __('Completar Producción #:id', ['id' => $production->id]) }}
</x-slot>

    <x-content-wrapper>
            <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Producto: :name', ['name' => $production->product->name]) }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Cantidad planificada: :qty', ['qty' => $production->quantity]) }}</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('production.complete', $production) }}">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="produced_quantity" :value="__('Cantidad Real Producida')" />
                                <x-text-input id="produced_quantity" class="block mt-1 w-full" type="number" step="0.001" min="0.001" name="produced_quantity" :value="old('produced_quantity', $production->quantity)" required />
                                <x-input-error :messages="$errors->get('produced_quantity')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="warehouse_id" :value="__('Almacén de Destino')" />
                                <select id="warehouse_id" name="warehouse_id" required class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('Seleccione un almacén') }}</option>
                                    @foreach ($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('Insumos a consumir') }}</h5>
                                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    @foreach ($production->items as $item)
                                        <li class="flex justify-between">
                                            <span>{{ $item->product->name }}</span>
                                            <span class="font-medium">{{ $item->quantity }} (Stock: {{ $item->product->stock }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 space-x-3">
                            <a href="{{ route('production.show', $production) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>{{ __('Completar Producción') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>
