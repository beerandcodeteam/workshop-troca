<?php

namespace App\Ai\Tools;

use App\Models\GameMatch;
use App\Services\TokenLimitService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

use function app;

class ReturnTokensTool implements Tool
{
    public function __construct(private GameMatch $gameMatch) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Devolve tokens excedentes';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $tokenLimitService = app(TokenLimitService::class);
        try {
            $tokenLimitService->returnTokens($this->gameMatch, 2, $request['tokensToReturn']);
            $this->gameMatch->refresh();
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return $this->gameMatch->tokenInventories->load('tokenColor');
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'tokensToReturn' => $schema->object(
                properties: [
                    'red' => $schema->integer(),
                    'green' => $schema->integer(),
                    'white' => $schema->integer(),
                    'yellow' => $schema->integer(),
                    'blue' => $schema->integer(),
                ]
            ),
        ];
    }
}
