@props([
    'label' => null,
    'name' => null,
    'placeholder' => null,
    'error' => null,
    'options' => [],
])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-2">
            {{ $label }}
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'w-full bg-surface-container border border-outline-variant/15 text-on-surface rounded-lg px-4 py-2.5 text-sm transition-all duration-200 focus:outline-none focus:border-primary/50 focus:shadow-[0_0_12px_rgba(151,169,255,0.15)] appearance-none cursor-pointer'
            . ($error ? ' border-danger/50' : '')
        ]) }}
    >
        @if($placeholder)
            <option value="" disabled selected class="text-on-surface-variant/50">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach

        {{ $slot }}
    </select>

    @if($error)
        <p class="mt-1.5 text-xs text-danger">{{ $error }}</p>
    @endif
</div>
