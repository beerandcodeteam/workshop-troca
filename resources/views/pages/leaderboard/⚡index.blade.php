<?php

use App\Models\MatchResultType;
use App\Models\MatchStatus;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app')] #[Title('Leaderboard')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function players()
    {
        $completedStatusId = MatchStatus::completed()->value('id');
        $playerWinId = MatchResultType::where('slug', 'player_win')->value('id');

        return User::query()
            ->select('users.*')
            ->withCount([
                'matches as matches_played' => function ($query) use ($completedStatusId) {
                    $query->where('match_status_id', $completedStatusId);
                },
                'matches as total_wins' => function ($query) use ($completedStatusId, $playerWinId) {
                    $query->where('match_status_id', $completedStatusId)
                        ->where('match_result_type_id', $playerWinId);
                },
            ])
            ->with('playerRank')
            ->orderByDesc('total_xp')
            ->paginate(20);
    }
};
?>

<div class="py-6">
    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-on-surface">Leaderboard</h1>
        <p class="mt-1 text-on-surface-variant">Ranking dos jogadores por XP</p>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant/15">
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">#</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Jogador</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Rank</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-on-surface-variant">Partidas</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wider text-on-surface-variant">Vitórias</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-on-surface-variant">XP Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($this->players as $index => $player)
                        <tr @class([
                            'transition-colors',
                            'bg-primary/5 border-l-2 border-l-primary' => $player->id === auth()->id(),
                            'hover:bg-surface-container-low' => $player->id !== auth()->id(),
                        ]) data-player-id="{{ $player->id }}">
                            <td class="px-4 py-3">
                                <span class="font-display font-bold text-on-surface-variant">
                                    {{ ($this->players->currentPage() - 1) * $this->players->perPage() + $index + 1 }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 items-center justify-center rounded-full bg-surface-container text-sm font-bold text-primary">
                                        {{ strtoupper(mb_substr($player->username, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-on-surface">{{ $player->username }}</span>
                                    @if($player->id === auth()->id())
                                        <x-badge variant="primary" size="sm">Você</x-badge>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($player->playerRank)
                                    <x-badge>{{ $player->playerRank->name }}</x-badge>
                                @else
                                    <span class="text-on-surface-variant text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-display font-bold text-on-surface">{{ $player->matches_played }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-display font-bold text-success">{{ $player->total_wins }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-display font-bold text-warning">{{ number_format($player->total_xp) }} XP</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($this->players->hasPages())
            <div class="mt-4 border-t border-outline-variant/15 pt-4">
                {{ $this->players->links() }}
            </div>
        @endif
    </x-card>
</div>
