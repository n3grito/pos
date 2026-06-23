<x-app-layout>
    <x-slot name="header">
    {{ __('Cliente') }}: {{ $client->name }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nombre') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Correo Electrónico') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->email ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Teléfono') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->phone ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tipo de Documento') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->document_type ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Número de Documento') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->document_number ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Dirección') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->address ?? __('N/A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Creado') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Actualizado') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if ($client->sales->count() > 0)
                        <div class="mt-8">
                            <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Compras del Cliente') }}</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Factura') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Total') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Fecha') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($client->sales as $sale)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->invoice_number }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ currency($sale->total) }}</td>
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
            </div>
    </x-content-wrapper>
</x-app-layout>
