@props([
    'color' => 'white',
    'size' => 'md',
])

@php
    $sizeClasses = match($size) {
        'sm' => 'size-3',
        'lg' => 'size-6',
        default => 'size-4',
    };

    $colorClasses = match($color) {
        'red' => 'bg-token-red shadow-[inset_0_0_6px_rgba(239,68,68,0.5)]',
        'green' => 'bg-token-green shadow-[inset_0_0_6px_rgba(34,197,94,0.5)]',
        'yellow' => 'bg-token-yellow shadow-[inset_0_0_6px_rgba(234,179,8,0.5)]',
        'blue' => 'bg-token-blue shadow-[inset_0_0_6px_rgba(59,130,246,0.5)]',
        default => 'bg-token-white shadow-[inset_0_0_6px_rgba(248,250,252,0.5)]',
    };
@endphp

<span {{ $attributes->merge([
    'class' => "inline-block rounded-full $sizeClasses $colorClasses"
]) }}></span>
