<?php

namespace App\Ai\Tools;

use App\Models\GameMatch;
use App\Services\TurnService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

use function app;

class EndTurnTool implements Tool
{
    public function __construct(private GameMatch $gameMatch) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Finaliza o turno da IA.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $turnService = app(TurnService::class);
        try {
            $turnService->endTurn($this->gameMatch);
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

        return 'Turno encerrado';
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
