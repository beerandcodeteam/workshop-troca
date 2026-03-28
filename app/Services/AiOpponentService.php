<?php

namespace App\Services;

use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchCompartmentCard;
use App\Models\MatchTokenInventory;
use App\Models\ParticipantType;
use App\Models\TokenColor;

class AiOpponentService
{
    public function __construct(
        private DiceService $diceService,
        private CardPurchaseService $cardPurchaseService,
        private TokenLimitService $tokenLimitService,
    ) {}

    public function executeTurn(GameMatch $match, ?DifficultyTier $difficultyTier = null): void
    {
        $aiType = ParticipantType::where('slug', 'ai')->firstOrFail();

        $result = $this->diceService->roll();

        if ($result === 'free') {
            $colors = ['red', 'green', 'white', 'yellow', 'blue'];
            $result = $colors[array_rand($colors)];
        }

        $this->diceService->applyRoll($match, $aiType->id, $result);

        $match->refresh();

        $this->tryPurchaseCard($match, $aiType->id);

        $match->refresh();

        if ($this->tokenLimitService->isOverLimit($match, $aiType->id)) {
            $this->returnRandomExcessTokens($match, $aiType->id);
        }

        $match->refresh();

        $this->endAiTurn($match, $aiType);
    }

    private function tryPurchaseCard(GameMatch $match, int $aiTypeId): void
    {
        $compartments = $match->compartments()->with('cards.card.tokens.tokenColor')->get();

        foreach ($compartments as $compartment) {
            $faceUpCard = $compartment->faceUpCard();
            if (! $faceUpCard) {
                continue;
            }

            if ($this->canAffordCard($match, $aiTypeId, $faceUpCard)) {
                $this->cardPurchaseService->purchaseCard($match, $aiTypeId, $faceUpCard->id);

                return;
            }
        }
    }

    private function canAffordCard(GameMatch $match, int $aiTypeId, MatchCompartmentCard $card): bool
    {
        $cardTokens = $card->card->tokens;

        foreach ($cardTokens as $cardToken) {
            $inventory = MatchTokenInventory::where('match_id', $match->id)
                ->where('participant_type_id', $aiTypeId)
                ->where('token_color_id', $cardToken->token_color_id)
                ->first();

            if (! $inventory || $inventory->quantity < $cardToken->quantity) {
                return false;
            }
        }

        return true;
    }

    private function returnRandomExcessTokens(GameMatch $match, int $aiTypeId): void
    {
        $excess = $this->tokenLimitService->getExcessCount($match, $aiTypeId);
        if ($excess <= 0) {
            return;
        }

        $inventories = MatchTokenInventory::where('match_id', $match->id)
            ->where('participant_type_id', $aiTypeId)
            ->where('quantity', '>', 0)
            ->get();

        $tokensToReturn = [];
        $remaining = $excess;

        foreach ($inventories as $inventory) {
            if ($remaining <= 0) {
                break;
            }

            $colorSlug = TokenColor::find($inventory->token_color_id)->slug;
            $returnQty = min($inventory->quantity, $remaining);
            $tokensToReturn[$colorSlug] = $returnQty;
            $remaining -= $returnQty;
        }

        if (! empty($tokensToReturn)) {
            $this->tokenLimitService->returnTokens($match, $aiTypeId, $tokensToReturn);
        }
    }

    private function endAiTurn(GameMatch $match, ParticipantType $aiType): void
    {
        if ($match->compartments_emptied >= 2) {
            app(MatchFinalizationService::class)->finalize($match);

            return;
        }

        $playerType = ParticipantType::where('slug', 'player')->firstOrFail();

        $match->update([
            'current_participant_type_id' => $playerType->id,
            'current_turn_number' => $match->current_turn_number + 1,
            'has_acted_this_turn' => false,
        ]);
    }
}
