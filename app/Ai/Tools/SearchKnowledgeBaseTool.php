<?php

namespace App\Ai\Tools;

use App\Models\TradeEmbedding;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchKnowledgeBaseTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Busca estratégias e conhecimento relevante na base de conhecimento do jogo TROCA a partir de uma busca de acordo com a situação necessária';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $embedding = Str::of($request['query'])->toEmbeddings(provider: Lab::OpenAI);

        $results = TradeEmbedding::query()
            ->whereVectorSimilarTo('embedding', $embedding, 0.5)
            ->limit(3)
            ->get();

        if ($results->isEmpty()) {
            return 'Nenhuma estratégia encontrada na base de conhecimento.';
        }

        return $results->pluck('content')->implode("\n\n -- \n\n");
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->required()
                ->description('Pergunta ou tema para buscar na base de conhecimento de estrategias.'),
        ];
    }
}
