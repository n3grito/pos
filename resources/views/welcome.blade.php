<x-guest-layout>
    <div class="text-center">
        @if ($brand->logo_path)
            <img src="{{ logo_url($brand->logo_path) }}" alt="{{ config('app.name') }}" class="h-16 w-auto mx-auto mb-4">
        @else
            <x-application-logo class="h-16 w-16 fill-current text-blue-600 dark:text-blue-400 mx-auto mb-4" />
        @endif

        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ $brand->store_name ?? config('app.name') }}
        </h1>

        @if ($brand->company_name)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $brand->company_name }}</p>
        @endif

        @if ($brand->address || $brand->phone)
            <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                @if ($brand->address){{ $brand->address }}@endif
                @if ($brand->phone)@if ($brand->address) &middot; @endif{{ $brand->phone }}@endif
            </p>
        @endif

        <p class="mt-6 text-sm text-gray-600 dark:text-gray-400 leading-relaxed text-left">
            {{ __('Sistema POS para administrar ventas, compras, inventario y clientes de tu negocio.') }}
        </p>

        <ul class="mt-4 text-xs text-gray-500 dark:text-gray-400 leading-relaxed text-left space-y-1.5">
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('Registro de ventas con múltiples métodos de pago') }}
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('Control de inventario y stock por almacén') }}
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('Gestión de compras y proveedores') }}
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('Catálogo de productos y servicios con precios e IVA') }}
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('Reportes de ventas, productos más vendidos y stock bajo') }}
            </li>
            <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ __('Múltiples usuarios con roles y permisos') }}
            </li>
        </ul>

        <div class="mt-8 space-y-3">
            <a href="{{ route('login') }}" class="block w-full px-4 py-3 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg transition-colors">
                {{ __('Iniciar Sesión') }}
            </a>

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="block w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    {{ __('Registrarse') }}
                </a>
            @endif
        </div>

        <p class="mt-8 text-xs text-gray-400 dark:text-gray-500">
            <a href="{{ route('privacy') }}" class="hover:underline">{{ __('Política de Privacidad') }}</a>
        </p>
    </div>
</x-guest-layout>
