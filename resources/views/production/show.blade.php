<x-app-layout>
    <x-slot name="header">
    {{ __('Orden de Producción #:id', ['id' => $production->id]) }}
</x-slot>

    <x-content-wrapper>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Detalles de la Orden') }}</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Producto') }}</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $production->product->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('SKU') }}</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $production->product->sku }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cantidad Planificada') }}</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $production->quantity }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Cantidad Producida') }}</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $production->produced_quantity ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Estado') }}</span>
                                <p>
                                    @php
                                        $statusLabels = ['draft' => 'Borrador', 'pending' => 'Pendiente', 'in_progress' => 'En Producción', 'completed' => 'Completada', 'cancelled' => 'Cancelada'];
                                        $statusClasses = ['draft' => 'bg-gray-100 text-gray-800', 'pending' => 'bg-yellow-100 text-yellow-800', 'in_progress' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-green-100 text-green-800', 'cancelled' => 'bg-red-100 text-red-800'];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$production->status] }}">
                                        {{ $statusLabels[$production->status] ?? $production->status }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Creado por') }}</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $production->user?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Creado') }}</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $production->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @if ($production->completed_at)
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Completado') }}</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $production->completed_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @endif
                        </div>
                        @if ($production->notes)
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Notas') }}</span>
                            <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $production->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Insumos Requeridos') }}</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Insumo') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('SKU') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Cantidad') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Stock Actual') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($production->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->product->sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $item->product->stock < $item->quantity ? 'text-red-600 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $item->product->stock }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Acciones') }}</h4>
                    <div class="space-y-3">
                        @can('production.update')
                            @if (in_array($production->status, ['draft', 'pending']))
                            <form action="{{ route('production.start', $production) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                    {{ __('Iniciar Producción') }}
                                </button>
                            </form>
                            <a href="{{ route('production.edit', $production) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                {{ __('Editar Orden') }}
                            </a>
                            @endif
                            @if ($production->status === 'in_progress')
                            <a href="{{ route('production.complete-form', $production) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                {{ __('Completar Producción') }}
                            </a>
                            @endif
                        @endcan
                        @can('production.cancel')
                            @if (!in_array($production->status, ['completed', 'cancelled']))
                            <form action="{{ route('production.cancel', $production) }}" method="POST" onsubmit="return confirm('{{ __('¿Cancelar esta orden de producción?') }}')">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                    {{ __('Cancelar Orden') }}
                                </button>
                            </form>
                            @endif
                        @endcan
                        <a href="{{ route('production.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                            {{ __('Volver al Listado') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
