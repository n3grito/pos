<x-app-layout>
    <x-slot name="header">{{ __('Nueva Lista de Precio') }}</x-slot>
    <x-content-wrapper>
        <form method="POST" action="{{ route('price-lists.store') }}">
            @csrf
            <div class="mb-4">
                <x-input-label for="name" :value="__('Nombre')" />
                <x-text-input id="name" name="name" class="w-full mt-1" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div class="mb-4">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 dark:border-gray-600">
                    <span>{{ __('Lista por defecto') }}</span>
                </label>
            </div>
            <div class="mb-4">
                <h3 class="text-sm font-semibold mb-2">{{ __('Precios por Producto') }}</h3>
                <div class="max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Producto') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('SKU') }}</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Precio') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $product->name }}</td>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ $product->sku }}</td>
                                <td class="px-4 py-2">
                                    <input type="number" step="0.01" min="0" name="prices[{{ $product->id }}]" placeholder="{{ $product->selling_price }}" class="w-28 text-right text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ __('Dejar vacío para usar el precio de venta del producto.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                <x-secondary-button as="a" href="{{ route('price-lists.index') }}">{{ __('Cancelar') }}</x-secondary-button>
            </div>
        </form>
    </x-content-wrapper>
</x-app-layout>
