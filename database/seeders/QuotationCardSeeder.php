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

        // 10 quotation cards from the original board game SQL data
        // Format: [left_tokens, right_tokens] where tokens are [color_slug => quantity]
        // Mapping: amarelo=yellow, azul=blue, branco=white, verde=green, vermelho=red
        // _e = esquerda (left), _d = direita (right)
        $cards = [
            1 => [
                'name' => 'Cotação 1',
                'trades' => [
                    [['red' => 1], ['white' => 2]],
                    [['blue' => 1], ['yellow' => 1, 'white' => 1, 'green' => 1]],
                    [['yellow' => 1, 'blue' => 1], ['green' => 1, 'red' => 1]],
                    [['red' => 1], ['yellow' => 1, 'blue' => 1, 'white' => 1, 'green' => 1]],
                    [['white' => 1, 'green' => 1], ['yellow' => 2, 'blue' => 1, 'red' => 1]],
                ],
            ],
            2 => [
                'name' => 'Cotação 2',
                'trades' => [
                    [['green' => 1], ['blue' => 1, 'red' => 1]],
                    [['yellow' => 1], ['blue' => 1, 'white' => 1, 'red' => 1]],
                    [['yellow' => 1, 'white' => 1], ['green' => 1, 'red' => 1]],
                    [['green' => 1, 'red' => 1], ['yellow' => 1, 'blue' => 1, 'white' => 1]],
                    [['yellow' => 1, 'white' => 1, 'red' => 1], ['blue' => 1, 'green' => 2]],
                ],
            ],
            3 => [
                'name' => 'Cotação 3',
                'trades' => [
                    [['white' => 1], ['red' => 2]],
                    [['blue' => 1], ['yellow' => 2, 'green' => 1]],
                    [['green' => 2], ['white' => 2]],
                    [['red' => 1], ['yellow' => 2, 'blue' => 2]],
                    [['blue' => 2], ['white' => 1, 'green' => 1, 'red' => 2]],
                ],
            ],
            4 => [
                'name' => 'Cotação 4',
                'trades' => [
                    [['yellow' => 1], ['white' => 1, 'green' => 1]],
                    [['red' => 2], ['yellow' => 1, 'blue' => 1]],
                    [['yellow' => 1, 'green' => 1], ['blue' => 1, 'red' => 1]],
                    [['blue' => 1], ['yellow' => 1, 'white' => 2, 'green' => 1]],
                    [['green' => 2], ['yellow' => 1, 'blue' => 1, 'white' => 1, 'red' => 1]],
                ],
            ],
            5 => [
                'name' => 'Cotação 5',
                'trades' => [
                    [['blue' => 1], ['yellow' => 1, 'white' => 1]],
                    [['white' => 2], ['green' => 1, 'red' => 1]],
                    [['yellow' => 1, 'red' => 1], ['blue' => 2]],
                    [['white' => 1, 'green' => 1], ['blue' => 1]],
                    [['yellow' => 2, 'blue' => 1], ['white' => 1, 'green' => 2]],
                ],
            ],
            6 => [
                'name' => 'Cotação 6',
                'trades' => [
                    [['yellow' => 1], ['blue' => 1, 'red' => 1]],
                    [['blue' => 1], ['white' => 2, 'green' => 1]],
                    [['yellow' => 1, 'white' => 1], ['blue' => 1, 'green' => 1]],
                    [['yellow' => 2], ['blue' => 1, 'green' => 1, 'red' => 1]],
                    [['white' => 1, 'red' => 2], ['yellow' => 1, 'blue' => 1, 'green' => 1]],
                ],
            ],
            7 => [
                'name' => 'Cotação 7',
                'trades' => [
                    [['white' => 1], ['yellow' => 1, 'red' => 1]],
                    [['red' => 1], ['blue' => 1, 'white' => 1, 'green' => 1]],
                    [['blue' => 2], ['white' => 1, 'green' => 1]],
                    [[], ['white' => 1, 'green' => 2, 'red' => 1]],
                    [['blue' => 1, 'white' => 1], ['yellow' => 2, 'red' => 2]],
                ],
            ],
            8 => [
                'name' => 'Cotação 8',
                'trades' => [
                    [['blue' => 1], ['green' => 2]],
                    [['white' => 1], ['yellow' => 1, 'green' => 1, 'red' => 1]],
                    [['red' => 1], ['yellow' => 1, 'blue' => 1, 'white' => 1]],
                    [['green' => 1, 'red' => 1], ['yellow' => 1, 'blue' => 2]],
                    [['white' => 2, 'green' => 1], ['yellow' => 1, 'blue' => 1, 'red' => 1]],
                ],
            ],
            9 => [
                'name' => 'Cotação 9',
                'trades' => [
                    [['red' => 1], ['blue' => 1, 'green' => 1]],
                    [['blue' => 1], ['yellow' => 1, 'green' => 1, 'red' => 1]],
                    [['yellow' => 1], ['white' => 2, 'red' => 1]],
                    [['green' => 1], ['yellow' => 1, 'blue' => 1, 'white' => 1, 'red' => 1]],
                    [['white' => 1, 'red' => 1], ['yellow' => 2, 'blue' => 1, 'green' => 1]],
                ],
            ],
            10 => [
                'name' => 'Cotação 10',
                'trades' => [
                    [['green' => 1], ['yellow' => 1, 'white' => 1]],
                    [['green' => 1], ['blue' => 1, 'red' => 2]],
                    [['white' => 1], ['yellow' => 1, 'blue' => 1, 'red' => 1]],
                    [['blue' => 1, 'green' => 1], ['yellow' => 1, 'white' => 1, 'red' => 1]],
                    [['white' => 2], ['yellow' => 1, 'blue' => 1, 'green' => 2]],
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
