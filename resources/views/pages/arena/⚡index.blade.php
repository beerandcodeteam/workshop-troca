<?php

use App\Models\GameMatch;
use App\Models\MatchStatus;
use App\Models\PlayerRank;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('Arena')] class extends Component
{
    #[Computed]
    public function player()
    {
        return auth()->user();
    }

    #[Computed]
    public function rank(): ?PlayerRank
    {
        return PlayerRank::findForXp($this->player->total_xp ?? 0);
    }

    #[Computed]
    public function nextRank(): ?PlayerRank
    {
        return PlayerRank::where('min_xp', '>', $this->player->total_xp ?? 0)
            ->orderBy('min_xp')
            ->first();
    }

    #[Computed]
    public function xpProgress(): int
    {
        $currentXp = $this->player->total_xp ?? 0;
        $currentMin = $this->rank?->min_xp ?? 0;
        $nextMin = $this->nextRank?->min_xp;

        if (! $nextMin) {
            return 100;
        }

        $range = $nextMin - $currentMin;

        if ($range <= 0) {
            return 100;
        }

        return (int) min(100, floor(($currentXp - $currentMin) / $range * 100));
    }

    #[Computed]
    public function inProgressMatch(): ?GameMatch
    {
        $statusId = MatchStatus::inProgress()->value('id');

        if (! $statusId) {
            return null;
        }

        return GameMatch::where('user_id', $this->player->id)
            ->where('match_status_id', $statusId)
            ->latest()
            ->first();
    }
};
?>

<div class="py-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-on-surface">Arena</h1>
        <p class="mt-1 text-on-surface-variant">Bem-vindo de volta, <span class="font-semibold text-on-surface">{{ $this->player->username }}</span></p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Player Stats Card --}}
        <x-card class="lg:col-span-2">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex size-14 items-center justify-center rounded-full bg-surface-container text-xl font-bold text-primary">
                        {{ strtoupper(mb_substr($this->player->username, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="font-display text-xl font-bold text-on-surface">{{ $this->player->username }}</h2>
                        @if($this->rank)
                            <x-badge variant="primary">{{ $this->rank->name }}</x-badge>
                        @endif
                    </div>
                </div>
            </div>

            {{-- XP Progress --}}
            <div class="mt-6">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-on-surface-variant">XP Total</span>
                    <span class="font-semibold text-on-surface">{{ number_format($this->player->total_xp ?? 0) }} XP</span>
                </div>
                <div class="mt-2 h-3 w-full overflow-hidden rounded-full bg-surface-container">
                    <div
                        class="h-full rounded-full bg-primary transition-all duration-500"
                        style="width: {{ $this->xpProgress }}%"
                    ></div>
                </div>
                <div class="mt-1 flex items-center justify-between text-xs text-on-surface-variant">
                    @if($this->rank)
                        <span>{{ $this->rank->name }}</span>
                    @endif
                    @if($this->nextRank)
                        <span>{{ $this->nextRank->name }} ({{ number_format($this->nextRank->min_xp) }} XP)</span>
                    @else
                        <span>Rank Maximo</span>
                    @endif
                </div>
            </div>
        </x-card>

        {{-- Actions Card --}}
        <x-card>
            <h3 class="font-display text-lg font-semibold text-on-surface mb-4">Ações</h3>

            <div class="space-y-3">
                @if($this->inProgressMatch)
                    <x-button :href="route('arena.match.show', $this->inProgressMatch->id)" variant="success" class="w-full justify-center">
                        Retomar Partida
                    </x-button>
                @endif

                <x-button :href="route('arena.match-setup')" class="w-full justify-center">
                    Nova Partida
                </x-button>
            </div>
        </x-card>
    </div>
</div>
