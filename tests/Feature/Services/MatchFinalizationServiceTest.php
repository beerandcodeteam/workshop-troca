<?php

use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchResultType;
use App\Models\MatchStatus;
use App\Models\PlayerRank;
use App\Models\User;
use App\Services\MatchFinalizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->service = new MatchFinalizationService;
});

function createMatchForFinalization(array $overrides = []): GameMatch
{
    $user = $overrides['user'] ?? User::factory()->create([
        'total_xp' => 0,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);

    $inProgressStatus = MatchStatus::inProgress()->firstOrFail();
    $tier = $overrides['tier'] ?? DifficultyTier::first();

    $attributes = collect($overrides)->except(['user', 'tier'])->toArray();

    return GameMatch::factory()->create(array_merge([
        'user_id' => $user->id,
        'difficulty_tier_id' => $tier->id,
        'match_status_id' => $inProgressStatus->id,
        'player_score' => 0,
        'ai_score' => 0,
        'player_cards_purchased' => 0,
        'ai_cards_purchased' => 0,
        'xp_earned' => 0,
        'started_at' => now(),
    ], $attributes));
}

it('player wins when player_score > ai_score', function () {
    $match = createMatchForFinalization([
        'player_score' => 15,
        'ai_score' => 10,
    ]);

    $this->service->finalizeMatch($match);

    $match->refresh();
    $resultType = MatchResultType::where('slug', 'player_win')->first();
    expect($match->match_result_type_id)->toBe($resultType->id);
});

it('AI wins when ai_score > player_score', function () {
    $match = createMatchForFinalization([
        'player_score' => 5,
        'ai_score' => 12,
    ]);

    $this->service->finalizeMatch($match);

    $match->refresh();
    $resultType = MatchResultType::where('slug', 'ai_win')->first();
    expect($match->match_result_type_id)->toBe($resultType->id);
});

it('tiebreaker: fewer cards purchased wins on score tie', function () {
    $match = createMatchForFinalization([
        'player_score' => 10,
        'ai_score' => 10,
        'player_cards_purchased' => 2,
        'ai_cards_purchased' => 4,
    ]);

    $this->service->finalizeMatch($match);

    $match->refresh();
    $resultType = MatchResultType::where('slug', 'player_win')->first();
    expect($match->match_result_type_id)->toBe($resultType->id);
});

it('draw when both scores and card counts are equal', function () {
    $match = createMatchForFinalization([
        'player_score' => 10,
        'ai_score' => 10,
        'player_cards_purchased' => 3,
        'ai_cards_purchased' => 3,
    ]);

    $this->service->finalizeMatch($match);

    $match->refresh();
    $resultType = MatchResultType::where('slug', 'draw')->first();
    expect($match->match_result_type_id)->toBe($resultType->id);
});

it('XP is calculated correctly: base + win bonus', function () {
    $tier = DifficultyTier::first();
    $match = createMatchForFinalization([
        'player_score' => 15,
        'ai_score' => 5,
        'tier' => $tier,
    ]);

    $this->service->finalizeMatch($match);

    $match->refresh();
    $expectedXp = $tier->base_xp_reward + $tier->win_bonus_xp;
    expect($match->xp_earned)->toBe($expectedXp);
});

it('XP is added to user total_xp', function () {
    $user = User::factory()->create([
        'total_xp' => 100,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $tier = DifficultyTier::first();
    $match = createMatchForFinalization([
        'user' => $user,
        'player_score' => 15,
        'ai_score' => 5,
        'tier' => $tier,
    ]);

    $this->service->finalizeMatch($match);

    $user->refresh();
    $expectedXp = 100 + $tier->base_xp_reward + $tier->win_bonus_xp;
    expect($user->total_xp)->toBe($expectedXp);
});

it('user player_rank_id is updated when crossing a rank threshold', function () {
    $bronzeRank = PlayerRank::where('slug', 'bronze')->first();
    $silverRank = PlayerRank::where('slug', 'silver')->first();

    $user = User::factory()->create([
        'total_xp' => 450,
        'player_rank_id' => $bronzeRank->id,
    ]);

    $tier = DifficultyTier::first();
    $match = createMatchForFinalization([
        'user' => $user,
        'player_score' => 15,
        'ai_score' => 5,
        'tier' => $tier,
    ]);

    $this->service->finalizeMatch($match);

    $user->refresh();
    expect($user->total_xp)->toBeGreaterThanOrEqual(500);
    expect($user->player_rank_id)->toBe($silverRank->id);
});

it('match status is set to completed', function () {
    $match = createMatchForFinalization([
        'player_score' => 10,
        'ai_score' => 5,
    ]);

    $this->service->finalizeMatch($match);

    $match->refresh();
    $completedStatus = MatchStatus::completed()->first();
    expect($match->match_status_id)->toBe($completedStatus->id);
});

it('completed_at timestamp is set', function () {
    $match = createMatchForFinalization([
        'player_score' => 10,
        'ai_score' => 5,
    ]);

    $this->service->finalizeMatch($match);

    $match->refresh();
    expect($match->completed_at)->not->toBeNull();
});

it('losing player still earns base XP with no win bonus', function () {
    $tier = DifficultyTier::first();
    $match = createMatchForFinalization([
        'player_score' => 3,
        'ai_score' => 15,
        'tier' => $tier,
    ]);

    $this->service->finalizeMatch($match);

    $match->refresh();
    expect($match->xp_earned)->toBe($tier->base_xp_reward);
});
