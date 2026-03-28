<?php

use App\Models\DifficultyTier;
use App\Models\MatchTokenInventory;
use App\Models\ParticipantType;
use App\Models\QuotationCard;
use App\Models\User;
use App\Services\AiOpponentService;
use App\Services\MatchInitializationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();

    $this->user = User::factory()->create();
    $quotationCardIds = QuotationCard::query()->limit(2)->pluck('id')->toArray();
    $tier = DifficultyTier::first();
    $this->match = (new MatchInitializationService)->createMatch($this->user, $quotationCardIds, $tier->id);

    $this->playerType = ParticipantType::where('slug', 'player')->first();
    $this->aiType = ParticipantType::where('slug', 'ai')->first();
});

it('player can end turn after acting via Livewire', function () {
    $this->match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => true,
    ]);

    $aiMock = Mockery::mock(AiOpponentService::class);
    $aiMock->shouldReceive('executeTurn')->once();
    app()->instance(AiOpponentService::class, $aiMock);

    Livewire::actingAs($this->user)
        ->test('pages::arena.match-board', ['match' => $this->match])
        ->call('endTurn')
        ->assertHasNoErrors();

    $this->match->refresh();
    expect($this->match->current_participant_type_id)->toBe($this->aiType->id);
});

it('End Turn button is hidden before player acts', function () {
    $this->match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => false,
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::arena.match-board', ['match' => $this->match])
        ->assertDontSee('Encerrar Turno');
});

it('End Turn button is hidden when player is over token limit', function () {
    $this->match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => true,
    ]);

    MatchTokenInventory::where('match_id', $this->match->id)
        ->where('participant_type_id', $this->playerType->id)
        ->first()
        ->update(['quantity' => 11]);

    Livewire::actingAs($this->user)
        ->test('pages::arena.match-board', ['match' => $this->match])
        ->assertDontSee('Encerrar Turno');
});

it('ending turn transitions to AI turn and back', function () {
    $this->match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => true,
    ]);

    $service = app(AiOpponentService::class);
    app()->instance(AiOpponentService::class, $service);

    Livewire::actingAs($this->user)
        ->test('pages::arena.match-board', ['match' => $this->match])
        ->call('endTurn');

    $this->match->refresh();
    expect($this->match->current_participant_type_id)->toBe($this->playerType->id)
        ->and($this->match->has_acted_this_turn)->toBeFalse();
});

it('ending turn redirects to results when game end condition is met', function () {
    $this->match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => true,
        'compartments_emptied' => 2,
    ]);

    Livewire::actingAs($this->user)
        ->test('pages::arena.match-board', ['match' => $this->match])
        ->call('endTurn')
        ->assertRedirect(route('arena.match.results', $this->match));
});
