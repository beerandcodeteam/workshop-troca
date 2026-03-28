<?php

namespace App\Services;

use App\Models\GameMatch;
use App\Models\MatchResultType;
use App\Models\MatchStatus;
use App\Models\PlayerRank;

class MatchFinalizationService
{
    public function finalizeMatch(GameMatch $match): void
    {
        $resultType = $this->determineResult($match);
        $xpEarned = $this->calculateXp($match, $resultType);

        $completedStatus = MatchStatus::completed()->firstOrFail();

        $match->update([
            'match_status_id' => $completedStatus->id,
            'match_result_type_id' => $resultType->id,
            'xp_earned' => $xpEarned,
            'completed_at' => now(),
        ]);

        $user = $match->user;
        $user->increment('total_xp', $xpEarned);

        $newRank = PlayerRank::findForXp($user->total_xp);
        if ($newRank && $user->player_rank_id !== $newRank->id) {
            $user->update(['player_rank_id' => $newRank->id]);
        }
    }

    public function finalize(GameMatch $match): void
    {
        $this->finalizeMatch($match);
    }

    private function determineResult(GameMatch $match): MatchResultType
    {
        if ($match->player_score > $match->ai_score) {
            $slug = 'player_win';
        } elseif ($match->ai_score > $match->player_score) {
            $slug = 'ai_win';
        } else {
            $slug = $this->resolveTiebreaker($match);
        }

        return MatchResultType::where('slug', $slug)->firstOrFail();
    }

    private function resolveTiebreaker(GameMatch $match): string
    {
        if ($match->player_cards_purchased < $match->ai_cards_purchased) {
            return 'player_win';
        }

        if ($match->ai_cards_purchased < $match->player_cards_purchased) {
            return 'ai_win';
        }

        return 'draw';
    }

    private function calculateXp(GameMatch $match, MatchResultType $resultType): int
    {
        $tier = $match->difficultyTier;
        $baseXp = $tier->base_xp_reward;
        $winBonus = $resultType->slug === 'player_win' ? $tier->win_bonus_xp : 0;

        return $baseXp + $winBonus;
    }
}
