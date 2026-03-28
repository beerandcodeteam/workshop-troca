@props([
    'quotationCard',
    'playerInventories' => collect(),
])

@php
    $trades = $quotationCard->trades->sortBy('sort_order');

    $playerTokens = $playerInventories->mapWithKeys(fn ($inv) => [$inv->tokenColor->slug => $inv->quantity]);
@endphp

<div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/10 space-y-3">
    <div class="flex items-center justify-between mb-1">
        <span class="text-[10px] text-on-surface-variant uppercase font-bold tracking-widest">{{ $quotationCard->name }}</span>
    </div>

    @foreach ($trades as $trade)
        @php
            $leftItems = $trade->leftItems;
            $rightItems = $trade->rightItems;

            $canAfford = true;
            foreach ($leftItems as $item) {
                $slug = $item->tokenColor->slug;
                if (($playerTokens[$slug] ?? 0) < $item->quantity) {
                    $canAfford = false;
                    break;
                }
            }
        @endphp

        <div @class([
            'p-3 rounded-xl border transition-all',
            'bg-surface-container-lowest/40 border-outline-variant/10' => $canAfford,
            'bg-surface-container-lowest/20 border-outline-variant/5 opacity-50' => !$canAfford,
        ])>
            <div class="flex items-center justify-around gap-2">
                <div class="flex items-center gap-2">
                    @foreach ($leftItems as $item)
                        <div class="flex items-center gap-1">
                            <x-token-dot :color="$item->tokenColor->slug" size="sm" />
                            <span class="text-[10px] font-bold">{{ $item->quantity }}x</span>
                        </div>
                    @endforeach
                </div>

                <span class="material-symbols-outlined text-outline-variant text-sm">swap_horiz</span>

                <div class="flex items-center gap-2">
                    @foreach ($rightItems as $item)
                        <div class="flex items-center gap-1">
                            <x-token-dot :color="$item->tokenColor->slug" size="sm" />
                            <span class="text-[10px] font-bold">{{ $item->quantity }}x</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <button
                @class([
                    'w-full mt-2 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-colors',
                    'bg-surface-variant text-on-surface-variant hover:bg-primary hover:text-on-primary' => $canAfford,
                    'bg-surface-variant/50 text-on-surface-variant/50 cursor-not-allowed' => !$canAfford,
                ])
                @if (!$canAfford) disabled @endif
            >
                Executar Troca
            </button>
        </div>
    @endforeach
</div>
