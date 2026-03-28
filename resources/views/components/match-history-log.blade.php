@props([
    'turns' => collect(),
    'limit' => 10,
])

@php
    $recentTurns = $turns->sortByDesc('id')->take($limit);

    $actionConfig = [
        'roll_dice' => ['label' => 'Lançou Dado', 'color' => 'bg-secondary', 'icon' => 'casino'],
        'trade' => ['label' => 'Troca Efetuada', 'color' => 'bg-tertiary', 'icon' => 'swap_horiz'],
        'purchase_card' => ['label' => 'Carta Comprada', 'color' => 'bg-primary', 'icon' => 'shopping_cart'],
        'return_tokens' => ['label' => 'Tokens Devolvidos', 'color' => 'bg-warning', 'icon' => 'undo'],
    ];
@endphp

<div class="space-y-4">
    <label class="font-display text-[10px] uppercase tracking-[0.2em] text-on-surface-variant font-bold">Histórico Recente</label>

    <div class="space-y-2">
        @forelse ($recentTurns as $turn)
            @php
                $slug = $turn->turnActionType?->slug ?? 'roll_dice';
                $config = $actionConfig[$slug] ?? $actionConfig['roll_dice'];
                $actionData = $turn->action_data ?? [];
                $participant = $turn->participantType?->slug === 'ai' ? 'IA' : 'Jogador';
                $description = $actionData['description'] ?? '';
            @endphp

            <div class="flex items-start gap-3 p-3 bg-surface-container-lowest/50 rounded-lg border border-outline-variant/10">
                <div class="w-2 h-2 rounded-full {{ $config['color'] }} mt-1.5 shrink-0"></div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-on-surface">{{ $config['label'] }}</p>
                    @if ($description)
                        <p class="text-[10px] text-on-surface-variant truncate">{{ $description }}</p>
                    @else
                        <p class="text-[10px] text-on-surface-variant">Turno {{ $turn->turn_number }} — {{ $participant }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-3 bg-surface-container-lowest/50 rounded-lg border border-outline-variant/10">
                <p class="text-xs text-on-surface-variant text-center">Nenhuma ação registrada</p>
            </div>
        @endforelse
    </div>
</div>
