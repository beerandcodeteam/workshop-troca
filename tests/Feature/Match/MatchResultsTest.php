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

function createCompletedMatch(array $overrides = []): GameMatch
{
    $user = $overrides['user'] ?? User::factory()->create([
        'total_xp' => 200,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);

    $completedStatus = MatchStatus::completed()->firstOrFail();
    $tier = DifficultyTier::first();
    $resultType = $overrides['result_slug'] ?? 'player_win';
    $resultTypeModel = MatchResultType::where('slug', $resultType)->firstOrFail();

    $attributes = collect($overrides)->except(['user', 'result_slug'])->toArray();

    return GameMatch::factory()->create(array_merge([
        'user_id' => $user->id,
        'difficulty_tier_id' => $tier->id,
        'match_status_id' => $completedStatus->id,
        'match_result_type_id' => $resultTypeModel->id,
        'player_score' => 15,
        'ai_score' => 10,
        'player_cards_purchased' => 3,
        'ai_cards_purchased' => 2,
        'xp_earned' => 150,
        'started_at' => now()->subMinutes(30),
        'completed_at' => now(),
    ], $attributes));
}

it('renders results page for completed match', function () {
    $user = User::factory()->create([
        'total_xp' => 200,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $match = createCompletedMatch(['user' => $user]);

    $this->actingAs($user)
        ->get(route('arena.match.results', $match))
        ->assertOk()
        ->assertSee('Partida #'.$match->id);
});

it('shows correct winner for player win', function () {
    $user = User::factory()->create([
        'total_xp' => 200,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $match = createCompletedMatch(['user' => $user, 'result_slug' => 'player_win']);

    $this->actingAs($user)
        ->get(route('arena.match.results', $match))
        ->assertOk()
        ->assertSee('Vitória!');
});

it('shows player and AI scores', function () {
    $user = User::factory()->create([
        'total_xp' => 200,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $match = createCompletedMatch([
        'user' => $user,
        'player_score' => 20,
        'ai_score' => 8,
    ]);

    $response = $this->actingAs($user)
        ->get(route('arena.match.results', $match));

    $response->assertOk()
        ->assertSee('20')
        ->assertSee('8');
});

it('shows XP earned', function () {
    $user = User::factory()->create([
        'total_xp' => 200,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $match = createCompletedMatch(['user' => $user, 'xp_earned' => 250]);

    $this->actingAs($user)
        ->get(route('arena.match.results', $match))
        ->assertOk()
        ->assertSee('+250 XP');
});

it('returns 404 for in-progress match', function () {
    $user = User::factory()->create([
        'total_xp' => 0,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $inProgressStatus = MatchStatus::inProgress()->firstOrFail();
    $match = GameMatch::factory()->create([
        'user_id' => $user->id,
        'match_status_id' => $inProgressStatus->id,
        'difficulty_tier_id' => DifficultyTier::first()->id,
        'started_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('arena.match.results', $match))
        ->assertNotFound();
});

it('returns 403 for non-owner', function () {
    $owner = User::factory()->create([
        'total_xp' => 200,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $otherUser = User::factory()->create([
        'total_xp' => 0,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $match = createCompletedMatch(['user' => $owner]);

    $this->actingAs($otherUser)
        ->get(route('arena.match.results', $match))
        ->assertForbidden();
});

it('play again link navigates to match setup', function () {
    $user = User::factory()->create([
        'total_xp' => 200,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $match = createCompletedMatch(['user' => $user]);

    $this->actingAs($user)
        ->get(route('arena.match.results', $match))
        ->assertOk()
        ->assertSee('Jogar Novamente')
        ->assertSee(route('arena.match-setup'));
});

it('back to arena link navigates to dashboard', function () {
    $user = User::factory()->create([
        'total_xp' => 200,
        'player_rank_id' => PlayerRank::where('slug', 'bronze')->first()->id,
    ]);
    $match = createCompletedMatch(['user' => $user]);

    $this->actingAs($user)
        ->get(route('arena.match.results', $match))
        ->assertOk()
        ->assertSee('Voltar à Arena')
        ->assertSee(route('arena'));
});
