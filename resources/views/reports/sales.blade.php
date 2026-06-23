<x-app-layout>
    <x-slot name="header">
    {{ __('Reporte de Ventas') }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="from" :value="__('Fecha Inicio')" />
                            <x-text-input id="from" class="block mt-1 w-full" type="date" name="from" :value="request('from', now()->startOfMonth()->format('Y-m-d'))" />
                        </div>
                        <div>
                            <x-input-label for="to" :value="__('Fecha Fin')" />
                            <x-text-input id="to" class="block mt-1 w-full" type="date" name="to" :value="request('to', now()->format('Y-m-d'))" />
                        </div>
                        <div class="flex items-end space-x-2">
                            <x-primary-button>{{ __('Filtrar') }}</x-primary-button>
                            <a href="{{ route('reports.sales') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Hoy') }}
                            </a>
                        </div>
                    </form>
                </div>

                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Ventas') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totals->total_sales ?? 0 }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Monto Total') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ currency($totals->total_revenue ?? 0) }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Promedio por Venta') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ currency($totals->total_sales > 0 ? $totals->total_revenue / $totals->total_sales : 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Factura') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Cliente') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Usuario') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Método') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fecha') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($sales as $sale)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->invoice_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->client->name ?? __('Cliente General') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->user->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($sale->total) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $sale->payment_method === 'cash' ? __('Efectivo') : ($sale->payment_method === 'card' ? __('Tarjeta') : ($sale->payment_method === 'transfer' ? __('Transferencia') : __('Crédito'))) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay ventas en el período seleccionado') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($sales instanceof \Illuminate\Contracts\Pagination\Paginator && $sales->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>
    </x-content-wrapper>
</x-app-layout>