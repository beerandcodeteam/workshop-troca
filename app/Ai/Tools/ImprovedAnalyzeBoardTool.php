<?php

namespace App\Ai\Tools;

use App\Models\GameMatch;
use App\Models\ParticipantType;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use function implode;
use function max;

class ImprovedAnalyzeBoardTool implements Tool
{


    public function __construct(private GameMatch $gameMatch)
    {
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'A description of the tool.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $match = $this->gameMatch->refresh()->load(
            'difficultyTier',
            'quotationCards.trades.items.tokenColor',
            'quotationCards.trades.items.tradeSide',
            'tokenInventories.tokenColor',
            'turns',
            'compartments.faceUpCards.card.tokens.tokenColor'
        );

        $aiType = ParticipantType::where('slug', 'ai')->first();
        $playerType = ParticipantType::where('slug', 'player')->first();

        $lines = [];
        $lines[] = '===== ESTADO DO TABULEIRO =====';
        $lines[] = '';

        $aiInventory = $match->tokenInventories->where('participant_type_id', $aiType->id);
        $playerInventory = $match->tokenInventories->where('participant_type_id', $playerType->id);

        $aiTokens = [];
        $aiTotal = 0;
        foreach ($aiInventory as $inventory) {
            $aiTokens[$inventory->tokenColor->slug] = $inventory->quantity;
            $aiTotal += $inventory->quantity;
        }

        $playerTokens = [];
        foreach ($playerInventory as $inventory) {
            $playerTokens[$inventory->tokenColor->slug] = $inventory->quantity;
        }

        $tokens = ['red', 'green', 'white', 'yellow', 'blue'];

        $lines[] = '## SUAS FICHAS (IA)';

        foreach($tokens as $color)
        {
            $lines[] = "{$color}: " . ($aiTokens[$color] ?? 0);
        }
        $lines[] = " Total: {$aiTotal}";
        $lines[] = '';

        $lines[] = '## FICHAS DO JOGADOR';

        foreach($tokens as $color)
        {
            $lines[] = "{$color}: " . ($playerTokens[$color] ?? 0);
        }
        $lines[] = '';

        $lines[] = "## PLACAR: IA {$match->ai_score} x {$match->player_score} Jogador";
        $lines[] = "## TURNO: {$match->current_turn_number}";
        $lines[] = '';

        $lines[] = '## CARTAS NOS COMPARTIMENTOS (disponíveis para compra)';
        foreach ($match->compartments->sortBy('position') as $compartment) {
            $faceUp = $compartment->faceUpCard();
            if (! $faceUp) {
                $lines[] = "  Compartimento {$compartment->position}: VAZIO";

                continue;
            }

            $card = $faceUp->card;
            $cost = [];
            foreach ($card->tokens as $token) {
                $cost[] = "{$token->quantity}x {$token->tokenColor->slug}";
            }

            $deficit = [];
            foreach ($card->tokens as $token) {
                $needed = $token->quantity;
                $have = $aiTokens[$token->tokenColor->slug] ?? 0;
                $missing = max(0, $needed - $have);
                if ($missing > 0) {
                    $deficit[] = "{$missing}x {$token->tokenColor->slug}";
                }
            }

            $starLabel = $card->star_count > 0 ? " ({$card->star_count} estrela(s))" : '';
            $deficitLabel = empty($deficit) ? '→ PODE COMPRAR!' : '→ falta: '.implode(', ', $deficit);

            $lines[] = "  Compartimento {$compartment->position} | purchase_card(matchCompartmentCardId={$faceUp->id})";
            $lines[] = '    Custo: '.implode(', ', $cost).$starLabel;
            $lines[] = "    {$deficitLabel}";
        }
        $lines[] = '';

        $lines[] = '## TROCAS DISPONÍVEIS';
        foreach ($match->quotationCards as $quotationCard) {
            $lines[] = "  --- {$quotationCard->name} ---";
            foreach ($quotationCard->trades as $trade) {
                $leftItems = [];
                $rightItems = [];
                foreach ($trade->items as $item) {
                    $entry = "{$item->quantity}x {$item->tokenColor->slug}";
                    if ($item->tradeSide->slug === 'left') {
                        $leftItems[] = $entry;
                    } else {
                        $rightItems[] = $entry;
                    }
                }

                $leftStr = empty($leftItems) ? '(nada)' : implode(' + ', $leftItems);
                $rightStr = empty($rightItems) ? '(nada)' : implode(' + ', $rightItems);

                $canLtr = $this->canAfford($trade, 'left', $aiTokens);
                $canRtl = $this->canAfford($trade, 'right', $aiTokens);

                $ltrLabel = $canLtr ? 'SIM' : 'NÃO';
                $rtlLabel = $canRtl ? 'SIM' : 'NÃO';

                $lines[] = "  Trade id={$trade->id}: left: [{$leftStr}] ↔ right: [{$rightStr}]";
                $lines[] = "    left_to_right (entrega left, recebe right): pode={$ltrLabel}";
                $lines[] = "    right_to_left (entrega right, recebe left): pode={$rtlLabel}";
            }
        }

        return implode("\n", $lines);
    }

    private function canAfford($trade, string $giveSide, array $tokens): bool
    {
        foreach ($trade->items as $item) {
            if ($item->tradeSide->slug === $giveSide) {
                $have = $tokens[$item->tokenColor->slug] ?? 0;
                if ($have < $item->quantity) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'value' => $schema->string()->required(),
        ];
    }
}
