<x-app-layout>
    <x-slot name="header">
        {{ __('Diseño del Recibo') }}
    </x-slot>

    <x-content-wrapper>
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/50 border border-green-200 text-green-700 dark:text-green-400 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Checkboxes -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Opciones del Recibo') }}</h3>

                    <form method="POST" action="{{ route('settings.receipt.update') }}">
                        @csrf

                        <div class="space-y-3">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="show_seller" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('show_seller', $receipt->show_seller ?? true) ? 'checked' : '' }} />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Mostrar nombre del vendedor en el recibo') }}</span>
                            </label>
                            <br>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="show_nit" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('show_nit', $receipt->show_nit ?? true) ? 'checked' : '' }} />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Mostrar NIT del cliente en el recibo') }}</span>
                            </label>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <x-primary-button>{{ __('Guardar configuración') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Receipt Preview -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Vista previa del recibo') }}</h3>
                    <div class="border border-gray-300 dark:border-gray-600 rounded p-4 max-w-sm mx-auto dark:bg-gray-700" style="font-family: monospace; font-size: 12px;">
                        <div class="text-center mb-2">
                            <div class="font-bold text-base text-gray-900 dark:text-gray-100">{{ $receipt->store_name ?? config('app.name') }}</div>
                            @if ($receipt->address)
                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $receipt->address }}</div>
                            @endif
                            @if ($receipt->phone)
                                <div class="text-xs text-gray-600 dark:text-gray-400">Tel: {{ $receipt->phone }}</div>
                            @endif
                        </div>
                        <hr class="border-t border-dashed border-gray-400 dark:border-gray-500 mb-2">
                        <div class="text-center text-xs text-gray-500 dark:text-gray-400">
                            <div>{{ __('Factura') }}: INV-20260617-00001</div>
                            <div>{{ now()->format('d/m/Y H:i') }}</div>
                        </div>
                        <hr class="border-t border-dashed border-gray-400 dark:border-gray-500 my-2">
                        <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <tr>
                                <td>{{ __('Producto ejemplo') }}</td>
                                <td class="text-center">1</td>
                                <td class="text-right">{{ currency(10) }}</td>
                                <td class="text-right">{{ currency(10) }}</td>
                            </tr>
                        </table>
                        </div>
                        <hr class="border-t border-dashed border-gray-400 dark:border-gray-500 my-2">
                        <div class="text-right font-bold text-gray-900 dark:text-gray-100">{{ __('Total') }}: {{ currency(10) }}</div>
                        <hr class="border-t border-dashed border-gray-400 dark:border-gray-500 my-2">
                        <div class="text-center text-xs text-gray-600 dark:text-gray-400">
                            {{ $receipt->footer_text ?? __('¡Gracias por su compra!') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
