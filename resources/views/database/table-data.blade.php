<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Tabla: {{ $table }}</h2>
            <a href="{{ route('database.explorer.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">&larr; Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Estructura</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Columna</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Tipo</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Nulo</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Default</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Extra</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($columns as $col)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-3 py-2 text-gray-800 dark:text-gray-200 font-medium">{{ $col->Field }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $col->Type }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $col->Null }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $col->Default ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $col->Extra }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total: <strong>{{ number_format($total) }}</strong> filas</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Mostrando {{ $rows->firstItem() }}–{{ $rows->lastItem() }}</p>
                </div>
                @if($rows->isEmpty())
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">Sin datos.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    @foreach($columns as $col)
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ $col->Field }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($rows as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    @foreach($columns as $col)
                                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ is_null($row->{$col->Field}) ? 'NULL' : $row->{$col->Field} }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                        {{ $rows->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
