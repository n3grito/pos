<x-app-layout>
    <x-slot name="header">
        {{ __('Manuales de Usuario') }}
    </x-slot>

    <x-content-wrapper>
        @php $userRole = auth()->user()->roles->first(); @endphp

        @if ($userRole && $roles->contains('id', $userRole->id))
            <div class="mb-8">
                <a href="{{ route('manuals.show', $userRole) }}"
                   class="block bg-gradient-to-br from-blue-500 to-blue-700 dark:from-blue-600 dark:to-blue-800 rounded-xl p-6 hover:from-blue-600 hover:to-blue-800 dark:hover:from-blue-500 dark:hover:to-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-blue-100 font-medium">{{ __('Tu rol') }}</p>
                                <h2 class="text-2xl font-bold text-white">{{ $userRole->name }}</h2>
                                <p class="text-blue-100 text-sm mt-1">{{ __('Haz clic para ver las funcionalidades disponibles para tu perfil') }}</p>
                            </div>
                        </div>
                        <svg class="w-8 h-8 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            </div>
        @elseif ($userRole && !$roles->contains('id', $userRole->id))
            <div class="mb-8 bg-gradient-to-br from-purple-500 to-purple-700 dark:from-purple-600 dark:to-purple-800 rounded-xl p-6 shadow-lg">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-purple-100 font-medium">{{ __('Tu rol') }}</p>
                        <h2 class="text-2xl font-bold text-white">{{ $userRole->name }}</h2>
                        <p class="text-purple-100 text-sm mt-1">{{ __('Acceso total al sistema — todos los permisos disponibles') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($roles->isNotEmpty() && !auth()->user()->hasRole('Admin'))
        @elseif ($roles->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Otros Roles') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Consulta el manual de otros roles para conocer sus permisos.') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($roles as $role)
                @if (auth()->user()->hasRole('Admin') || !$userRole || $role->id !== $userRole->id)
                    <a href="{{ route('manuals.show', $role) }}" class="block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200 group">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/50 rounded-lg shrink-0">
                                <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $role->name }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $role->permissions_count }} {{ __('permisos asignados') }}
                                </p>
                                <span class="mt-3 inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:underline">
                                    {{ __('Ver manual') }}
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach

            @if (auth()->user()->hasRole('Admin') && isset($adminRole))
                <a href="{{ route('manuals.show', $adminRole) }}" class="block bg-white dark:bg-gray-800 border-2 border-blue-200 dark:border-blue-700 rounded-xl p-6 hover:shadow-lg hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-200 group">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-purple-50 dark:bg-purple-900/50 rounded-lg shrink-0">
                            <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $adminRole->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Acceso total al sistema — todos los permisos') }}
                            </p>
                            <span class="mt-3 inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:underline">
                                {{ __('Ver manual') }}
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>
                    </div>
                </a>
            @endif
        </div>

        @if ($roles->isEmpty() && !auth()->user()->hasRole('Admin'))
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center">{{ __('No se encontraron manuales disponibles para tu usuario.') }}</p>
            </div>
        @endif
    </x-content-wrapper>
</x-app-layout>
