<x-app-layout>
    <x-slot name="header">
    {{ __('Órdenes de Producción') }}
</x-slot>

    <x-content-wrapper>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Listado de Órdenes') }}</h3>
                    @can('production.create')
                    <a href="{{ route('production.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                        {{ __('Nueva Orden') }}
                    </a>
                    @endcan
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Producto') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cant. Plan') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cant. Prod.') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Estado') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Creado por') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fecha') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">#{{ $order->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $order->product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $order->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $order->produced_quantity ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusClasses = [
                                                'draft' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
                                                'pending' => 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200',
                                                'in_progress' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200',
                                                'completed' => 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200',
                                                'cancelled' => 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200',
                                            ];
                                            $statusLabels = [
                                                'draft' => 'Borrador',
                                                'pending' => 'Pendiente',
                                                'in_progress' => 'En Producción',
                                                'completed' => 'Completada',
                                                'cancelled' => 'Cancelada',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $order->user?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        @can('production.view')
                                        <a href="{{ route('production.show', $order) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">{{ __('Ver') }}</a>
                                        @endcan
                                        @can('production.update')
                                            @if (in_array($order->status, ['draft', 'pending']))
                                            <a href="{{ route('production.edit', $order) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">{{ __('Editar') }}</a>
                                            @endif
                                            @if ($order->status === 'draft' || $order->status === 'pending')
                                            <form action="{{ route('production.start', $order) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-900">{{ __('Iniciar') }}</button>
                                            </form>
                                            @endif
                                            @if ($order->status === 'in_progress')
                                            <a href="{{ route('production.complete-form', $order) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">{{ __('Completar') }}</a>
                                            @endif
                                        @endcan
                                        @can('production.cancel')
                                            @if (!in_array($order->status, ['completed', 'cancelled']))
                                            <form action="{{ route('production.cancel', $order) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Cancelar esta orden?') }}')">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Cancelar') }}</button>
                                            </form>
                                            @endif
                                        @endcan
                                        @can('production.delete')
                                            @if ($order->status !== 'completed')
                                            <form action="{{ route('production.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar esta orden?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Eliminar') }}</button>
                                            </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay órdenes de producción') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($orders->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
    </x-content-wrapper>
</x-app-layout>
