<?php

use App\Models\GameMatch;
use App\Models\MatchStatus;
use App\Models\ParticipantType;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] #[\Livewire\Attributes\Title('Partida')] class extends Component
{
    public GameMatch $match;

    public function mount(GameMatch $match): void
    {
        abort_unless($match->user_id === auth()->id(), 403);

        $completedStatus = MatchStatus::completed()->first();
        if ($completedStatus && $match->match_status_id === $completedStatus->id) {
            $this->redirect(route('arena.match.results', $match), navigate: true);
            return;
        }

        $this->match = $match->load([
            'user',
            'difficultyTier',
            'matchStatus',
            'currentParticipantType',
            'quotationCards.trades.leftItems.tokenColor',
            'quotationCards.trades.rightItems.tokenColor',
            'compartments.cards.card.tokens.tokenColor',
            'tokenInventories.tokenColor',
            'tokenInventories.participantType',
            'turns.participantType',
            'turns.turnActionType',
        ]);
    }

    #[Computed]
    public function playerInventories()
    {
        $playerType = ParticipantType::where('slug', 'player')->first();

        return $this->match->tokenInventories
            ->where('participant_type_id', $playerType?->id)
            ->sortBy(fn ($inv) => array_search($inv->tokenColor->slug, ['red', 'green', 'white', 'yellow', 'blue']));
    }

    #[Computed]
    public function totalTokens(): int
    {
        return $this->playerInventories->sum('quantity');
    }

    #[Computed]
    public function needsTokenReturn(): bool
    {
        return $this->totalTokens > 10;
    }

    #[Computed]
    public function currentTurnLabel(): string
    {
        $participant = $this->match->currentParticipantType;

        return match ($participant?->slug) {
            'player' => 'Sua Vez',
            'ai' => 'Vez da IA',
            default => 'Aguardando...',
        };
    }

    #[Computed]
    public function matchStats(): array
    {
        $turns = $this->match->turns;

        return [
            'total_actions' => $turns->count(),
            'dice_rolls' => $turns->filter(fn ($t) => $t->turnActionType?->slug === 'roll_dice')->count(),
            'trades' => $turns->filter(fn ($t) => $t->turnActionType?->slug === 'trade')->count(),
        ];
    }

};
?>

<div class="flex h-[calc(100vh-8rem)] overflow-hidden -mx-6 -mb-8">
    {{-- Left Sidebar: Match Summary & History --}}
    <aside class="h-full w-80 flex flex-col p-6 border-r border-outline-variant/15 shrink-0 overflow-y-auto">
        <div class="space-y-6 flex-1">
            {{-- Match Stats --}}
            <div class="space-y-4">
                <label class="font-display text-[10px] uppercase tracking-[0.2em] text-on-surface-variant font-bold">Resumo da Partida</label>
                <div class="grid grid-cols-1 gap-3">
                    <div class="bg-surface-container-low p-4 rounded-xl flex items-center justify-between hover:bg-surface-container transition-all">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">data_usage</span>
                            <span class="text-sm font-medium text-on-surface-variant">Ações Totais</span>
                        </div>
                        <span class="text-lg font-black font-display text-primary">{{ str_pad($this->matchStats['total_actions'], 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="bg-surface-container-low p-4 rounded-xl flex items-center justify-between hover:bg-surface-container transition-all">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-secondary">casino</span>
                            <span class="text-sm font-medium text-on-surface-variant">Lançamentos</span>
                        </div>
                        <span class="text-lg font-black font-display text-secondary">{{ str_pad($this->matchStats['dice_rolls'], 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="bg-surface-container-low p-4 rounded-xl flex items-center justify-between hover:bg-surface-container transition-all">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-tertiary">swap_horiz</span>
                            <span class="text-sm font-medium text-on-surface-variant">Trocas Realizadas</span>
                        </div>
                        <span class="text-lg font-black font-display text-tertiary">{{ str_pad($this->matchStats['trades'], 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>

            {{-- Turn Indicator --}}
            <div class="p-4 rounded-xl border {{ $this->match->currentParticipantType?->slug === 'player' ? 'bg-primary/10 border-primary/20' : 'bg-tertiary/10 border-tertiary/20' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined {{ $this->match->currentParticipantType?->slug === 'player' ? 'text-primary' : 'text-tertiary' }}">
                        {{ $this->match->currentParticipantType?->slug === 'player' ? 'person' : 'smart_toy' }}
                    </span>
                    <div>
                        <p class="text-xs font-bold text-on-surface">{{ $this->currentTurnLabel }}</p>
                        <p class="text-[10px] text-on-surface-variant">Turno {{ $this->match->current_turn_number ?? 1 }}</p>
                    </div>
                </div>
            </div>

            {{-- History Log --}}
            <x-match-history-log :turns="$this->match->turns" />
        </div>
    </aside>

    {{-- Main Content Area --}}
    <main class="flex-1 overflow-y-auto p-8 space-y-8">
        {{-- Token Inventory --}}
        <x-token-inventory :inventories="$this->playerInventories" />

        {{-- Card Compartments --}}
        <section class="space-y-4">
            <label class="font-display text-xs uppercase tracking-[0.3em] text-on-surface-variant font-bold">Compartimentos</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($this->match->compartments->sortBy('position') as $compartment)
                    <x-card-compartment :compartment="$compartment" :position="$compartment->position" />
                @endforeach
            </div>
        </section>

        {{-- Dice Roll Action Area --}}
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <div class="lg:col-span-12">
                <div class="bg-surface-container p-1 rounded-3xl border border-outline-variant/10">
                    <button class="w-full bg-gradient-to-br from-primary-dim to-primary rounded-[1.4rem] flex flex-col items-center justify-center gap-4 p-8 group transition-all hover:brightness-110 active:scale-[0.98] relative overflow-hidden">
                        <div class="w-20 h-20 rounded-2xl bg-on-primary/20 flex items-center justify-center rotate-12 group-hover:rotate-0 transition-transform duration-500">
                            <span class="material-symbols-outlined text-5xl text-on-primary font-black">casino</span>
                        </div>
                        <div class="text-center relative z-10">
                            <span class="block text-4xl font-black font-display text-on-primary uppercase tracking-tighter">Lançar Dado</span>
                            <span class="text-on-primary/70 text-sm font-medium tracking-widest uppercase mt-1 block">Custo: 1 Ação</span>
                        </div>
                    </button>
                </div>
            </div>
        </section>

        {{-- Active Quotation Cards --}}
        <section class="space-y-4">
            <label class="font-display text-xs uppercase tracking-[0.3em] text-on-surface-variant font-bold">Cotações Ativas (Mercado)</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($this->match->quotationCards as $quotationCard)
                    <x-quotation-card-display
                        :quotationCard="$quotationCard"
                        :playerInventories="$this->playerInventories"
                    />
                @endforeach
            </div>
        </section>

        {{-- Token Return UI (shown only when over 10 tokens) --}}
        @if ($this->needsTokenReturn)
            <livewire:arena.token-return :match="$this->match" :key="'token-return-' . $this->match->id" />
        @endif
    </main>
</div>
