<x-app-layout>
    <x-slot name="header">{{ __('Reporte de Ventas') }}</x-slot>
    <x-content-wrapper>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <div>
                        <x-input-label for="from" :value="__('Fecha Inicio')" />
                        <x-text-input id="from" class="block mt-1 w-full" type="date" name="from" :value="request('from', now()->startOfMonth()->format('Y-m-d'))" />
                    </div>
                    <div>
                        <x-input-label for="to" :value="__('Fecha Fin')" />
                        <x-text-input id="to" class="block mt-1 w-full" type="date" name="to" :value="request('to', now()->format('Y-m-d'))" />
                    </div>
                    <div>
                        <x-input-label for="payment_method" :value="__('Método')" />
                        <select id="payment_method" name="payment_method" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 text-sm">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach ($paymentMethods as $val => $label)
                                <option value="{{ $val }}" {{ request('payment_method') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="branch_id" :value="__('Sucursal')" />
                        <select id="branch_id" name="branch_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 text-sm">
                            <option value="">{{ __('Todas') }}</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="user_id" :value="__('Usuario')" />
                        <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-300 text-sm">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-end gap-2">
                        <x-primary-button class="w-full justify-center">{{ __('Filtrar') }}</x-primary-button>
                        <a href="{{ route('reports.sales') }}" class="w-full sm:w-auto text-center inline-flex items-center justify-center px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">Hoy</a>
                    </div>
                </form>
            </div>

            <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Ventas') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $totals->total_sales }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total') }}</div>
                        <div class="mt-1 text-xl font-semibold text-indigo-600 dark:text-indigo-400">{{ currency($totals->total_revenue) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Impuestos') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ currency($totals->total_tax) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Promedio') }}</div>
                        <div class="mt-1 text-xl font-semibold text-gray-900 dark:text-gray-100">{{ currency($totals->average) }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Efectivo') }}</div>
                        <div class="mt-1 text-xl font-semibold text-green-600 dark:text-green-400">{{ currency($totals->cash_total) }}</div>
                    </div>
                </div>
            </div>

            <div class="px-4 md:px-6 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Detalle de Ventas') }}</h3>
                <div class="flex gap-2">
                    <a href="{{ route('reports.export-pdf', 'sales') }}?{{ http_build_query(request()->query()) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                    <a href="{{ route('reports.export-csv', 'sales') }}?{{ http_build_query(request()->query()) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        CSV
                    </a>
                </div>
            </div>

            @if ($dailyChartData->isNotEmpty())
            <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
                <canvas id="salesChart" height="80"></canvas>
            </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Factura') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Cliente') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Usuario') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Total') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Método') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Sucursal') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Fecha') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->invoice_number }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->client?->name ?? $sale->client_name ?? __('Cliente General') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ currency($sale->total) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $sale->payment_method === 'cash' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' : '' }}
                                        {{ $sale->payment_method === 'card' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400' : '' }}
                                        {{ $sale->payment_method === 'transfer' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-400' : '' }}
                                        {{ $sale->payment_method === 'credit' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' : '' }}">
                                        {{ $paymentMethods[$sale->payment_method] ?? $sale->payment_method }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->branch?->name ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay ventas') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-content-wrapper>

    @push('scripts')
    <script nonce="{{ $cspNonce }}">
        const chartData = @json($dailyChartData);
        if (chartData && chartData.length > 0 && document.getElementById('salesChart')) {
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(d => d.date),
                    datasets: [{
                        label: '{{ __("Ventas") }}',
                        data: chartData.map(d => d.total),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 10 } } },
                        y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { callback: v => '$' + v.toLocaleString() } }
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
