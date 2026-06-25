@php use Illuminate\Support\Facades\DB; @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Explorador de Base de Datos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200 rounded-lg">{{ session('success') }}</div>
            @endif

            @if(session('sql'))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Última consulta ({{ session('execTime') }} ms)</h3>
                    <pre class="text-xs bg-gray-100 dark:bg-gray-900 p-3 rounded overflow-x-auto mb-3"><code>{{ session('sql') }}</code></pre>
                    @if(session('columns'))
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        @foreach(session('columns') as $col)
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ $col }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach(session('results') as $row)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            @foreach(session('columns') as $col)
                                                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ is_null($row->$col) ? 'NULL' : $row->$col }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ count(session('results')) }} filas en {{ session('execTime') }} ms</p>
                    @endif
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200 rounded-lg">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Consola SQL</h3>
                <form method="POST" action="{{ route('database.explorer.query') }}">
                    @csrf
                    <textarea name="sql" rows="3" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500" placeholder="SELECT * FROM users LIMIT 10"></textarea>
                    <div class="flex justify-end mt-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg text-sm transition-colors">
                            Ejecutar
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Base de datos: <strong class="text-gray-800 dark:text-gray-200">{{ $dbName }}</strong> —
                        Tamaño: <strong class="text-gray-800 dark:text-gray-200">{{ number_format($dbSize / 1024 / 1024, 2) }} MB</strong> —
                        Tablas: <strong class="text-gray-800 dark:text-gray-200">{{ $tables->count() }}</strong>
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tabla</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Motor</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Filas</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tamaño</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Cotejamiento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Comentario</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($tables as $table)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 font-medium">{{ $table['name'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $table['engine'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 text-right">{{ number_format($table['rows']) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 text-right">{{ number_format($table['size'] / 1024, 1) }} KB</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $table['collation'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $table['comment'] }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('database.explorer.show', $table['name']) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-100 dark:bg-blue-900/50 hover:bg-blue-200 dark:hover:bg-blue-800/50 text-blue-700 dark:text-blue-300 rounded-md text-sm transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
