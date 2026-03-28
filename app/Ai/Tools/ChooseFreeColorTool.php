<?php

namespace App\Ai\Tools;

use App\Models\GameMatch;
use App\Models\ParticipantType;
use App\Services\DiceService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

use function app;

class ChooseFreeColorTool implements Tool
{
    public function __construct(private GameMatch $gameMatch) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Quando o dado rolar free, voce pode escolher a melhor opção livremente para sua estrategia utilizando essa tool';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $diceService = app(DiceService::class);
        $participantTypeId = ParticipantType::where('slug', 'ai')->first()->id;

        try {
            $diceService->applyRoll($this->gameMatch, $participantTypeId, $request['color']);
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
            'color' => $schema
                ->string()
                ->required()
                ->description("Escolha uma cor livre entre as opções ['red', 'green', 'white', 'yellow', 'blue']"),
        ];
    }
}
