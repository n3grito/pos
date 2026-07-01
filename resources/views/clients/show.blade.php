<x-app-layout>
    <x-slot name="header">{{ __('Cliente') }}: {{ $client->name }}</x-slot>

    <x-content-wrapper>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <div class="p-6">
                        <div class="flex items-start gap-6 mb-6">
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0 ring-2" style="border-color: {{ $client->type_color }}40">
                                @if ($client->photo_url)
                                    <img src="{{ $client->photo_url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $client->name }}</h2>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $client->type_color }}15; color: {{ $client->type_color }}; border: 1px solid {{ $client->type_color }}30">
                                        {{ $client->type_label }}
                                    </span>
                                    @if ($client->document_number)
                                        <span class="text-sm text-gray-500 dark:text-gray-400">NIT: {{ $client->document_number }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Correo Electrónico') }}</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->email ?? __('N/A') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Teléfono') }}</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->phone ?? __('N/A') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Dirección') }}</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->address ?? __('N/A') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Grupo') }}</h4>
                                <p class="mt-1">
                                    @if ($client->customerGroup)
                                        <span class="px-2 py-1 text-xs rounded-full" style="background-color: {{ $client->customerGroup->color }}20; color: {{ $client->customerGroup->color }}">{{ $client->customerGroup->name }}</span>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sin grupo') }}</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Creado') }}</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @if ($client->notes)
                            <div class="md:col-span-2">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Notas') }}</h4>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($client->sales->count() > 0)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Compras del Cliente') }}</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Factura') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Descuento') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Puntos') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fecha') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($client->sales as $sale)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $sale->invoice_number }}</a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($sale->total) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->discount_amount > 0 ? currency($sale->discount_amount) : '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->points_earned > 0 ? '+' . $sale->points_earned : '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex space-x-3">
                    <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                        {{ __('Editar') }}
                    </a>
                    <a href="{{ route('clients.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ __('Volver') }}
                    </a>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">{{ __('Lealtad') }}</h4>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($client->points) }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Puntos') }}</div>
                    </div>
                    @php
                        $pointsValue = $client->points > 0 ? $client->points / \App\Services\LoyaltyService::REDEEM_RATE : 0;
                    @endphp
                    @if ($pointsValue > 0)
                        <div class="mt-3 text-center text-sm text-green-600 dark:text-green-400">
                            {{ __('Valor: :value', ['value' => currency($pointsValue)]) }}
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 space-y-4">
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Estadísticas') }}</h4>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ currency($client->total_spent) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total Gastado') }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $client->sales->count() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Compras Realizadas') }}</div>
                    </div>
                    @if ($client->last_purchase_at)
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $client->last_purchase_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Última Compra') }}</div>
                        </div>
                    @endif
                    @if ($client->sales->count() > 0)
                        @php
                            $avgTicket = $client->total_spent / $client->sales->count();
                        @endphp
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ currency($avgTicket) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Ticket Promedio') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
