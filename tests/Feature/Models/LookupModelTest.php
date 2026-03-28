<?php

use App\Models\PlayerRank;
use App\Models\ScoringRule;
use Database\Seeders\PlayerRankSeeder;
use Database\Seeders\ScoringRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        PlayerRankSeeder::class,
        ScoringRuleSeeder::class,
    ]);
});

it('returns the correct rank for given XP thresholds via PlayerRank::findForXp', function (int $xp, string $expectedSlug) {
    $rank = PlayerRank::findForXp($xp);

    expect($rank)->not->toBeNull()
        ->and($rank->slug)->toBe($expectedSlug);
})->with([
    'zero XP' => [0, 'bronze'],
    'just below silver' => [499, 'bronze'],
    'exact silver threshold' => [500, 'silver'],
    'mid gold' => [2000, 'gold'],
    'exact platinum' => [3500, 'platinum'],
    'high diamond' => [10000, 'diamond'],
]);

it('returns correct points for all 12 scoring combinations via ScoringRule::calculatePoints', function (int $remainingTokens, int $starCount, int $expectedPoints) {
    expect(ScoringRule::calculatePoints($remainingTokens, $starCount))->toBe($expectedPoints);
})->with([
    '3+ tokens, 0 stars' => [5, 0, 1],
    '3+ tokens, 1 star' => [3, 1, 2],
    '3+ tokens, 2 stars' => [4, 2, 3],
    '2 tokens, 0 stars' => [2, 0, 2],
    '2 tokens, 1 star' => [2, 1, 3],
    '2 tokens, 2 stars' => [2, 2, 5],
    '1 token, 0 stars' => [1, 0, 3],
    '1 token, 1 star' => [1, 1, 5],
    '1 token, 2 stars' => [1, 2, 8],
    '0 tokens, 0 stars' => [0, 0, 5],
    '0 tokens, 1 star' => [0, 1, 8],
    '0 tokens, 2 stars' => [0, 2, 12],
]);
