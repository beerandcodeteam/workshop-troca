@props([
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $sizeClasses = match($size) {
        'sm' => 'px-2 py-0.5 text-[10px]',
        'lg' => 'px-3.5 py-1.5 text-sm',
        default => 'px-2.5 py-1 text-xs',
    };

    $variantClasses = match($variant) {
        'secondary' => 'bg-secondary/15 text-secondary',
        'tertiary' => 'bg-tertiary/15 text-tertiary',
        'danger' => 'bg-danger/15 text-danger',
        'warning' => 'bg-warning/15 text-warning',
        'success' => 'bg-success/15 text-success',
        'info' => 'bg-info/15 text-info',
        default => 'bg-primary/15 text-primary',
    };
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center font-semibold uppercase tracking-wider rounded-full $sizeClasses $variantClasses"
]) }}>
    {{ $slot }}
</span>
