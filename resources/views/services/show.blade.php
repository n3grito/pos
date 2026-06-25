<x-app-layout>
    <x-slot name="header">{{ $service->name }}</x-slot>
    <x-content-wrapper>
        <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Nombre') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $service->name }}</dd>
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Descripción') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $service->description ?? '—' }}</dd>
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Categoría') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $service->category->name ?? '—' }}</dd>
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Precio de Venta') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-gray-200">{{ currency($service->selling_price) }}</dd>
                <dt class="text-gray-500 dark:text-gray-400">{{ __('IVA') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $service->tax_percentage }}%</dd>
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Estado') }}</dt>
                <dd>
                    @if ($service->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200">{{ __('Activo') }}</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200">{{ __('Inactivo') }}</span>
                    @endif
                </dd>
            </dl>

            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mt-8 mb-3">{{ __('Productos incluidos') }}</h4>
            <div class="overflow-x-auto"><table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-4 py-2 font-semibold">{{ __('Producto') }}</th>
                        <th class="text-center px-4 py-2 font-semibold">{{ __('Cantidad') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($service->products as $p)
                    <tr>
                        <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $p->name }}</td>
                        <td class="px-4 py-2 text-center text-gray-600 dark:text-gray-400">{{ $p->pivot->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table></div>

            <div class="mt-8 flex space-x-3">
                @can('service.update')
                <a href="{{ route('services.edit', $service) }}" class="px-4 py-2 text-sm font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/50 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/70">{{ __('Editar') }}</a>
                @endcan
                @can('service.delete')
                <form method="POST" action="{{ route('services.destroy', $service) }}" onsubmit="return confirm('{{ __('¿Eliminar este servicio?') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/50 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/70">{{ __('Eliminar') }}</button>
                </form>
                @endcan
                <a href="{{ route('services.index') }}" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">{{ __('Volver') }}</a>
            </div>
        </div>
    </x-content-wrapper>
</x-app-layout>
