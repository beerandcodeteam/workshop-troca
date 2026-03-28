<?php

use App\Models\GameMatch;
use App\Models\MatchStatus;
use App\Models\ParticipantType;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('Resultados da Partida')] class extends Component
{
    public GameMatch $match;

    public function mount(GameMatch $match): void
    {
        abort_unless($match->user_id === auth()->id(), 403);

        $completedStatus = MatchStatus::completed()->first();
        abort_unless($completedStatus && $match->match_status_id === $completedStatus->id, 404);

        $this->match = $match->load([
            'user',
            'difficultyTier',
            'matchResultType',
            'compartments.cards.card',
            'compartments.cards.purchasedByParticipantType',
        ]);
    }

    #[Computed]
    public function resultLabel(): string
    {
        return match ($this->match->matchResultType?->slug) {
            'player_win' => 'Vitória!',
            'ai_win' => 'Derrota',
            'draw' => 'Empate',
            default => 'Resultado',
        };
    }

    #[Computed]
    public function resultIcon(): string
    {
        return match ($this->match->matchResultType?->slug) {
            'player_win' => 'emoji_events',
            'ai_win' => 'sentiment_dissatisfied',
            'draw' => 'handshake',
            default => 'info',
        };
    }

    #[Computed]
    public function resultColorClass(): string
    {
        return match ($this->match->matchResultType?->slug) {
            'player_win' => 'text-success',
            'ai_win' => 'text-danger',
            'draw' => 'text-warning',
            default => 'text-on-surface',
        };
    }

    #[Computed]
    public function playerCards(): \Illuminate\Support\Collection
    {
        $playerType = ParticipantType::where('slug', 'player')->first();

        return $this->match->compartments
            ->flatMap->cards
            ->where('is_purchased', true)
            ->where('purchased_by_participant_type_id', $playerType?->id);
    }

    #[Computed]
    public function aiCards(): \Illuminate\Support\Collection
    {
        $aiType = ParticipantType::where('slug', 'ai')->first();

        return $this->match->compartments
            ->flatMap->cards
            ->where('is_purchased', true)
            ->where('purchased_by_participant_type_id', $aiType?->id);
    }
};
?>

<div class="py-6">
    {{-- Result Announcement --}}
    <div class="text-center mb-8">
        <span class="material-symbols-outlined text-7xl {{ $this->resultColorClass }}">
            {{ $this->resultIcon }}
        </span>
        <h1 class="font-display text-4xl font-bold {{ $this->resultColorClass }} mt-4">
            {{ $this->resultLabel }}
        </h1>
        <p class="text-on-surface-variant mt-2">
            Partida #{{ $match->id }} — {{ $match->difficultyTier->name }}
        </p>
    </div>

    {{-- Scores Overview --}}
    <div class="grid grid-cols-2 gap-6 max-w-2xl mx-auto mb-8">
        <x-card class="text-center">
            <span class="text-xs uppercase tracking-widest text-primary font-bold">Jogador</span>
            <p class="text-5xl font-black font-display text-primary mt-2">{{ $match->player_score }}</p>
            <p class="text-sm text-on-surface-variant mt-1">{{ $match->player_cards_purchased }} {{ $match->player_cards_purchased === 1 ? 'carta' : 'cartas' }}</p>
        </x-card>
        <x-card class="text-center">
            <span class="text-xs uppercase tracking-widest text-tertiary font-bold">IA</span>
            <p class="text-5xl font-black font-display text-tertiary mt-2">{{ $match->ai_score }}</p>
            <p class="text-sm text-on-surface-variant mt-1">{{ $match->ai_cards_purchased }} {{ $match->ai_cards_purchased === 1 ? 'carta' : 'cartas' }}</p>
        </x-card>
    </div>

    {{-- XP Earned --}}
    <div class="max-w-2xl mx-auto mb-8">
        <x-card class="text-center">
            <div class="flex items-center justify-center gap-3">
                <span class="material-symbols-outlined text-3xl text-warning">star</span>
                <div>
                    <span class="text-sm uppercase tracking-widest text-on-surface-variant font-bold">XP Ganho</span>
                    <p class="text-3xl font-black font-display text-warning">+{{ $match->xp_earned }} XP</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Card Breakdowns --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl mx-auto mb-8">
        {{-- Player Cards --}}
        <x-card>
            <h3 class="font-display text-lg font-semibold text-on-surface mb-4">
                <span class="material-symbols-outlined text-primary align-middle mr-1">person</span>
                Cartas do Jogador
            </h3>
            @if($this->playerCards->isEmpty())
                <p class="text-sm text-on-surface-variant">Nenhuma carta comprada.</p>
            @else
                <div class="space-y-3">
                    @foreach($this->playerCards as $matchCard)
                        <div class="flex items-center justify-between bg-surface-container-low p-3 rounded-xl">
                            <div class="flex items-center gap-3">
                                <span class="font-display font-bold text-on-surface">Carta {{ $matchCard->card->number }}</span>
                                <div class="flex gap-0.5">
                                    @for($i = 0; $i < $matchCard->card->star_count; $i++)
                                        <span class="material-symbols-outlined text-warning text-sm">star</span>
                                    @endfor
                                </div>
                            </div>
                            <span class="font-display font-bold text-primary">{{ $matchCard->points_scored ?? 0 }} pts</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        {{-- AI Cards --}}
        <x-card>
            <h3 class="font-display text-lg font-semibold text-on-surface mb-4">
                <span class="material-symbols-outlined text-tertiary align-middle mr-1">smart_toy</span>
                Cartas da IA
            </h3>
            @if($this->aiCards->isEmpty())
                <p class="text-sm text-on-surface-variant">Nenhuma carta comprada.</p>
            @else
                <div class="space-y-3">
                    @foreach($this->aiCards as $matchCard)
                        <div class="flex items-center justify-between bg-surface-container-low p-3 rounded-xl">
                            <div class="flex items-center gap-3">
                                <span class="font-display font-bold text-on-surface">Carta {{ $matchCard->card->number }}</span>
                                <div class="flex gap-0.5">
                                    @for($i = 0; $i < $matchCard->card->star_count; $i++)
                                        <span class="material-symbols-outlined text-warning text-sm">star</span>
                                    @endfor
                                </div>
                            </div>
                            <span class="font-display font-bold text-tertiary">{{ $matchCard->points_scored ?? 0 }} pts</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-center gap-4">
        <x-button href="{{ route('arena.match-setup') }}" wire:navigate>
            <span class="material-symbols-outlined text-lg">replay</span>
            Jogar Novamente
        </x-button>
        <x-button href="{{ route('arena') }}" variant="secondary" wire:navigate>
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Voltar à Arena
        </x-button>
    </div>
</div>
