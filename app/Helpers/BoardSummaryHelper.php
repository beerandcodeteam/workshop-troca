<?php

namespace App\Helpers;

use App\Models\GameMatch;
use App\Models\ParticipantType;

class BoardSummaryHelper
{
    public static function inventorySummary(GameMatch $match): string
    {
        $aiType = ParticipantType::where('slug', 'ai')->first();
        $inventories = $match->tokenInventories()->with('tokenColor')->where('participant_type_id', $aiType->id)->get();

        $tokens = [];
        $total = 0;
        foreach ($inventories as $inv) {
            $tokens[$inv->tokenColor->slug] = $inv->quantity;
            $total += $inv->quantity;
        }

        $lines = ['Suas fichas atualizadas:'];
        foreach (['red', 'green', 'white', 'yellow', 'blue'] as $color) {
            $lines[] = "  {$color}: ".($tokens[$color] ?? 0);
        }
        $lines[] = "  TOTAL: {$total}";

        $excess = max(0, $total - 10);
        if ($excess > 0) {
            $lines[] = "  ⚠ EXCESSO: {$excess} fichas acima do limite!";
        }

        return implode("\n", $lines);
    }
}