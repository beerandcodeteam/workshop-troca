<?php

use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchStatus;
use App\Models\QuotationCard;
use App\Models\TokenColor;
use App\Models\User;
use App\Services\MatchInitializationService;
use Database\Seeders\CardSeeder;
use Database\Seeders\DifficultyTierSeeder;
use Database\Seeders\MatchResultTypeSeeder;
use Database\Seeders\MatchStatusSeeder;
use Database\Seeders\ParticipantTypeSeeder;
use Database\Seeders\PlayerRankSeeder;
use Database\Seeders\QuotationCardSeeder;
use Database\Seeders\TokenColorSeeder;
use Database\Seeders\TradeSideSeeder;
use Database\Seeders\TurnActionTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        PlayerRankSeeder::class,
        TokenColorSeeder::class,
        TradeSideSeeder::class,
        DifficultyTierSeeder::class,
        MatchStatusSeeder::class,
        MatchResultTypeSeeder::class,
        ParticipantTypeSeeder::class,
        TurnActionTypeSeeder::class,
        QuotationCardSeeder::class,
        CardSeeder::class,
    ]);
});

function createMatchForUser(User $user): GameMatch
{
    $service = app(MatchInitializationService::class);
    $quotationCardIds = QuotationCard::orderBy('number')->limit(2)->pluck('id')->toArray();
    $tierId = DifficultyTier::first()->id;

    return $service->createMatch($user, $quotationCardIds, $tierId);
}

it('renders game board page for match owner', function () {
    $user = User::factory()->create();
    $match = createMatchForUser($user);

    $this->actingAs($user)
        ->get('/arena/match/'.$match->id)
        ->assertStatus(200);
});

it('returns 403 for non-owner', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $match = createMatchForUser($owner);

    $this->actingAs($otherUser)
        ->get('/arena/match/'.$match->id)
        ->assertStatus(403);
});

it('displays player token inventory with 5 colors', function () {
    $user = User::factory()->create();
    $match = createMatchForUser($user);

    $component = Livewire::actingAs($user)
        ->test('pages::arena.match-board', ['match' => $match]);

    $component->assertSee('Seu Inventário');

    $tokenColors = TokenColor::all();
    expect($tokenColors)->toHaveCount(5);

    $component
        ->assertSee('Vermelhos')
        ->assertSee('Verdes')
        ->assertSee('Brancos')
        ->assertSee('Amarelos')
        ->assertSee('Azuis');
});

it('displays 4 card compartments with face-up cards', function () {
    $user = User::factory()->create();
    $match = createMatchForUser($user);

    $component = Livewire::actingAs($user)
        ->test('pages::arena.match-board', ['match' => $match]);

    $component->assertSee('Compartimentos');

    expect($match->compartments)->toHaveCount(4);

    foreach ($match->compartments as $compartment) {
        $component->assertSee('Comp. '.$compartment->position);
        $faceUpCard = $compartment->faceUpCard();
        if ($faceUpCard) {
            $component->assertSee('Carta #'.$faceUpCard->card->number);
        }
    }
});

it('displays 2 active quotation cards', function () {
    $user = User::factory()->create();
    $match = createMatchForUser($user);

    $component = Livewire::actingAs($user)
        ->test('pages::arena.match-board', ['match' => $match]);

    $component->assertSee('Cotações Ativas (Mercado)');

    expect($match->quotationCards)->toHaveCount(2);

    foreach ($match->quotationCards as $quotationCard) {
        $component->assertSee($quotationCard->name);
    }
});

it('displays whose turn it is', function () {
    $user = User::factory()->create();
    $match = createMatchForUser($user);

    $component = Livewire::actingAs($user)
        ->test('pages::arena.match-board', ['match' => $match]);

    $currentParticipant = $match->currentParticipantType;

    $expectedLabel = match ($currentParticipant?->slug) {
        'player' => 'Sua Vez',
        'ai' => 'Vez da IA',
        default => 'Aguardando...',
    };

    $component->assertSee($expectedLabel);
});

it('displays match summary stats', function () {
    $user = User::factory()->create();
    $match = createMatchForUser($user);

    $component = Livewire::actingAs($user)
        ->test('pages::arena.match-board', ['match' => $match]);

    $component
        ->assertSee('Resumo da Partida')
        ->assertSee('Ações Totais')
        ->assertSee('Lançamentos')
        ->assertSee('Trocas Realizadas');
});

it('redirects completed match to results page', function () {
    $user = User::factory()->create();
    $match = createMatchForUser($user);

    $completedStatus = MatchStatus::completed()->first();
    $match->update(['match_status_id' => $completedStatus->id]);

    Livewire::actingAs($user)
        ->test('pages::arena.match-board', ['match' => $match])
        ->assertRedirect(route('arena.match.results', $match));
});
