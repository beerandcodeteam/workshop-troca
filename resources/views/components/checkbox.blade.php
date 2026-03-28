@props([
    'label' => null,
    'name' => null,
    'checked' => false,
])

<label class="inline-flex items-center gap-2.5 cursor-pointer group">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        @checked($checked)
        {{ $attributes->merge([
            'class' => 'size-4 rounded border-outline-variant/30 bg-surface-container text-primary focus:ring-primary/30 focus:ring-offset-0 cursor-pointer'
        ]) }}
    />
    @if($label)
        <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors duration-200">{{ $label }}</span>
    @endif
</label>
