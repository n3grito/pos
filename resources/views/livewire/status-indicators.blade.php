<div
    x-data="{
        init() {
            this.refresh();
            setInterval(() => { this.refresh(); }, 30000);
        },
        refresh() {
            $wire.refresh();
        }
    }"
    x-init="init"
    class="flex items-center gap-1"
>
    @if ($alerts > 0)
        <button
            x-data
            @click="document.querySelector('[data-shortcut-alerts]')?.click()"
            class="relative p-1.5 text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg transition-colors group"
            title="{{ __('Alertas (:count)', ['count' => $alerts]) }}"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">{{ min($alerts, 99) }}</span>
            <div class="absolute top-full right-0 mt-2 w-56 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg p-3 hidden group-hover:block z-50">
                <div class="text-xs space-y-2">
                    @if ($lowStockCount > 0)
                        <a href="{{ route('inventory.low-stock') }}" class="flex items-center gap-2 text-amber-600 dark:text-amber-400 hover:underline">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $lowStockCount }} {{ __('productos con stock bajo') }}
                        </a>
                    @endif
                    @if ($pendingProduction > 0)
                        <a href="{{ route('production.index') }}" class="flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:underline">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            {{ $pendingProduction }} {{ __('órdenes de producción pendientes') }}
                        </a>
                    @endif
                </div>
            </div>
        </button>
    @endif

    @if ($activeSessions > 0)
        <div class="flex items-center gap-1 px-2 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium" title="{{ __('Sesiones de caja activas') }}">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
            <span>{{ $activeSessions }}</span>
        </div>
    @endif
</div>
