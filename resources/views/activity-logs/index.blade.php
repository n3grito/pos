<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Registro de Actividad</h2>
            <div class="flex items-center gap-4">
                <div id="alertCount" class="hidden text-xs font-medium px-2 py-1 rounded-full bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300"></div>
                <label class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <input type="checkbox" id="autoRefresh" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 mr-2" onchange="if(this.checked) { startPolling(); } else { stopPolling(); }">
                    {{ __('Auto-actualizar (10s)') }}
                </label>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 flex flex-wrap gap-4 items-end">
                <div>
                    <label for="filter_user" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Usuario</label>
                    <select id="filter_user" name="user_id" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg">
                        <option value="">Todos</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter_action" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Acción</label>
                    <select id="filter_action" name="action" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg">
                        <option value="">Todas</option>
                        @foreach($actions as $a)
                            <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ __(ucfirst(str_replace('_', ' ', $a))) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter_severity" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Severidad</label>
                    <select id="filter_severity" name="severity" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg">
                        <option value="">Todas</option>
                        @foreach($severities as $s)
                            <option value="{{ $s }}" {{ request('severity') == $s ? 'selected' : '' }}>{{ __(ucfirst($s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2 pb-1">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition-colors">Filtrar</button>
                    <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">Limpiar</a>
                </div>
            </form>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Severidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fecha/Hora</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Usuario</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acción</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Descripción</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $log->notable ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                        {{ $log->severity === 'critical' ? 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300' : '' }}
                                        {{ $log->severity === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300' : '' }}
                                        {{ $log->severity === 'info' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300' : '' }}">
                                        {{ $log->severity ?? 'info' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $log->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                        {{ $log->action === 'login' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300' : '' }}
                                        {{ $log->action === 'logout' ? 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300' : '' }}
                                        {{ !in_array($log->action, ['login', 'logout']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}">
                                        {{ __(ucfirst(str_replace('_', ' ', $log->action))) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-md truncate">{{ $log->description ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Sin actividad registrada.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script nonce="{{ $cspNonce }}">
        let pollTimer = null;

        function startPolling() {
            pollTimer = setInterval(function() {
                fetch('{{ route('activity-logs.stream') }}?since=60')
                    .then(r => r.json())
                    .then(data => {
                        if (data.count > 0) {
                            const badge = document.getElementById('alertCount');
                            badge.textContent = data.count + ' nueva(s)';
                            badge.classList.remove('hidden');
                            setTimeout(() => badge.classList.add('hidden'), 8000);

                            data.alerts.forEach(function(alert) {
                                if (window.addToast) {
                                    window.addToast(alert.description, alert.severity === 'critical' ? 'error' : 'warning', false);
                                }
                            });
                        }
                    })
                    .catch(() => {});
            }, 10000);
        }

        function stopPolling() {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        document.addEventListener('alpine:init', () => {
            if (document.getElementById('autoRefresh')?.checked) {
                startPolling();
            }
        });
    </script>
    @endpush
</x-app-layout>
