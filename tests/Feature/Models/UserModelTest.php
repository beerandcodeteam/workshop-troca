<?php

use App\Models\GameMatch;
use App\Models\PlayerRank;
use App\Models\User;
use Database\Seeders\DifficultyTierSeeder;
use Database\Seeders\MatchResultTypeSeeder;
use Database\Seeders\MatchStatusSeeder;
use Database\Seeders\ParticipantTypeSeeder;
use Database\Seeders\PlayerRankSeeder;
use Database\Seeders\ScoringRuleSeeder;
use Database\Seeders\TokenColorSeeder;
use Database\Seeders\TradeSideSeeder;
use Database\Seeders\TurnActionTypeSeeder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a user with username, email, password', function () {
    $user = User::factory()->create([
        'username' => 'jogador1',
        'email' => 'jogador1@test.com',
    ]);

    expect($user->username)->toBe('jogador1')
        ->and($user->email)->toBe('jogador1@test.com')
        ->and($user->exists)->toBeTrue();
});

it('enforces username uniqueness constraint', function () {
    User::factory()->create(['username' => 'uniqueuser']);

    User::factory()->create(['username' => 'uniqueuser']);
})->throws(UniqueConstraintViolationException::class);

it('validates username length between 3 and 20 chars at database level', function () {
    $user = User::factory()->create(['username' => 'abc']);
    expect($user->exists)->toBeTrue();

    $longUser = User::factory()->create(['username' => str_repeat('a', 20)]);
    expect($longUser->exists)->toBeTrue();
});

it('returns correct playerRank relationship', function () {
    $this->seed(PlayerRankSeeder::class);

    $rank = PlayerRank::where('slug', 'gold')->first();
    $user = User::factory()->create(['player_rank_id' => $rank->id]);

    expect($user->playerRank->slug)->toBe('gold')
        ->and($user->playerRank)->toBeInstanceOf(PlayerRank::class);
});

it('returns associated matches relationship', function () {
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

    $user = User::factory()->create();

    expect($user->matches)->toHaveCount(0);

    GameMatch::factory()->create(['user_id' => $user->id]);
    $user->refresh();

    expect($user->matches)->toHaveCount(1);
});
