<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Log: {{ $filename }}</h2>
            <a href="{{ route('logs.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">&larr; Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total: <strong>{{ number_format($totalLines) }}</strong> líneas</p>
                    <div class="flex items-center gap-2">
                        @if($page > 1)
                            <a href="{{ route('logs.show', ['filename' => $filename, 'page' => $page - 1]) }}" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md transition-colors">&larr; Anterior</a>
                        @endif
                        <span class="text-sm text-gray-600 dark:text-gray-400">Página {{ $page }} de {{ $lastPage }}</span>
                        @if($page < $lastPage)
                            <a href="{{ route('logs.show', ['filename' => $filename, 'page' => $page + 1]) }}" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md transition-colors">Siguiente &rarr;</a>
                        @endif
                    </div>
                </div>
                <pre class="text-xs leading-relaxed bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto max-h-[70vh]"><code>@foreach($contentLines as $line){{ $line }}@endforeach</code></pre>
            </div>
        </div>
    </div>
</x-app-layout>
