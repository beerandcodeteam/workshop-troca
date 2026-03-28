<?php

namespace App\Ai\Tools;

use App\Models\GameMatch;
use App\Models\ParticipantType;
use App\Services\TokenLimitService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

use function app;

class GetExcessTokenCountTool implements Tool
{
    public function __construct(private GameMatch $gameMatch) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Recupera a quantidade de tokens excedentes';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $tokenLimitService = app(TokenLimitService::class);
        $participantTypeId = ParticipantType::where('slug', 'ai')->first()->id;

        return $tokenLimitService->getExcessCount($this->gameMatch, $participantTypeId);
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
