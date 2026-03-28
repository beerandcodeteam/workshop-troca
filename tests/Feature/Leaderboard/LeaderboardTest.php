<?php

use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchResultType;
use App\Models\MatchStatus;
use App\Models\PlayerRank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('renders leaderboard page for authenticated user', function () {
    $user = User::factory()->create([
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);

    $this->actingAs($user)
        ->get(route('leaderboard'))
        ->assertOk()
        ->assertSee('Leaderboard');
});

it('players are sorted by total_xp descending', function () {
    $rank = PlayerRank::where('slug', 'bronze')->first();

    $low = User::factory()->create(['total_xp' => 100, 'username' => 'lowxp_player', 'player_rank_id' => $rank->id]);
    $high = User::factory()->create(['total_xp' => 900, 'username' => 'highxp_player', 'player_rank_id' => $rank->id]);
    $mid = User::factory()->create(['total_xp' => 500, 'username' => 'midxp_player', 'player_rank_id' => $rank->id]);

    $response = $this->actingAs($low)
        ->get(route('leaderboard'));

    $response->assertOk()
        ->assertSeeInOrder(['highxp_player', 'midxp_player', 'lowxp_player']);
});

it('current player row is visually marked', function () {
    $rank = PlayerRank::where('slug', 'bronze')->first();
    $user = User::factory()->create(['total_xp' => 100, 'player_rank_id' => $rank->id]);

    $response = $this->actingAs($user)
        ->get(route('leaderboard'));

    $response->assertOk()
        ->assertSee('Você');
});

it('shows correct match counts and win counts', function () {
    $rank = PlayerRank::where('slug', 'bronze')->first();
    $user = User::factory()->create(['total_xp' => 500, 'player_rank_id' => $rank->id]);
    $tier = DifficultyTier::first();
    $completedStatus = MatchStatus::completed()->firstOrFail();
    $playerWin = MatchResultType::where('slug', 'player_win')->firstOrFail();
    $aiWin = MatchResultType::where('slug', 'ai_win')->firstOrFail();

    GameMatch::factory()->count(2)->create([
        'user_id' => $user->id,
        'difficulty_tier_id' => $tier->id,
        'match_status_id' => $completedStatus->id,
        'match_result_type_id' => $playerWin->id,
        'completed_at' => now(),
    ]);

    GameMatch::factory()->create([
        'user_id' => $user->id,
        'difficulty_tier_id' => $tier->id,
        'match_status_id' => $completedStatus->id,
        'match_result_type_id' => $aiWin->id,
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('leaderboard'));

    $response->assertOk();

    $html = $response->content();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $row = $xpath->query("//tr[@data-player-id='{$user->id}']")->item(0);
    expect($row)->not->toBeNull();

    $cells = $xpath->query('.//td', $row);
    $matchesPlayed = trim($cells->item(3)->textContent);
    $totalWins = trim($cells->item(4)->textContent);

    expect($matchesPlayed)->toBe('3');
    expect($totalWins)->toBe('2');
});

it('paginates correctly', function () {
    $rank = PlayerRank::where('slug', 'bronze')->first();
    $users = User::factory()->count(25)->create(['player_rank_id' => $rank->id]);
    $firstUser = $users->first();

    $response = $this->actingAs($firstUser)
        ->get(route('leaderboard'));

    $response->assertOk();

    $response2 = $this->actingAs($firstUser)
        ->get(route('leaderboard', ['page' => 2]));

    $response2->assertOk();
});

it('handles users with 0 matches', function () {
    $rank = PlayerRank::where('slug', 'bronze')->first();
    $user = User::factory()->create(['total_xp' => 0, 'player_rank_id' => $rank->id]);

    $response = $this->actingAs($user)
        ->get(route('leaderboard'));

    $response->assertOk();

    $html = $response->content();
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $row = $xpath->query("//tr[@data-player-id='{$user->id}']")->item(0);
    expect($row)->not->toBeNull();

    $cells = $xpath->query('.//td', $row);
    $matchesPlayed = trim($cells->item(3)->textContent);
    $totalWins = trim($cells->item(4)->textContent);

    expect($matchesPlayed)->toBe('0');
    expect($totalWins)->toBe('0');
});
