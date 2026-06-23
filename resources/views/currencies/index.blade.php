<x-app-layout>
    <x-slot name="header">
    {{ __('Monedas') }}
</x-slot>

    <x-content-wrapper>
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/50 border border-green-200 text-green-700 dark:text-green-400 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Listado de Monedas') }}</h3>
                    <a href="{{ route('currencies.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600">
                        {{ __('Nueva Moneda') }}
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nombre') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Código') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Símbolo') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tasa de Cambio') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Estado') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($currencies as $currency)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $currency->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $currency->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $currency->symbol }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($currency->exchange_rate, 4) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form action="{{ route('currencies.toggle-active', $currency) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full border-0 cursor-pointer {{ $currency->is_active ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800/50' : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200 hover:bg-red-200 dark:hover:bg-red-800/50' }}">
                                                {{ $currency->is_active ? __('Activa') : __('Inactiva') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('currencies.edit', $currency) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">{{ __('Editar') }}</a>
                                        <form action="{{ route('currencies.destroy', $currency) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Está seguro de eliminar esta moneda?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">{{ __('Eliminar') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay monedas registradas') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($currencies->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $currencies->links() }}
                    </div>
                @endif
            </div>
    </x-content-wrapper>
</x-app-layout>
