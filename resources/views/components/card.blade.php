<div {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-xl']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-gray-200">
            {{ $header }}
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
