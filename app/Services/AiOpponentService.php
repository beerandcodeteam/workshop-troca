<?php

namespace App\Services;

use App\Ai\Agents\EasyAgent;
use App\Ai\Agents\HardAgent;
use App\Ai\Agents\MediumAgent;
use App\Models\GameMatch;

class AiOpponentService
{
    public function executeTurn(GameMatch $match)
    {
        $agentClass = match ($match->difficultyTier->slug) {
            'padrao-primario' => EasyAgent::class,
            'cadeia-cruzada' => MediumAgent::class,
            'mestre-do-caos' => HardAgent::class,
        };

        $agent = app($agentClass, [
            'gameMatch' => $match,
        ]);

        return $agent->continue($match->id, as: auth()->user())->stream('Sua Vez! Faça a sua jogada e explique a estrategia');
    }
}
