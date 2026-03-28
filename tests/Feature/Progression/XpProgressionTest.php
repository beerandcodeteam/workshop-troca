<?php

use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchStatus;
use App\Models\PlayerRank;
use App\Models\User;
use App\Services\MatchFinalizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->service = new MatchFinalizationService;
    $this->bronzeRank = PlayerRank::where('slug', 'bronze')->first();
});

function createProgressionMatch(User $user, array $overrides = []): GameMatch
{
    $inProgressStatus = MatchStatus::inProgress()->firstOrFail();
    $tier = DifficultyTier::first();

    return GameMatch::factory()->create(array_merge([
        'user_id' => $user->id,
        'difficulty_tier_id' => $tier->id,
        'match_status_id' => $inProgressStatus->id,
        'player_score' => 15,
        'ai_score' => 5,
        'player_cards_purchased' => 3,
        'ai_cards_purchased' => 2,
        'xp_earned' => 0,
        'started_at' => now(),
    ], $overrides));
}

it('user starts at Bronze rank with 0 XP', function () {
    $user = User::factory()->create([
        'total_xp' => 0,
        'player_rank_id' => $this->bronzeRank->id,
    ]);

    expect($user->total_xp)->toBe(0);
    expect($user->player_rank_id)->toBe($this->bronzeRank->id);
});

it('user rank updates to Silver at 500 XP', function () {
    $silverRank = PlayerRank::where('slug', 'silver')->first();

    $user = User::factory()->create([
        'total_xp' => 450,
        'player_rank_id' => $this->bronzeRank->id,
    ]);

    $match = createProgressionMatch($user);
    $this->service->finalizeMatch($match);

    $user->refresh();
    expect($user->total_xp)->toBeGreaterThanOrEqual(500);
    expect($user->player_rank_id)->toBe($silverRank->id);
});

it('user rank updates to Gold at 1500 XP', function () {
    $goldRank = PlayerRank::where('slug', 'gold')->first();
    $silverRank = PlayerRank::where('slug', 'silver')->first();

    $user = User::factory()->create([
        'total_xp' => 1450,
        'player_rank_id' => $silverRank->id,
    ]);

    $match = createProgressionMatch($user);
    $this->service->finalizeMatch($match);

    $user->refresh();
    expect($user->total_xp)->toBeGreaterThanOrEqual(1500);
    expect($user->player_rank_id)->toBe($goldRank->id);
});

it('user rank updates to Platinum at 3500 XP', function () {
    $platinumRank = PlayerRank::where('slug', 'platinum')->first();
    $goldRank = PlayerRank::where('slug', 'gold')->first();

    $user = User::factory()->create([
        'total_xp' => 3450,
        'player_rank_id' => $goldRank->id,
    ]);

    $match = createProgressionMatch($user);
    $this->service->finalizeMatch($match);

    $user->refresh();
    expect($user->total_xp)->toBeGreaterThanOrEqual(3500);
    expect($user->player_rank_id)->toBe($platinumRank->id);
});

it('user rank updates to Diamond at 7000 XP', function () {
    $diamondRank = PlayerRank::where('slug', 'diamond')->first();
    $platinumRank = PlayerRank::where('slug', 'platinum')->first();

    $user = User::factory()->create([
        'total_xp' => 6950,
        'player_rank_id' => $platinumRank->id,
    ]);

    $match = createProgressionMatch($user);
    $this->service->finalizeMatch($match);

    $user->refresh();
    expect($user->total_xp)->toBeGreaterThanOrEqual(7000);
    expect($user->player_rank_id)->toBe($diamondRank->id);
});

it('rank does not downgrade since XP is cumulative', function () {
    $silverRank = PlayerRank::where('slug', 'silver')->first();

    $user = User::factory()->create([
        'total_xp' => 600,
        'player_rank_id' => $silverRank->id,
    ]);

    $match = createProgressionMatch($user, [
        'player_score' => 3,
        'ai_score' => 15,
    ]);
    $this->service->finalizeMatch($match);

    $user->refresh();
    expect($user->player_rank_id)->toBe($silverRank->id);
    expect($user->total_xp)->toBeGreaterThanOrEqual(600);
});

it('XP from multiple matches accumulates correctly', function () {
    $user = User::factory()->create([
        'total_xp' => 0,
        'player_rank_id' => $this->bronzeRank->id,
    ]);

    $tier = DifficultyTier::first();

    $match1 = createProgressionMatch($user);
    $this->service->finalizeMatch($match1);
    $user->refresh();
    $xpAfterFirst = $user->total_xp;
    expect($xpAfterFirst)->toBe($tier->base_xp_reward + $tier->win_bonus_xp);

    $match2 = createProgressionMatch($user);
    $this->service->finalizeMatch($match2);
    $user->refresh();
    $xpAfterSecond = $user->total_xp;
    expect($xpAfterSecond)->toBe($xpAfterFirst + $tier->base_xp_reward + $tier->win_bonus_xp);

    $match3 = createProgressionMatch($user, [
        'player_score' => 2,
        'ai_score' => 10,
    ]);
    $this->service->finalizeMatch($match3);
    $user->refresh();
    expect($user->total_xp)->toBe($xpAfterSecond + $tier->base_xp_reward);
});
