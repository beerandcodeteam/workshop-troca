@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold tracking-wide uppercase transition-all duration-200 ease-in-out focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer';

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs rounded-md gap-1.5',
        'lg' => 'px-8 py-3.5 text-base rounded-lg gap-3',
        default => 'px-5 py-2.5 text-sm rounded-lg gap-2',
    };

    $variantClasses = match($variant) {
        'secondary' => 'bg-surface-container-high text-on-surface border border-outline-variant/15 hover:bg-surface-bright',
        'danger' => 'bg-danger text-on-danger hover:brightness-110 hover:shadow-[0_0_20px_rgba(255,113,108,0.3)]',
        'ghost' => 'bg-transparent text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high',
        'success' => 'bg-success text-on-success hover:brightness-110 hover:shadow-[0_0_20px_rgba(34,197,94,0.3)]',
        default => 'bg-primary text-on-primary hover:brightness-110 hover:shadow-[0_0_20px_rgba(151,169,255,0.4)]',
    };

    $classes = "$baseClasses $sizeClasses $variantClasses";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled || $loading ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
