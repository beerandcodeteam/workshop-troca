<?php

use App\Models\Card;
use App\Models\QuotationCard;
use App\Models\QuotationCardTradeItem;
use App\Models\TokenColor;
use App\Models\TradeSide;
use Database\Seeders\CardSeeder;
use Database\Seeders\QuotationCardSeeder;
use Database\Seeders\TokenColorSeeder;
use Database\Seeders\TradeSideSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        TokenColorSeeder::class,
        TradeSideSeeder::class,
        QuotationCardSeeder::class,
        CardSeeder::class,
    ]);
});

it('seeds exactly 10 quotation cards', function () {
    expect(QuotationCard::count())->toBe(10);
});

it('ensures each quotation card has at least 2 trade rows', function () {
    QuotationCard::all()->each(function ($card) {
        expect($card->trades->count())->toBeGreaterThanOrEqual(2, "Card #{$card->number} has fewer than 2 trades");
    });
});

it('ensures all trade items reference valid token colors and trade sides', function () {
    $validColorIds = TokenColor::pluck('id')->toArray();
    $validSideIds = TradeSide::pluck('id')->toArray();

    QuotationCardTradeItem::all()->each(function ($item) use ($validColorIds, $validSideIds) {
        expect($validColorIds)->toContain($item->token_color_id)
            ->and($validSideIds)->toContain($item->trade_side_id);
    });
});

it('seeds exactly 45 cards', function () {
    expect(Card::count())->toBe(45);
});

it('ensures each card has exactly 5 total tokens', function () {
    Card::with('tokens')->get()->each(function ($card) {
        $total = $card->tokens->sum('quantity');
        expect($total)->toBe(5, "Card #{$card->number} has {$total} total tokens instead of 5");
    });
});

it('ensures cards have appropriate star_count distribution', function () {
    $distribution = Card::selectRaw('star_count, count(*) as count')
        ->groupBy('star_count')
        ->pluck('count', 'star_count');

    expect($distribution->has(0))->toBeTrue()
        ->and($distribution->has(1))->toBeTrue()
        ->and($distribution->has(2))->toBeTrue()
        ->and($distribution[0])->toBeGreaterThan($distribution[1])
        ->and($distribution[1])->toBeGreaterThan($distribution[2]);
});
