<?php

namespace App\Console\Commands;

use App\Models\TradeEmbedding;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;

use function resource_path;

#[Signature('app:generate-trade-embeddings')]
#[Description('Command description')]
class GenerateTradeEmbeddings extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = File::allFiles(resource_path('knowledge-bases'));

        TradeEmbedding::query()->truncate();

        $chunks = collect($files)
            ->map(fn ($file) => $file->getContents())
            ->filter();

        $response = Embeddings::for($chunks->values()->all())
            ->generate(Lab::OpenAI);

        $chunks->each(function (string $content, int $index) use ($response, $files) {
            TradeEmbedding::create([
                'name' => $files[$index]->getFilename(),
                'content' => $content,
                'embedding' => $response->embeddings[$index],
            ]);
        });
    }
}
