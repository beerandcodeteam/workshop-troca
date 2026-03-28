<?php

use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchStatus;
use App\Models\MatchTokenInventory;
use App\Models\ParticipantType;
use App\Models\QuotationCard;
use App\Models\User;
use App\Services\AiOpponentService;
use App\Services\MatchInitializationService;
use App\Services\TurnService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->playerType = ParticipantType::where('slug', 'player')->first();
    $this->aiType = ParticipantType::where('slug', 'ai')->first();
});

function createMatchForTurn(): GameMatch
{
    $user = User::factory()->create();
    $quotationCardIds = QuotationCard::query()->limit(2)->pluck('id')->toArray();
    $tier = DifficultyTier::first();

    return (new MatchInitializationService)->createMatch($user, $quotationCardIds, $tier->id);
}

it('endTurn() switches participant from player to AI', function () {
    $match = createMatchForTurn();
    $match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => true,
    ]);

    $aiMock = Mockery::mock(AiOpponentService::class);
    $aiMock->shouldReceive('executeTurn')->once();
    app()->instance(AiOpponentService::class, $aiMock);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();
    expect($match->current_participant_type_id)->toBe($this->aiType->id);
});

it('endTurn() switches participant from AI to player', function () {
    $match = createMatchForTurn();
    $match->update([
        'current_participant_type_id' => $this->aiType->id,
        'has_acted_this_turn' => true,
    ]);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();
    expect($match->current_participant_type_id)->toBe($this->playerType->id);
});

it('endTurn() increments turn number', function () {
    $match = createMatchForTurn();
    $turnBefore = $match->current_turn_number;
    $match->update([
        'current_participant_type_id' => $this->aiType->id,
        'has_acted_this_turn' => true,
    ]);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();
    expect($match->current_turn_number)->toBe($turnBefore + 1);
});

it('endTurn() resets has_acted_this_turn to false', function () {
    $match = createMatchForTurn();
    $match->update([
        'current_participant_type_id' => $this->aiType->id,
        'has_acted_this_turn' => true,
    ]);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();
    expect($match->has_acted_this_turn)->toBeFalse();
});

it('endTurn() fails if participant has not acted', function () {
    $match = createMatchForTurn();
    $match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => false,
    ]);

    $service = app(TurnService::class);
    $service->endTurn($match);
})->throws(InvalidArgumentException::class, 'O participante deve realizar uma ação antes de encerrar o turno.');

it('endTurn() fails if participant is over token limit', function () {
    $match = createMatchForTurn();
    $match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => true,
    ]);

    MatchTokenInventory::where('match_id', $match->id)
        ->where('participant_type_id', $this->playerType->id)
        ->first()
        ->update(['quantity' => 11]);

    $service = app(TurnService::class);
    $service->endTurn($match);
})->throws(InvalidArgumentException::class, 'O participante está acima do limite de tokens. Devolva tokens antes de encerrar o turno.');

it('endTurn() triggers game end when 2 compartments are emptied', function () {
    $match = createMatchForTurn();
    $match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => true,
        'compartments_emptied' => 2,
    ]);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();
    $completedStatus = MatchStatus::completed()->first();
    expect($match->match_status_id)->toBe($completedStatus->id)
        ->and($match->completed_at)->not->toBeNull();
});

it('endTurn() triggers AI stub when turn passes to AI', function () {
    $match = createMatchForTurn();
    $match->update([
        'current_participant_type_id' => $this->playerType->id,
        'has_acted_this_turn' => true,
    ]);

    $aiMock = Mockery::mock(AiOpponentService::class);
    $aiMock->shouldReceive('executeTurn')->once()->with(Mockery::type(GameMatch::class));
    app()->instance(AiOpponentService::class, $aiMock);

    $service = app(TurnService::class);
    $service->endTurn($match);
});
