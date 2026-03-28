@props([
    'hover' => false,
])

<div {{ $attributes->merge([
    'class' => 'bg-surface-container-high rounded-xl p-5 transition-all duration-200'
    . ($hover ? ' hover:bg-surface-bright hover:shadow-[0_0_30px_rgba(151,169,255,0.06)]' : '')
]) }}>
    @if(isset($header))
        <div class="mb-4">
            {{ $header }}
        </div>
    @endif

    {{ $slot }}
</div>
