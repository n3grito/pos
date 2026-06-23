<x-app-layout>
    <x-slot name="header">
    {{ __('Cajas Registradoras') }}
</x-slot>

    <x-content-wrapper>
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 dark:bg-green-900/50 border border-green-200 text-green-700 dark:text-green-400 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="p-6 flex justify-between items-center border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ __('Listado de Cajas Registradoras') }}</h3>
                    <a href="{{ route('cash-registers.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600">
                        {{ __('Nueva Caja') }}
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nombre') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Sucursal') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Estado') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($cashRegisters as $cashRegister)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cashRegister->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $cashRegister->branch->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $cashRegister->status === 'open' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                            {{ $cashRegister->status === 'open' ? __('Abierta') : __('Cerrada') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('cash-registers.edit', $cashRegister) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">{{ __('Editar') }}</a>
                                        @if ($cashRegister->status === 'closed')
                                            <form action="{{ route('cash-registers.open', $cashRegister) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">{{ __('Abrir') }}</button>
                                            </form>
                                        @endif
                                        @if ($cashRegister->status === 'open' && $cashRegister->activeSession)
                                            <form action="{{ route('cash-register-sessions.close', $cashRegister->activeSession) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Cerrar caja?') }}')">
                                                @csrf
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">{{ __('Cerrar') }}</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('cash-registers.destroy', $cashRegister) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Está seguro?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">{{ __('Eliminar') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('No hay cajas registradoras') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
    </x-content-wrapper>
</x-app-layout>