<?php

namespace App\Services;

use App\Ai\Agents\EasyAgent;
use App\Models\GameMatch;

class AiOpponentService
{
    public function executeTurn(GameMatch $match)
    {
        $agentClass = match ($match->difficultyTier->slug) {
            'padrao-primario' => EasyAgent::class,
            'cadeia-cruzada' => 'hello world',
            'mestre-do-caos' => 'hello world'
        };

        $agent = app($agentClass, [
            'gameMatch' => $match,
        ]);

        return $agent->prompt('Sua vez!');
    }
}
