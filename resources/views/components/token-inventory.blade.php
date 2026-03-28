@props([
    'inventories' => [],
    'maxTokens' => 10,
])

@php
    $colorConfig = [
        'red' => ['label' => 'Vermelhos', 'icon' => 'local_fire_department', 'bg' => 'bg-danger', 'glow' => 'shadow-[inset_0_0_8px_rgba(239,68,68,0.5),0_0_12px_rgba(239,68,68,0.3)]', 'text' => 'text-danger', 'iconText' => 'text-on-danger'],
        'green' => ['label' => 'Verdes', 'icon' => 'eco', 'bg' => 'bg-secondary', 'glow' => 'shadow-[inset_0_0_8px_rgba(34,197,94,0.5),0_0_12px_rgba(34,197,94,0.3)]', 'text' => 'text-secondary', 'iconText' => 'text-on-secondary'],
        'white' => ['label' => 'Brancos', 'icon' => 'circle', 'bg' => 'bg-on-surface', 'glow' => 'shadow-[inset_0_0_8px_rgba(248,250,252,0.5),0_0_12px_rgba(248,250,252,0.3)]', 'text' => 'text-on-surface-variant', 'iconText' => 'text-surface'],
        'yellow' => ['label' => 'Amarelos', 'icon' => 'star', 'bg' => 'bg-warning', 'glow' => 'shadow-[inset_0_0_8px_rgba(234,179,8,0.5),0_0_12px_rgba(234,179,8,0.3)]', 'text' => 'text-warning', 'iconText' => 'text-on-warning'],
        'blue' => ['label' => 'Azuis', 'icon' => 'ac_unit', 'bg' => 'bg-primary', 'glow' => 'shadow-[inset_0_0_8px_rgba(59,130,246,0.5),0_0_12px_rgba(59,130,246,0.3)]', 'text' => 'text-primary', 'iconText' => 'text-on-primary'],
    ];

    $totalTokens = collect($inventories)->sum('quantity');
    $nearLimit = $totalTokens >= ($maxTokens - 2) && $totalTokens <= $maxTokens;
    $overLimit = $totalTokens > $maxTokens;
@endphp

<section class="space-y-4">
    <div class="flex items-center justify-between">
        <label class="font-display text-xs uppercase tracking-[0.3em] text-on-surface-variant font-bold">Seu Inventário</label>
        <span @class([
            'text-[10px] px-2 py-1 rounded border',
            'text-outline-variant bg-surface-container border-outline-variant/10' => !$nearLimit && !$overLimit,
            'text-warning bg-warning/10 border-warning/20' => $nearLimit,
            'text-danger bg-danger/10 border-danger/20' => $overLimit,
        ])>
            {{ $totalTokens }} / {{ $maxTokens }}
        </span>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach ($inventories as $inventory)
            @php
                $slug = $inventory->tokenColor->slug;
                $config = $colorConfig[$slug] ?? $colorConfig['white'];
                $hasTokens = $inventory->quantity > 0;
            @endphp
            <div @class([
                'p-5 rounded-2xl flex flex-col items-center gap-3 border transition-all',
                'bg-surface-container-low border-outline-variant/5' => $hasTokens,
                'bg-surface-container-low/50 border-outline-variant/5 opacity-50' => !$hasTokens,
            ])>
                <div class="w-12 h-12 rounded-full {{ $config['bg'] }} {{ $config['glow'] }} flex items-center justify-center">
                    <span class="material-symbols-outlined {{ $config['iconText'] }}">{{ $config['icon'] }}</span>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-black font-display text-on-surface">{{ str_pad($inventory->quantity, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-[10px] uppercase font-bold {{ $config['text'] }} tracking-tighter">{{ $config['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
