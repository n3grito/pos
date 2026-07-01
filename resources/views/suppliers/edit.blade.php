<x-app-layout>
    <x-slot name="header">{{ __('Editar Proveedor') }}: {{ $supplier->name }}</x-slot>

    <x-content-wrapper>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="p-6">
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Foto') }}</label>
                            <div class="flex flex-col items-center gap-3">
                                <div id="photoPreview" class="w-36 h-36 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 border-2 {{ $supplier->photo ? '' : 'border-dashed border-gray-300 dark:border-gray-600' }} flex items-center justify-center">
                                    @if ($supplier->photo_url)
                                        <img src="{{ $supplier->photo_url }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                </div>
                                <label class="w-full cursor-pointer">
                                    <span class="block w-full text-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">{{ __('Cambiar foto') }}</span>
                                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="previewPhoto(event)">
                                </label>
                                @if ($supplier->photo)
                                    <label class="flex items-center gap-2 text-xs text-gray-500">
                                        <input type="checkbox" name="remove_photo" value="1">
                                        {{ __('Eliminar foto actual') }}
                                    </label>
                                @endif
                                <p class="text-xs text-gray-400">{{ __('JPEG, PNG o WebP · máx 2MB') }}</p>
                            </div>
                        </div>

                        <div class="lg:col-span-2 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('Tipo de Proveedor') }}</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach (\App\Models\Supplier::TYPES as $value => $label)
                                        @php $color = \App\Models\Supplier::TYPE_COLORS[$value]; @endphp
                                        <label class="relative flex items-center p-3 rounded-lg border-2 cursor-pointer transition-all duration-150" style="border-color: {{ $color }}30; background-color: {{ $color }}08" data-type-option data-border-color="{{ $color }}">
                                            <input type="radio" name="client_type" value="{{ $value }}" {{ old('client_type', $supplier->client_type) === $value ? 'checked' : '' }} class="sr-only">
                                            <span class="w-3 h-3 rounded-full mr-2 shrink-0" style="background-color: {{ $color }}"></span>
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('client_type')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="name" :value="__('Nombre de la Empresa')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $supplier->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="tax_id" :value="__('NIT')" />
                                    <x-text-input id="tax_id" class="block mt-1 w-full" type="text" name="tax_id" :value="old('tax_id', $supplier->tax_id)" required placeholder="{{ __('Número de Identificación Tributaria') }}" />
                                    <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="contact_person" :value="__('Persona de Contacto')" />
                                    <x-text-input id="contact_person" class="block mt-1 w-full" type="text" name="contact_person" :value="old('contact_person', $supplier->contact_person)" />
                                    <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Correo Electrónico')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $supplier->email)" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="phone" :value="__('Teléfono')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $supplier->phone)" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="address" :value="__('Dirección')" />
                                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $supplier->address)" />
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end mt-8 gap-2 sm:space-x-3 sm:gap-0 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('suppliers.index') }}" class="text-center inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button class="w-full sm:w-auto justify-center">{{ __('Actualizar Proveedor') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </x-content-wrapper>

    @push('scripts')
    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('photoPreview');
                preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                preview.classList.remove('border-dashed', 'border-gray-300', 'dark:border-gray-600');
            };
            reader.readAsDataURL(file);
        }
    </script>
    @endpush
</x-app-layout>
