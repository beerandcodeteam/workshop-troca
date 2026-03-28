<?php

namespace App\Ai\Tools;

use App\Models\GameMatch;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class AnalyzeBoardTool implements Tool
{
    public function __construct(private GameMatch $gameMatch) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Verifica o estado atual do board da partida';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        return dd($this->gameMatch->refresh()->load(
            'difficultyTier',
            'quotationCards.trades.items.tokenColor',
            'quotationCards.trades.items.tradeSide',
            'tokenInventories.tokenColor',
            'turns',
            'compartments.faceUpCards.card.tokens.tokenColor'
        )->toArray());
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
        ];
    }
}
