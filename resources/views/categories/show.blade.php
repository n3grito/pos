<x-app-layout>
    <x-slot name="header">
    {{ __('Categoría') }}: {{ $category->name }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nombre') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $category->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Creado') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Descripción') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $category->description ?? __('Sin descripción') }}</p>
                        </div>
                    </div>

                    @if ($category->products->count() > 0)
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Productos en esta categoría') }}</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nombre') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('SKU') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Precio') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Stock') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($category->products as $product)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->sku }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($product->selling_price) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->stock }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600">
                            {{ __('Editar') }}
                        </a>
                        <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>