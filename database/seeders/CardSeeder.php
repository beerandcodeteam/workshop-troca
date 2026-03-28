<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\CardToken;
use App\Models\TokenColor;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $colors = TokenColor::all()->keyBy('slug');

        // 45 cards: each requires exactly 5 tokens total
        // Format: [number => [star_count, [red, green, white, yellow, blue]]]
        $cards = [
            // 0-star cards (30 cards)
            1 => [0, ['red' => 2, 'green' => 1, 'white' => 1, 'blue' => 1]],
            2 => [0, ['red' => 1, 'green' => 2, 'white' => 1, 'yellow' => 1]],
            3 => [0, ['red' => 1, 'green' => 1, 'white' => 2, 'blue' => 1]],
            4 => [0, ['red' => 1, 'green' => 1, 'yellow' => 2, 'blue' => 1]],
            5 => [0, ['red' => 1, 'white' => 1, 'yellow' => 1, 'blue' => 2]],
            6 => [0, ['green' => 2, 'white' => 1, 'yellow' => 1, 'blue' => 1]],
            7 => [0, ['red' => 1, 'green' => 1, 'white' => 1, 'yellow' => 1, 'blue' => 1]],
            8 => [0, ['red' => 2, 'green' => 2, 'white' => 1]],
            9 => [0, ['red' => 2, 'yellow' => 2, 'blue' => 1]],
            10 => [0, ['green' => 2, 'white' => 2, 'yellow' => 1]],
            11 => [0, ['red' => 1, 'white' => 2, 'yellow' => 1, 'blue' => 1]],
            12 => [0, ['red' => 1, 'green' => 1, 'yellow' => 1, 'blue' => 2]],
            13 => [0, ['green' => 1, 'white' => 1, 'yellow' => 2, 'blue' => 1]],
            14 => [0, ['red' => 2, 'green' => 1, 'yellow' => 1, 'blue' => 1]],
            15 => [0, ['red' => 1, 'green' => 2, 'white' => 1, 'blue' => 1]],
            16 => [0, ['red' => 1, 'green' => 1, 'white' => 1, 'yellow' => 2]],
            17 => [0, ['white' => 2, 'yellow' => 1, 'blue' => 2]],
            18 => [0, ['red' => 1, 'green' => 2, 'yellow' => 2]],
            19 => [0, ['red' => 2, 'white' => 1, 'yellow' => 1, 'blue' => 1]],
            20 => [0, ['green' => 1, 'white' => 2, 'yellow' => 1, 'blue' => 1]],
            21 => [0, ['red' => 1, 'green' => 1, 'white' => 2, 'yellow' => 1]],
            22 => [0, ['red' => 1, 'white' => 1, 'yellow' => 2, 'blue' => 1]],
            23 => [0, ['green' => 2, 'white' => 1, 'yellow' => 2]],
            24 => [0, ['red' => 2, 'green' => 1, 'white' => 2]],
            25 => [0, ['red' => 1, 'yellow' => 2, 'blue' => 2]],
            26 => [0, ['green' => 1, 'white' => 1, 'yellow' => 1, 'blue' => 2]],
            27 => [0, ['red' => 2, 'green' => 1, 'white' => 1, 'yellow' => 1]],
            28 => [0, ['green' => 1, 'white' => 2, 'blue' => 2]],
            29 => [0, ['red' => 1, 'green' => 2, 'white' => 2]],
            30 => [0, ['red' => 2, 'white' => 2, 'blue' => 1]],
            // 1-star cards (10 cards)
            31 => [1, ['red' => 1, 'green' => 1, 'white' => 1, 'yellow' => 1, 'blue' => 1]],
            32 => [1, ['red' => 2, 'green' => 1, 'yellow' => 1, 'blue' => 1]],
            33 => [1, ['red' => 1, 'green' => 2, 'white' => 1, 'yellow' => 1]],
            34 => [1, ['green' => 1, 'white' => 2, 'yellow' => 1, 'blue' => 1]],
            35 => [1, ['red' => 1, 'white' => 1, 'yellow' => 2, 'blue' => 1]],
            36 => [1, ['red' => 1, 'green' => 1, 'white' => 1, 'blue' => 2]],
            37 => [1, ['red' => 2, 'green' => 2, 'blue' => 1]],
            38 => [1, ['green' => 1, 'white' => 2, 'yellow' => 2]],
            39 => [1, ['red' => 1, 'yellow' => 2, 'blue' => 2]],
            40 => [1, ['red' => 2, 'white' => 1, 'yellow' => 1, 'blue' => 1]],
            // 2-star cards (5 cards)
            41 => [2, ['red' => 1, 'green' => 1, 'white' => 1, 'yellow' => 1, 'blue' => 1]],
            42 => [2, ['red' => 2, 'green' => 1, 'white' => 1, 'blue' => 1]],
            43 => [2, ['red' => 1, 'green' => 2, 'white' => 1, 'yellow' => 1]],
            44 => [2, ['green' => 1, 'white' => 1, 'yellow' => 2, 'blue' => 1]],
            45 => [2, ['red' => 1, 'green' => 1, 'yellow' => 1, 'blue' => 2]],
        ];

        foreach ($cards as $number => [$starCount, $tokens]) {
            $card = Card::updateOrCreate(
                ['number' => $number],
                ['star_count' => $starCount],
            );

            $card->tokens()->delete();

            foreach ($tokens as $colorSlug => $qty) {
                CardToken::create([
                    'card_id' => $card->id,
                    'token_color_id' => $colors[$colorSlug]->id,
                    'quantity' => $qty,
                ]);
            }
        }
    }
}
