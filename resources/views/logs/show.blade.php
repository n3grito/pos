<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Log: {{ $filename }}</h2>
            <a href="{{ route('logs.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">&larr; Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-4">
                <form method="GET" class="flex flex-wrap items-end gap-4">
                    <input type="hidden" name="page" value="1">
                    <div>
                        <label for="log_level" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Nivel</label>
                        <select id="log_level" name="level" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                            <option value="">Todos</option>
                            @foreach($levels as $lvl)
                                <option value="{{ $lvl }}" {{ $level === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="log_search" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Buscar</label>
                        <input id="log_search" type="text" name="search" value="{{ $search }}" placeholder="Texto..." class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md transition-colors">Filtrar</button>
                    @if($level || $search)
                        <a href="{{ route('logs.show', $filename) }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-sm rounded-md transition-colors">Limpiar</a>
                    @endif
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>{{ number_format($totalEntries) }}</strong> entradas encontradas
                    </p>
                    <div class="flex items-center gap-2">
                        @if($page > 1)
                            <a href="{{ route('logs.show', array_merge(['filename' => $filename, 'page' => $page - 1], request()->only(['level', 'search']))) }}" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md transition-colors">&larr; Anterior</a>
                        @endif
                        <span class="text-sm text-gray-600 dark:text-gray-400">Página {{ $page }} de {{ $lastPage }}</span>
                        @if($page < $lastPage)
                            <a href="{{ route('logs.show', array_merge(['filename' => $filename, 'page' => $page + 1], request()->only(['level', 'search']))) }}" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md transition-colors">Siguiente &rarr;</a>
                        @endif
                    </div>
                </div>

                @if($paginated->isEmpty())
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">No se encontraron entradas.</div>
                @else
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($paginated as $entry)
                            @php
                                $levelColors = [
                                    'EMERGENCY' => 'bg-red-900 text-red-100',
                                    'ALERT' => 'bg-red-700 text-white',
                                    'CRITICAL' => 'bg-red-600 text-white',
                                    'ERROR' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                    'WARNING' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                                    'NOTICE' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                    'INFO' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                    'DEBUG' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                ];
                                $color = $levelColors[$entry['level']] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <div class="flex items-start gap-3">
                                    <span class="shrink-0 inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded {{ $color }}">
                                        {{ $entry['level'] }}
                                    </span>
                                    <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">
                                        {{ \Carbon\Carbon::parse($entry['datetime'])->format('d/m/Y H:i:s') }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm text-gray-900 dark:text-gray-100 break-words">{{ $entry['message'] }}</p>
                                        @if($entry['context'])
                                            <pre class="mt-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/50 p-2 rounded overflow-x-auto max-h-24">{{ $entry['context'] }}</pre>
                                        @endif
                                        @if($entry['stack_trace'])
                                            <details class="mt-1">
                                                <summary class="text-xs text-blue-600 dark:text-blue-400 cursor-pointer hover:underline">Stack Trace</summary>
                                                <pre class="mt-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900/50 p-2 rounded overflow-x-auto max-h-48">{{ $entry['stack_trace'] }}</pre>
                                            </details>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total: <strong>{{ number_format($totalEntries) }}</strong> entradas</p>
                    <div class="flex items-center gap-2">
                        @if($page > 1)
                            <a href="{{ route('logs.show', array_merge(['filename' => $filename, 'page' => $page - 1], request()->only(['level', 'search']))) }}" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md transition-colors">&larr; Anterior</a>
                        @endif
                        <span class="text-sm text-gray-600 dark:text-gray-400">Página {{ $page }} de {{ $lastPage }}</span>
                        @if($page < $lastPage)
                            <a href="{{ route('logs.show', array_merge(['filename' => $filename, 'page' => $page + 1], request()->only(['level', 'search']))) }}" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md transition-colors">Siguiente &rarr;</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>