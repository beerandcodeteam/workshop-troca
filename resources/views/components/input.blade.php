@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'error' => null,
    'icon' => null,
])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-2">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-on-surface-variant">
                {{ $icon }}
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => 'w-full bg-surface-container border border-outline-variant/15 text-on-surface placeholder-on-surface-variant/50 rounded-lg px-4 py-2.5 text-sm transition-all duration-200 focus:outline-none focus:border-primary/50 focus:shadow-[0_0_12px_rgba(151,169,255,0.15)]'
                . ($icon ? ' pl-10' : '')
                . ($error ? ' border-danger/50' : '')
            ]) }}
        />
    </div>

    @if($error)
        <p class="mt-1.5 text-xs text-danger">{{ $error }}</p>
    @endif
</div>
