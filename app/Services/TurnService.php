<?php

namespace App\Services;

use App\Models\GameMatch;
use App\Models\ParticipantType;
use InvalidArgumentException;

class TurnService
{
    public function __construct(
        private MatchFinalizationService $matchFinalizationService,
        private AiOpponentService $aiOpponentService,
        private TokenLimitService $tokenLimitService,
    ) {}

    public function endTurn(GameMatch $match): void
    {
        if (! $match->has_acted_this_turn) {
            throw new InvalidArgumentException('O participante deve realizar uma ação antes de encerrar o turno.');
        }

        if ($this->tokenLimitService->isOverLimit($match, $match->current_participant_type_id)) {
            throw new InvalidArgumentException('O participante está acima do limite de tokens. Devolva tokens antes de encerrar o turno.');
        }

        if ($match->compartments_emptied >= 2) {
            $this->matchFinalizationService->finalize($match);

            return;
        }

        $playerType = ParticipantType::where('slug', 'player')->first();
        $aiType = ParticipantType::where('slug', 'ai')->first();

        $nextParticipantId = $match->current_participant_type_id === $playerType->id
            ? $aiType->id
            : $playerType->id;

        $match->update([
            'current_participant_type_id' => $nextParticipantId,
            'current_turn_number' => $match->current_turn_number + 1,
            'has_acted_this_turn' => false,
        ]);

        if ($nextParticipantId === $aiType->id) {
            $this->aiOpponentService->executeTurn($match);
        }
    }

    /**
     * @return array{current_participant: string, has_acted: bool, can_end_turn: bool, is_over_limit: bool}
     */
    public function getCurrentTurnState(GameMatch $match): array
    {
        $participantType = $match->currentParticipantType;
        $isOverLimit = $this->tokenLimitService->isOverLimit($match, $match->current_participant_type_id);

        return [
            'current_participant' => $participantType?->slug ?? 'unknown',
            'has_acted' => $match->has_acted_this_turn,
            'can_end_turn' => $match->has_acted_this_turn && ! $isOverLimit,
            'is_over_limit' => $isOverLimit,
        ];
    }
}
