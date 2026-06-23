<x-app-layout>
    <x-slot name="header">
    {{ __('Movimientos de Inventario') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Historial de Movimientos') }}</h3>
                </div>

                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <form method="GET" class="flex flex-wrap items-center gap-3">
                        <div class="w-48">
                            <select name="product_id" class="block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('Todos los productos') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-36">
                            <select name="type" class="block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('Todos los tipos') }}</option>
                                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>{{ __('Entrada') }}</option>
                                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>{{ __('Salida') }}</option>
                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>{{ __('Transferencia') }}</option>
                                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>{{ __('Ajuste') }}</option>
                                <option value="warehouse_entry" {{ request('type') == 'warehouse_entry' ? 'selected' : '' }}>{{ __('Entrada Almacén') }}</option>
                            </select>
                        </div>
                        <div>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="block w-40 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="block w-40 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 dark:hover:bg-gray-400">
                            {{ __('Filtrar') }}
                        </button>
                        @if(request()->anyFilled(['product_id', 'type', 'date_from', 'date_to']))
                            <a href="{{ route('inventory.movements') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">{{ __('Limpiar') }}</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fecha') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Producto') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tipo') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cantidad') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Referencia') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Usuario') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Notas') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($movements as $movement)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('inventory.show', $movement->product) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">{{ $movement->product->name }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $typeLabels = [
                                                'in' => ['label' => 'Entrada', 'class' => 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400'],
                                                'out' => ['label' => 'Salida', 'class' => 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400'],
                                                'transfer' => ['label' => 'Transferencia', 'class' => 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400'],
                                                'adjustment' => ['label' => 'Ajuste', 'class' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400'],
                                                'warehouse_entry' => ['label' => 'Entrada Almacén', 'class' => 'bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-400'],
                                                'warehouse_exit' => ['label' => 'Salida Almacén', 'class' => 'bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-400'],
                                            ];
                                            $typeInfo = $typeLabels[$movement->type] ?? ['label' => ucfirst($movement->type), 'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeInfo['class'] }}">{{ __($typeInfo['label']) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ in_array($movement->type, ['out', 'warehouse_exit']) ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                        {{ in_array($movement->type, ['out', 'warehouse_exit']) ? '-' : '+' }}{{ $movement->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->reference ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $movement->user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $movement->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay movimientos registrados') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($movements->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $movements->links() }}
                    </div>
                @endif
            </div>
    </x-content-wrapper>
</x-app-layout>
