<?php

namespace App\Ai\Tools;

use App\Models\GameMatch;
use App\Models\ParticipantType;
use App\Services\DiceService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class RollDiceTool implements Tool
{
    public function __construct(private GameMatch $gameMatch) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Rola o dado e aplica os dados do game.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $diceService = app(DiceService::class);
        $roll = $diceService->roll();

        if ($roll == 'free') {
            return "Selecione livremente 1 cor ['red', 'green', 'white', 'yellow', 'blue']";
        }

        $participantTypeId = ParticipantType::where('slug', 'ai')->first()->id;

        try {
            $diceService->applyRoll($this->gameMatch, $participantTypeId, $roll);
            $this->gameMatch->refresh();
        } catch (\Exception $e) {
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
        ];
    }
}
