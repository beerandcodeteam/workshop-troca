<?php

namespace Database\Seeders;

use App\Models\QuotationCard;
use App\Models\QuotationCardTrade;
use App\Models\QuotationCardTradeItem;
use App\Models\TokenColor;
use App\Models\TradeSide;
use Illuminate\Database\Seeder;

class QuotationCardSeeder extends Seeder
{
    public function run(): void
    {
        $colors = TokenColor::all()->keyBy('slug');
        $leftSide = TradeSide::where('slug', 'left')->first();
        $rightSide = TradeSide::where('slug', 'right')->first();

        // 10 quotation cards based on the physical game
        // Format: [left_tokens, right_tokens] where tokens are [color_slug => quantity]
        $cards = [
            // Card 1: Red-focused (from game rules example)
            1 => [
                'name' => 'Vermelho Universal',
                'trades' => [
                    [['red' => 1], ['blue' => 1, 'green' => 1, 'yellow' => 1, 'white' => 1]],
                    [['red' => 1], ['white' => 2]],
                    [['blue' => 1], ['green' => 1, 'yellow' => 1]],
                ],
            ],
            // Card 2: Green-focused
            2 => [
                'name' => 'Verde Expansivo',
                'trades' => [
                    [['green' => 1], ['red' => 1, 'blue' => 1, 'yellow' => 1, 'white' => 1]],
                    [['green' => 1], ['yellow' => 2]],
                    [['red' => 1], ['white' => 1, 'blue' => 1]],
                ],
            ],
            // Card 3: Blue-focused
            3 => [
                'name' => 'Azul Estratégico',
                'trades' => [
                    [['blue' => 1], ['red' => 1, 'green' => 1, 'yellow' => 1, 'white' => 1]],
                    [['blue' => 1], ['red' => 2]],
                    [['yellow' => 1], ['white' => 1, 'green' => 1]],
                ],
            ],
            // Card 4: Yellow-focused
            4 => [
                'name' => 'Amarelo Dinâmico',
                'trades' => [
                    [['yellow' => 1], ['red' => 1, 'blue' => 1, 'green' => 1, 'white' => 1]],
                    [['yellow' => 1], ['green' => 2]],
                    [['white' => 1], ['red' => 1, 'blue' => 1]],
                ],
            ],
            // Card 5: Blue-White-Yellow (from game rules example)
            5 => [
                'name' => 'Azul Branco Amarelo',
                'trades' => [
                    [['blue' => 1], ['white' => 1, 'yellow' => 1]],
                    [['red' => 1], ['green' => 1, 'yellow' => 1]],
                    [['white' => 1], ['green' => 2]],
                ],
            ],
            // Card 6: White-focused
            6 => [
                'name' => 'Branco Dominante',
                'trades' => [
                    [['white' => 1], ['red' => 1, 'blue' => 1, 'green' => 1, 'yellow' => 1]],
                    [['white' => 1], ['blue' => 2]],
                    [['green' => 1], ['red' => 1, 'yellow' => 1]],
                ],
            ],
            // Card 7: Mixed trades
            7 => [
                'name' => 'Troca Mista',
                'trades' => [
                    [['red' => 1], ['blue' => 1, 'yellow' => 1]],
                    [['green' => 1], ['white' => 1, 'blue' => 1]],
                    [['yellow' => 1], ['red' => 1, 'white' => 1]],
                ],
            ],
            // Card 8: Double trades
            8 => [
                'name' => 'Dupla Conversão',
                'trades' => [
                    [['red' => 2], ['blue' => 1, 'green' => 1, 'yellow' => 1, 'white' => 1]],
                    [['blue' => 1], ['green' => 1, 'white' => 1]],
                    [['yellow' => 1], ['green' => 1, 'red' => 1]],
                ],
            ],
            // Card 9: Cross trades
            9 => [
                'name' => 'Cruzamento de Cores',
                'trades' => [
                    [['red' => 1], ['green' => 1, 'white' => 1]],
                    [['blue' => 1], ['yellow' => 1, 'red' => 1]],
                    [['white' => 1], ['yellow' => 1, 'blue' => 1]],
                    [['green' => 1], ['red' => 1, 'blue' => 1]],
                ],
            ],
            // Card 10: Balanced trades
            10 => [
                'name' => 'Equilíbrio Total',
                'trades' => [
                    [['yellow' => 1], ['blue' => 1, 'white' => 1]],
                    [['red' => 1], ['green' => 1, 'blue' => 1]],
                    [['green' => 1], ['yellow' => 1, 'white' => 1]],
                    [['white' => 1], ['red' => 1, 'green' => 1]],
                ],
            ],
        ];

        foreach ($cards as $number => $cardData) {
            $card = QuotationCard::updateOrCreate(
                ['number' => $number],
                ['name' => $cardData['name']],
            );

            $card->trades()->delete();

            foreach ($cardData['trades'] as $sortOrder => $tradeData) {
                $trade = QuotationCardTrade::create([
                    'quotation_card_id' => $card->id,
                    'sort_order' => $sortOrder + 1,
                ]);

                [$leftTokens, $rightTokens] = $tradeData;

                foreach ($leftTokens as $colorSlug => $qty) {
                    QuotationCardTradeItem::create([
                        'quotation_card_trade_id' => $trade->id,
                        'trade_side_id' => $leftSide->id,
                        'token_color_id' => $colors[$colorSlug]->id,
                        'quantity' => $qty,
                    ]);
                }

                foreach ($rightTokens as $colorSlug => $qty) {
                    QuotationCardTradeItem::create([
                        'quotation_card_trade_id' => $trade->id,
                        'trade_side_id' => $rightSide->id,
                        'token_color_id' => $colors[$colorSlug]->id,
                        'quantity' => $qty,
                    ]);
                }
            }
        }
    }
}
