<x-app-layout>
    <x-slot name="header">
    {{ __('Actualizar Compra') }}: {{ $purchase->invoice_number }}
</x-slot>

    <x-content-wrapper>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Proveedor') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $purchase->supplier->name ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total') }}</h4>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ currency($purchase->total) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estado Actual') }}</h4>
                            <p class="mt-1 text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400' : '' }}
                                    {{ $purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-400' : '' }}
                                    {{ $purchase->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400' : '' }}">
                                    {{ $purchase->status === 'completed' ? __('Completada') : ($purchase->status === 'pending' ? __('Pendiente') : __('Cancelada')) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if ($purchase->status !== 'cancelled')
                        <form method="POST" action="{{ route('purchases.update', $purchase) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <x-input-label for="status" :value="__('Cambiar Estado')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="completed" {{ old('status', $purchase->status) === 'completed' ? 'selected' : '' }}>{{ __('Completada') }}</option>
                                    <option value="pending" {{ old('status', $purchase->status) === 'pending' ? 'selected' : '' }}>{{ __('Pendiente') }}</option>
                                    <option value="cancelled">{{ __('Cancelar Compra') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Al cancelar la compra, el stock de los productos se revertirá.') }}</p>
                            </div>

                            <div class="flex items-center justify-end mt-6 space-x-3">
                                <a href="{{ route('purchases.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                                    {{ __('Cancelar') }}
                                </a>
                                <x-primary-button>{{ __('Actualizar') }}</x-primary-button>
                            </div>
                        </form>
                    @else
                        <div class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md p-4">
                            <p class="text-sm text-red-700 dark:text-red-400">{{ __('Esta compra ha sido cancelada y no se puede modificar.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>