@props([
    'variant' => 'info',
    'dismissible' => true,
])

@php
    $variantClasses = match($variant) {
        'success' => 'bg-success/10 text-success border-success/20',
        'error' => 'bg-danger/10 text-danger border-danger/20',
        'warning' => 'bg-warning/10 text-warning border-warning/20',
        default => 'bg-info/10 text-info border-info/20',
    };
@endphp

<div
    x-data="{ visible: true }"
    x-show="visible"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    {{ $attributes->merge([
        'class' => "flex items-center justify-between rounded-lg border px-4 py-3 text-sm $variantClasses"
    ]) }}
    role="alert"
>
    <span>{{ $slot }}</span>

    @if($dismissible)
        <button x-on:click="visible = false" class="ml-3 opacity-70 hover:opacity-100 transition-opacity cursor-pointer">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>
