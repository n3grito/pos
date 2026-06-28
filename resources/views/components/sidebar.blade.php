<nav class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0" :class="{'translate-x-0': sidebarOpen}">
    @php
        $brand = \App\Models\ReceiptSetting::firstOrNew([]);
    @endphp
    <div class="flex items-center min-h-16 px-6 py-3 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            @if ($brand->logo_path)
                <img src="{{ logo_url($brand->logo_path) }}" alt="{{ config('app.name') }}" class="h-9 w-auto">
            @else
                <x-application-logo class="h-8 w-8 fill-current text-blue-600 dark:text-blue-400 shrink-0" />
            @endif
            <div class="min-w-0">
                <div class="text-lg font-bold text-gray-800 dark:text-gray-100 leading-tight truncate">{{ config('app.name', 'POS') }}</div>
                @if ($brand->company_name)
                    <div class="text-xs text-gray-500 dark:text-gray-400 leading-tight truncate">{{ $brand->company_name }}</div>
                @endif
            </div>
        </a>
    </div>

    <div class="px-3 py-4 space-y-1">
        <div class="px-3 py-2 text-xs font-semibold tracking-wider text-gray-400 dark:text-gray-500 uppercase">{{ __('Principal') }}</div>
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100' }}">
            <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            {{ __('Dashboard') }}
        </a>

        @canany(['sale.create', 'sale.view-any'])
        <div class="pt-4">
            <div x-data="{ open: {{ request()->routeIs('sales.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4"/></svg>
                    <span class="flex-1 text-left">{{ __('Ventas') }}</span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" class="mt-1 space-y-1">
                    @can('sale.create')
                    <a href="{{ route('sales.create') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('sales.create') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('sales.create') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Nueva Venta') }}
                    </a>
                    @endcan
                    @can('sale.view-any')
                    <a href="{{ route('sales.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('sales.index', 'sales.show', 'sales.edit', 'sales.cancel') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('sales.index', 'sales.show', 'sales.edit', 'sales.cancel') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        {{ __('Lista de Ventas') }}
                    </a>
                    <a href="{{ route('sales.kanban') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('sales.kanban') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('sales.kanban') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                        {{ __('Kanban') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany

        @canany(['purchase.create', 'purchase.view-any'])
        <div class="pt-4">
            <div x-data="{ open: {{ request()->routeIs('purchases.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0"/></svg>
                    <span class="flex-1 text-left">{{ __('Compras') }}</span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" class="mt-1 space-y-1">
                    @can('purchase.create')
                    <a href="{{ route('purchases.create') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('purchases.create') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('purchases.create') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Nueva Compra') }}
                    </a>
                    @endcan
                    @can('purchase.view-any')
                    <a href="{{ route('purchases.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('purchases.index', 'purchases.show', 'purchases.edit') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('purchases.index', 'purchases.show', 'purchases.edit') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        {{ __('Lista de Compras') }}
                    </a>
                    <a href="{{ route('purchases.kanban') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('purchases.kanban') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('purchases.kanban') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                        {{ __('Kanban') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany

        @canany(['product.view-any', 'category.view-any', 'service.view-any', 'inventory.view', 'inventory.movements', 'inventory.adjustment', 'inventory.low-stock'])
        <div class="pt-4">
            <div x-data="{ open: {{ request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('inventory.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span class="flex-1 text-left">{{ __('Inventario') }}</span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" class="mt-1 space-y-1">
                    @can('product.view-any')
                    <a href="{{ route('products.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('products.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('products.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        {{ __('Productos') }}
                    </a>
                    @endcan
                    @can('category.view-any')
                    <a href="{{ route('categories.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('categories.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('categories.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        {{ __('Categorías') }}
                    </a>
                    @endcan
                    @can('service.view-any')
                    <a href="{{ route('services.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('services.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('services.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        {{ __('Servicios') }}
                    </a>
                    @endcan
                    @can('inventory.view')
                    <a href="{{ route('inventory.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('inventory.index') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('inventory.index') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        {{ __('Stock General') }}
                    </a>
                    @endcan
                    @can('inventory.movements')
                    <a href="{{ route('inventory.movements') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('inventory.movements') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('inventory.movements') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                        {{ __('Movimientos') }}
                    </a>
                    @endcan
                    @can('inventory.adjustment')
                    <a href="{{ route('inventory.adjustment') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('inventory.adjustment*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('inventory.adjustment*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9"/></svg>
                        {{ __('Ajustar Stock') }}
                    </a>
                    @endcan
                    @can('inventory.low-stock')
                    <a href="{{ route('inventory.low-stock') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('inventory.low-stock') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('inventory.low-stock') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        {{ __('Stock Bajo') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany

        @canany(['warehouse.view-any', 'warehouse.stock', 'warehouse.transfer'])
        <div class="pt-4">
            <div x-data="{ open: {{ request()->routeIs('warehouses.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span class="flex-1 text-left">{{ __('Almacén') }}</span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" class="mt-1 space-y-1">
                    @can('warehouse.view-any')
                    <a href="{{ route('warehouses.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('warehouses.index', 'warehouses.create', 'warehouses.edit', 'warehouses.show') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('warehouses.index', 'warehouses.create', 'warehouses.edit', 'warehouses.show') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        {{ __('Almacenes') }}
                    </a>
                    @endcan
                    @can('warehouse.stock')
                    <a href="{{ route('warehouses.stock') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('warehouses.stock') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('warehouses.stock') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        {{ __('Stock por Almacén') }}
                    </a>
                    @endcan
                    @can('warehouse.transfer')
                    <a href="{{ route('warehouses.transfer') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('warehouses.transfer') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('warehouses.transfer') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        {{ __('Entrada a Inventario') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany

        @canany(['client.view-any', 'supplier.view-any'])
        <div class="pt-4">
            <div x-data="{ open: {{ request()->routeIs('clients.*') || request()->routeIs('suppliers.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="flex-1 text-left">{{ __('Clientes') }}</span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" class="mt-1 space-y-1">
                    @can('client.view-any')
                    <a href="{{ route('clients.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('clients.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('clients.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ __('Clientes') }}
                    </a>
                    @endcan
                    @can('supplier.view-any')
                    <a href="{{ route('suppliers.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('suppliers.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('suppliers.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        {{ __('Proveedores') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany

        @canany(['report.view-any'])
        <div class="pt-4">
            <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="flex-1 text-left">{{ __('Reportes') }}</span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" class="mt-1 space-y-1">
                    @can('report.view-any')
                    <a href="{{ route('reports.sales') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('reports.sales') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('reports.sales') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        {{ __('Reporte de Ventas') }}
                    </a>
                    @endcan
                    @can('report.view-any')
                    <a href="{{ route('reports.top-products') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('reports.top-products') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('reports.top-products') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        {{ __('Productos Más Vendidos') }}
                    </a>
                    @endcan
                    @can('report.view-any')
                    <a href="{{ route('reports.low-stock') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('reports.low-stock') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('reports.low-stock') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        {{ __('Stock Bajo') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany

        @can('manual.view')
        <div class="pt-4">
            <a href="{{ route('manuals.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('manuals.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100' }}">
                <svg class="w-5 h-5 mr-3 shrink-0 {{ request()->routeIs('manuals.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                {{ __('Manuales') }}
            </a>
        </div>
        @endcan

        @canany(['branch.view-any', 'cash-register.view-any', 'cash-register-session.view-any', 'currency.view-any', 'user.view-any', 'role.view-any', 'setting.manage', 'activity-log.view', 'database.backup', 'database.explorer', 'log.viewer'])
        <div class="pt-4">
            <div x-data="{ open: {{ request()->routeIs('branches.*') || request()->routeIs('cash-registers.*') || request()->routeIs('cash-register-sessions.*') || request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('currencies.*') || request()->routeIs('settings.*') || request()->routeIs('price-lists.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="flex-1 text-left">{{ __('Configuración') }}</span>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" class="mt-1 space-y-1">
                    @can('branch.view-any')
                    <a href="{{ route('branches.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('branches.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('branches.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ __('Sucursales') }}
                    </a>
                    @endcan
                    @can('cash-register.view-any')
                    <a href="{{ route('cash-registers.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('cash-registers.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('cash-registers.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        {{ __('Cajas Registradoras') }}
                    </a>
                    @endcan
                    @can('cash-register-session.view-any')
                    <a href="{{ route('cash-register-sessions.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('cash-register-sessions.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('cash-register-sessions.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Sesiones de Caja') }}
                    </a>
                    @endcan
                    @can('currency.view-any')
                    <a href="{{ route('currencies.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('currencies.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('currencies.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        {{ __('Monedas') }}
                    </a>
                    @endcan
                    @can('user.view-any')
                    <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('users.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('users.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ __('Usuarios') }}
                    </a>
                    @endcan
                    @can('role.view-any')
                    <a href="{{ route('roles.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('roles.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('roles.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        {{ __('Roles y Permisos') }}
                    </a>
                    @endcan
                    @can('setting.manage')
                    <a href="{{ route('price-lists.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('price-lists.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('price-lists.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Listas de Precio') }}
                    </a>
                    @endcan
                    @can('setting.manage')
                    <a href="{{ route('settings.general') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('settings.general') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('settings.general') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ __('General') }}
                    </a>
                    <a href="{{ route('settings.receipt') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('settings.receipt') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('settings.receipt') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('Diseño del Recibo') }}
                    </a>
                    <a href="{{ route('settings.mail') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('settings.mail') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('settings.mail') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ __('Configuración de Correo') }}
                    </a>
                    @endcan
                    @can('activity-log.view')
                    <a href="{{ route('activity-logs.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('activity-logs.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('activity-logs.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        {{ __('Registro de Actividad') }}
                        <span id="sidebarAlertBadge" class="ml-auto hidden w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    </a>
                    @endcan
                    @canany(['database.backup', 'database.explorer', 'log.viewer'])
                    <div class="border-t border-gray-200 dark:border-gray-600 my-2"></div>
                    @endcanany
                    @can('database.backup')
                    <a href="{{ route('database.backups') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('database.backups*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('database.backups*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        {{ __('Copias de Seguridad') }}
                    </a>
                    @endcan
                    @can('database.explorer')
                    <a href="{{ route('database.explorer.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('database.explorer*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('database.explorer*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6"/></svg>
                        {{ __('Explorador BD') }}
                    </a>
                    @endcan
                    @can('log.viewer')
                    <a href="{{ route('logs.index') }}" class="flex items-center px-3 py-2 ml-8 text-sm font-medium rounded-lg transition-colors duration-150 {{ request()->routeIs('logs.*') ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0 {{ request()->routeIs('logs.*') ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('Visor de Logs') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endcanany
    </div>

    </div>
</nav>
