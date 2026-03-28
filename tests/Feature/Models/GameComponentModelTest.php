<?php

use App\Models\Card;
use App\Models\CardToken;
use App\Models\QuotationCard;
use App\Models\QuotationCardTrade;
use App\Models\QuotationCardTradeItem;
use App\Models\TokenColor;
use App\Models\TradeSide;
use Database\Seeders\TokenColorSeeder;
use Database\Seeders\TradeSideSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        TokenColorSeeder::class,
        TradeSideSeeder::class,
    ]);
});

it('QuotationCard has many QuotationCardTrade', function () {
    $card = QuotationCard::create(['number' => 1, 'name' => 'Test Card']);

    QuotationCardTrade::create(['quotation_card_id' => $card->id, 'sort_order' => 1]);
    QuotationCardTrade::create(['quotation_card_id' => $card->id, 'sort_order' => 2]);

    expect($card->trades)->toHaveCount(2);
});

it('QuotationCardTrade has leftItems and rightItems returning correct sides', function () {
    $card = QuotationCard::create(['number' => 1, 'name' => 'Test Card']);
    $trade = QuotationCardTrade::create(['quotation_card_id' => $card->id, 'sort_order' => 1]);

    $leftSide = TradeSide::where('slug', 'left')->first();
    $rightSide = TradeSide::where('slug', 'right')->first();
    $red = TokenColor::where('slug', 'red')->first();
    $blue = TokenColor::where('slug', 'blue')->first();
    $green = TokenColor::where('slug', 'green')->first();

    QuotationCardTradeItem::create([
        'quotation_card_trade_id' => $trade->id,
        'trade_side_id' => $leftSide->id,
        'token_color_id' => $red->id,
        'quantity' => 1,
    ]);

    QuotationCardTradeItem::create([
        'quotation_card_trade_id' => $trade->id,
        'trade_side_id' => $rightSide->id,
        'token_color_id' => $blue->id,
        'quantity' => 1,
    ]);

    QuotationCardTradeItem::create([
        'quotation_card_trade_id' => $trade->id,
        'trade_side_id' => $rightSide->id,
        'token_color_id' => $green->id,
        'quantity' => 1,
    ]);

    expect($trade->leftItems)->toHaveCount(1)
        ->and($trade->rightItems)->toHaveCount(2);
});

it('Card has many CardToken and each card totals exactly 5 tokens', function () {
    $card = Card::create(['number' => 1, 'star_count' => 0]);

    $colors = TokenColor::all();
    $quantities = [2, 1, 1, 1, 0];

    foreach ($colors as $i => $color) {
        if ($quantities[$i] > 0) {
            CardToken::create([
                'card_id' => $card->id,
                'token_color_id' => $color->id,
                'quantity' => $quantities[$i],
            ]);
        }
    }

    $totalTokens = $card->tokens->sum('quantity');
    expect($card->tokens)->toHaveCount(4)
        ->and($totalTokens)->toBe(5);
});

it('Card star_count accepts values 0, 1, 2', function () {
    $card0 = Card::create(['number' => 1, 'star_count' => 0]);
    $card1 = Card::create(['number' => 2, 'star_count' => 1]);
    $card2 = Card::create(['number' => 3, 'star_count' => 2]);

    expect($card0->star_count)->toBe(0)
        ->and($card1->star_count)->toBe(1)
        ->and($card2->star_count)->toBe(2);
});
