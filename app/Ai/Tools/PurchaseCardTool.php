<?php

namespace App\Ai\Tools;

use App\Models\GameMatch;
use App\Services\CardPurchaseService;
use Exception;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

use function app;

class PurchaseCardTool implements Tool
{
    public function __construct(private GameMatch $gameMatch) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Realiza a compra de uma carta e ganha pontos.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $cardPurchaseService = app(CardPurchaseService::class);
        try {
            $cardPurchaseService->purchaseCard($this->gameMatch, 2, $request['matchCompartmentCardId']);
            $this->gameMatch->refresh();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->gameMatch;
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'matchCompartmentCardId' => $schema
                ->integer()
                ->required()
                ->description('Id do carta no compartimento da partida'),
        ];
    }
}
