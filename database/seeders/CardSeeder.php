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

        // 44 purchase cards from the original board game SQL data
        // Format: [number => [star_count, [color_slug => quantity]]]
        // Mapping: amarelo=yellow, azul=blue, branco=white, verde=green, vermelho=red, estrela=star
        $cards = [
            // 0-star cards
            1 => [0, ['white' => 3, 'red' => 2]],
            2 => [0, ['yellow' => 1, 'white' => 2, 'red' => 2]],
            3 => [0, ['yellow' => 2, 'white' => 1, 'red' => 2]],
            4 => [0, ['yellow' => 2, 'blue' => 1, 'green' => 2]],
            5 => [0, ['yellow' => 3, 'green' => 2]],
            6 => [0, ['yellow' => 3, 'white' => 2]],
            7 => [0, ['blue' => 2, 'white' => 1, 'green' => 2]],
            8 => [0, ['blue' => 2, 'green' => 2, 'red' => 1]],
            9 => [0, ['yellow' => 1, 'white' => 2, 'green' => 2]],
            10 => [0, ['yellow' => 1, 'blue' => 1, 'white' => 1, 'green' => 1, 'red' => 1]],
            12 => [0, ['yellow' => 1, 'blue' => 1, 'white' => 1, 'green' => 1, 'red' => 1]],
            13 => [0, ['blue' => 3, 'green' => 2]],
            14 => [0, ['blue' => 2, 'white' => 2, 'red' => 1]],
            15 => [0, ['blue' => 1, 'white' => 2, 'red' => 2]],
            21 => [0, ['white' => 2, 'green' => 3]],
            22 => [0, ['blue' => 1, 'green' => 2, 'red' => 2]],
            24 => [0, ['yellow' => 1, 'blue' => 2, 'white' => 2]],
            25 => [0, ['yellow' => 2, 'blue' => 3]],
            26 => [0, ['yellow' => 2, 'white' => 2, 'red' => 1]],
            28 => [0, ['yellow' => 2, 'blue' => 2, 'white' => 1]],
            29 => [0, ['yellow' => 1, 'blue' => 1, 'white' => 1, 'green' => 1, 'red' => 1]],
            30 => [0, ['yellow' => 2, 'blue' => 2, 'green' => 1]],
            31 => [0, ['yellow' => 1, 'blue' => 1, 'white' => 1, 'green' => 1, 'red' => 1]],
            32 => [0, ['blue' => 2, 'white' => 3]],
            35 => [0, ['yellow' => 1, 'white' => 1, 'green' => 2, 'red' => 1]],
            37 => [0, ['blue' => 2, 'green' => 1, 'red' => 2]],
            38 => [0, ['blue' => 2, 'red' => 3]],
            39 => [0, ['yellow' => 2, 'white' => 1, 'green' => 2]],
            42 => [0, ['green' => 3, 'red' => 2]],
            43 => [0, ['yellow' => 2, 'green' => 1, 'red' => 2]],
            // 1-star cards
            11 => [1, ['yellow' => 5]],
            16 => [1, ['blue' => 1, 'green' => 4]],
            17 => [1, ['yellow' => 1, 'green' => 4]],
            18 => [1, ['blue' => 4, 'white' => 1]],
            19 => [1, ['green' => 5]],
            20 => [1, ['white' => 5]],
            23 => [1, ['blue' => 5]],
            27 => [1, ['blue' => 4, 'red' => 1]],
            33 => [1, ['white' => 1, 'red' => 4]],
            34 => [1, ['red' => 5]],
            36 => [1, ['yellow' => 1, 'white' => 4]],
            40 => [1, ['white' => 4, 'green' => 1]],
            41 => [1, ['yellow' => 4, 'blue' => 1]],
            44 => [1, ['green' => 1, 'red' => 4]],
        ];

        Card::whereNotIn('number', array_keys($cards))->each(function (Card $card) {
            $card->tokens()->delete();
            $card->delete();
        });

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
