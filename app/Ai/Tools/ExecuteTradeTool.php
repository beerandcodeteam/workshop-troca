<?php

namespace App\Ai\Tools;

use App\Models\GameMatch;
use App\Models\ParticipantType;
use App\Services\TradeService;
use Exception;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ExecuteTradeTool implements Tool
{
    public function __construct(private GameMatch $gameMatch) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Realiza uma troca com base em uma carta de cotação e a direção';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $tradeService = app(TradeService::class);
        try {
            $participantTypeId = ParticipantType::where('slug', 'ai')->first()->id;

            $tradeService->executeTrade(
                $this->gameMatch,
                $participantTypeId,
                $request['quotationCardTradeId'],
                $request['direction']
            );
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->gameMatch->tokenInventories->load('tokenColor');
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'quotationCardTradeId' => $schema->integer()->required()->description('Id do trade que deseja realizar'),
            'direction' => $schema->string()->required()->description('Direção do trade que deseja realizar [left, right]'),
        ];
    }
}
