<?php

use Database\Seeders\DifficultyTierSeeder;
use Database\Seeders\MatchResultTypeSeeder;
use Database\Seeders\MatchStatusSeeder;
use Database\Seeders\ParticipantTypeSeeder;
use Database\Seeders\PlayerRankSeeder;
use Database\Seeders\ScoringRuleSeeder;
use Database\Seeders\TokenColorSeeder;
use Database\Seeders\TradeSideSeeder;
use Database\Seeders\TurnActionTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        TokenColorSeeder::class,
        DifficultyTierSeeder::class,
        MatchStatusSeeder::class,
        MatchResultTypeSeeder::class,
        TurnActionTypeSeeder::class,
        ParticipantTypeSeeder::class,
        TradeSideSeeder::class,
        PlayerRankSeeder::class,
        ScoringRuleSeeder::class,
    ]);
});

it('seeds all lookup tables with the correct number of rows', function () {
    expect(DB::table('token_colors')->count())->toBe(5)
        ->and(DB::table('difficulty_tiers')->count())->toBe(3)
        ->and(DB::table('match_statuses')->count())->toBe(4)
        ->and(DB::table('match_result_types')->count())->toBe(3)
        ->and(DB::table('turn_action_types')->count())->toBe(4)
        ->and(DB::table('participant_types')->count())->toBe(2)
        ->and(DB::table('trade_sides')->count())->toBe(2)
        ->and(DB::table('player_ranks')->count())->toBe(5)
        ->and(DB::table('scoring_rules')->count())->toBe(12);
});

it('seeds token_colors with exactly 5 entries with valid hex codes', function () {
    $colors = DB::table('token_colors')->get();

    expect($colors)->toHaveCount(5);

    foreach ($colors as $color) {
        expect($color->hex_code)->toMatch('/^#[0-9A-Fa-f]{6}$/');
    }
});

it('seeds difficulty_tiers with 3 entries ordered by sort_order', function () {
    $tiers = DB::table('difficulty_tiers')->orderBy('sort_order')->get();

    expect($tiers)->toHaveCount(3)
        ->and($tiers[0]->slug)->toBe('padrao-primario')
        ->and($tiers[1]->slug)->toBe('cadeia-cruzada')
        ->and($tiers[2]->slug)->toBe('mestre-do-caos')
        ->and($tiers[0]->sort_order)->toBeLessThan($tiers[1]->sort_order)
        ->and($tiers[1]->sort_order)->toBeLessThan($tiers[2]->sort_order);
});

it('seeds scoring_rules with 12 entries covering all bracket/star combinations', function () {
    $rules = DB::table('scoring_rules')->get();

    expect($rules)->toHaveCount(12);

    $brackets = [0, 1, 2, 3];
    $stars = [0, 1, 2];

    foreach ($brackets as $minTokens) {
        foreach ($stars as $starCount) {
            $match = $rules->first(fn ($r) => $r->min_remaining_tokens === $minTokens && $r->star_count === $starCount);
            expect($match)->not->toBeNull("Missing rule for min_remaining_tokens={$minTokens}, star_count={$starCount}");
        }
    }
});

it('seeds player_ranks with entries with increasing min_xp values', function () {
    $ranks = DB::table('player_ranks')->orderBy('sort_order')->get();

    expect($ranks)->toHaveCount(5);

    for ($i = 1; $i < $ranks->count(); $i++) {
        expect($ranks[$i]->min_xp)->toBeGreaterThan($ranks[$i - 1]->min_xp);
    }
});
