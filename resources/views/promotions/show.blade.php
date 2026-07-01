<x-app-layout>
    <x-slot name="header">
    {{ __('Promoción') }}: {{ $promotion->name }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nombre') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $promotion->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tipo') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $promotion->type === 'percentage' ? __('Porcentaje') : ($promotion->type === 'fixed' ? __('Monto Fijo') : __('BOGO')) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Valor') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $promotion->type === 'percentage' ? $promotion->value . '%' : currency($promotion->value) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aplica a') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if ($promotion->applies_to === 'all')
                                    {{ __('Todos los productos') }}
                                @elseif ($promotion->applies_to === 'products')
                                    {{ __('Productos específicos') }}
                                @else
                                    {{ __('Grupos de clientes') }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Vigencia') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $promotion->start_date->format('d/m/Y') }} - {{ $promotion->end_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Usos') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $promotion->used_count }}{{ $promotion->usage_limit ? ' / ' . $promotion->usage_limit : ' (ilimitado)' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estado') }}</h4>
                            <p class="mt-1">
                                @if ($promotion->is_active && $promotion->end_date >= today())
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400">{{ __('Activa') }}</span>
                                @elseif (!$promotion->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">{{ __('Inactiva') }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400">{{ __('Vencida') }}</span>
                                @endif
                            </p>
                        </div>
                        @if ($promotion->description)
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Descripción') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $promotion->description }}</p>
                        </div>
                        @endif
                        @if ($promotion->applies_to === 'products' && $promotion->products->isNotEmpty())
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Productos') }}</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($promotion->products as $product)
                                    <span class="px-2 py-1 text-xs bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 rounded-full">{{ $product->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if ($promotion->applies_to === 'groups' && $promotion->customerGroups->isNotEmpty())
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Grupos de Clientes') }}</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($promotion->customerGroups as $group)
                                    <span class="px-2 py-1 text-xs rounded-full" style="background-color: {{ $group->color }}20; color: {{ $group->color }}">{{ $group->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('promotions.edit', $promotion) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                            {{ __('Editar') }}
                        </a>
                        <a href="{{ route('promotions.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('Volver') }}
                        </a>
                    </div>
                </div>
            </div>

            @if ($promotion->sales->isNotEmpty())
                <div class="mt-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Ventas con esta promoción') }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Factura') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Descuento') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fecha') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($promotion->sales as $sale)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->invoice_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($sale->total) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($sale->discount_amount) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
    </x-content-wrapper>
</x-app-layout>
