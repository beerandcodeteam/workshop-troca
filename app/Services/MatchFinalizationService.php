<?php

namespace App\Services;

use App\Models\GameMatch;
use App\Models\MatchResultType;
use App\Models\MatchStatus;

class MatchFinalizationService
{
    public function finalize(GameMatch $match): void
    {
        $completedStatus = MatchStatus::completed()->firstOrFail();

        $resultType = $this->determineResult($match);

        $match->update([
            'match_status_id' => $completedStatus->id,
            'match_result_type_id' => $resultType->id,
            'completed_at' => now(),
        ]);
    }

    private function determineResult(GameMatch $match): MatchResultType
    {
        $slug = match (true) {
            $match->player_score > $match->ai_score => 'player_win',
            $match->ai_score > $match->player_score => 'ai_win',
            default => 'draw',
        };

        return MatchResultType::where('slug', $slug)->firstOrFail();
    }
}
