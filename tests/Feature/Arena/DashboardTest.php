<?php

use App\Models\GameMatch;
use App\Models\MatchStatus;
use App\Models\PlayerRank;
use App\Models\User;
use Database\Seeders\DifficultyTierSeeder;
use Database\Seeders\MatchStatusSeeder;
use Database\Seeders\ParticipantTypeSeeder;
use Database\Seeders\PlayerRankSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        PlayerRankSeeder::class,
        MatchStatusSeeder::class,
        DifficultyTierSeeder::class,
        ParticipantTypeSeeder::class,
    ]);
});

it('renders the arena dashboard for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/arena')
        ->assertStatus(200);
});

it('displays the player username', function () {
    $user = User::factory()->create(['username' => 'arena_player']);

    Livewire::actingAs($user)
        ->test('pages::arena.index')
        ->assertSee('arena_player');
});

it('displays the player rank based on XP', function () {
    $bronzeRank = PlayerRank::where('slug', 'bronze')->first();

    $user = User::factory()->create([
        'total_xp' => 0,
        'player_rank_id' => $bronzeRank->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::arena.index')
        ->assertSee('Bronze');
});

it('displays rank based on XP threshold', function () {
    $silverRank = PlayerRank::where('slug', 'silver')->first();

    $user = User::factory()->create([
        'total_xp' => 600,
        'player_rank_id' => $silverRank->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::arena.index')
        ->assertSee('Silver');
});

it('shows the new match button', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::arena.index')
        ->assertSee('Nova Partida');
});

it('shows resume match button when an in-progress match exists', function () {
    $user = User::factory()->create();
    $inProgressStatus = MatchStatus::inProgress()->first();

    GameMatch::factory()->create([
        'user_id' => $user->id,
        'match_status_id' => $inProgressStatus->id,
    ]);

    Livewire::actingAs($user)
        ->test('pages::arena.index')
        ->assertSee('Retomar Partida');
});

it('does not show resume match button when no active match exists', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::arena.index')
        ->assertDontSee('Retomar Partida');
});

it('redirects unauthenticated user to login', function () {
    $this->get('/arena')
        ->assertRedirect('/login');
});
