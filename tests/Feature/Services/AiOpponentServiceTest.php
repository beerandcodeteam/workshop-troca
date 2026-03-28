<?php

use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchTokenInventory;
use App\Models\MatchTurn;
use App\Models\ParticipantType;
use App\Models\QuotationCard;
use App\Models\TurnActionType;
use App\Models\User;
use App\Services\AiOpponentService;
use App\Services\DiceService;
use App\Services\MatchInitializationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->aiType = ParticipantType::where('slug', 'ai')->first();
    $this->playerType = ParticipantType::where('slug', 'player')->first();
});

function createMatchForAi(): GameMatch
{
    $user = User::factory()->create();
    $quotationCardIds = QuotationCard::query()->limit(2)->pluck('id')->toArray();
    $tier = DifficultyTier::first();

    $match = (new MatchInitializationService)->createMatch($user, $quotationCardIds, $tier->id);

    $aiType = ParticipantType::where('slug', 'ai')->first();
    $match->update([
        'current_participant_type_id' => $aiType->id,
        'has_acted_this_turn' => false,
    ]);

    return $match;
}

it('AI stub executes a dice roll on its turn', function () {
    $match = createMatchForAi();

    $diceMock = Mockery::mock(DiceService::class);
    $diceMock->shouldReceive('roll')->once()->andReturn('blue');
    $diceMock->shouldReceive('applyRoll')->once();
    app()->instance(DiceService::class, $diceMock);

    $service = app(AiOpponentService::class);
    $service->executeTurn($match);
});

it('AI stub creates a turn record with correct participant type', function () {
    $match = createMatchForAi();
    $turnsBefore = MatchTurn::where('match_id', $match->id)->count();

    $service = app(AiOpponentService::class);
    $service->executeTurn($match);

    $rollDiceAction = TurnActionType::where('slug', 'roll_dice')->first();
    $aiTurn = MatchTurn::where('match_id', $match->id)
        ->where('participant_type_id', $this->aiType->id)
        ->where('turn_action_type_id', $rollDiceAction->id)
        ->first();

    expect($aiTurn)->not->toBeNull()
        ->and($aiTurn->participant_type_id)->toBe($this->aiType->id);
});

it('AI stub adds a token to AI inventory', function () {
    $match = createMatchForAi();

    $totalBefore = MatchTokenInventory::where('match_id', $match->id)
        ->where('participant_type_id', $this->aiType->id)
        ->sum('quantity');

    $service = app(AiOpponentService::class);
    $service->executeTurn($match);

    $totalAfter = MatchTokenInventory::where('match_id', $match->id)
        ->where('participant_type_id', $this->aiType->id)
        ->sum('quantity');

    expect($totalAfter)->toBeGreaterThanOrEqual($totalBefore + 1);
});

it('AI stub purchases a card when it has the required tokens', function () {
    $match = createMatchForAi();

    $compartment = $match->compartments()->first();
    $faceUpCard = $compartment->faceUpCard();
    $cardTokens = $faceUpCard->card->tokens;

    foreach ($cardTokens as $cardToken) {
        MatchTokenInventory::where('match_id', $match->id)
            ->where('participant_type_id', $this->aiType->id)
            ->where('token_color_id', $cardToken->token_color_id)
            ->update(['quantity' => $cardToken->quantity + 5]);
    }

    $service = app(AiOpponentService::class);
    $service->executeTurn($match);

    $faceUpCard->refresh();
    expect($faceUpCard->is_purchased)->toBeTrue()
        ->and($faceUpCard->purchased_by_participant_type_id)->toBe($this->aiType->id);
});

it('AI stub respects the 10-token limit', function () {
    $match = createMatchForAi();

    MatchTokenInventory::where('match_id', $match->id)
        ->where('participant_type_id', $this->aiType->id)
        ->update(['quantity' => 2]);

    $service = app(AiOpponentService::class);
    $service->executeTurn($match);

    $totalAfter = MatchTokenInventory::where('match_id', $match->id)
        ->where('participant_type_id', $this->aiType->id)
        ->sum('quantity');

    expect($totalAfter)->toBeLessThanOrEqual(10);
});

it('AI stub returns turn to player after completing its action', function () {
    $match = createMatchForAi();

    $service = app(AiOpponentService::class);
    $service->executeTurn($match);

    $match->refresh();
    expect($match->current_participant_type_id)->toBe($this->playerType->id)
        ->and($match->has_acted_this_turn)->toBeFalse();
});

it('AI stub receives difficulty tier parameter', function () {
    $match = createMatchForAi();
    $tier = DifficultyTier::first();

    $service = app(AiOpponentService::class);
    $service->executeTurn($match, $tier);

    $match->refresh();
    expect($match->current_participant_type_id)->toBe($this->playerType->id);
});

it('AI stub handles free dice result by picking a random color', function () {
    $match = createMatchForAi();

    $diceMock = Mockery::mock(DiceService::class);
    $diceMock->shouldReceive('roll')->once()->andReturn('free');
    $diceMock->shouldReceive('applyRoll')->once()->withArgs(function ($m, $ptId, $color) {
        return in_array($color, ['red', 'green', 'white', 'yellow', 'blue']);
    });
    app()->instance(DiceService::class, $diceMock);

    $service = app(AiOpponentService::class);
    $service->executeTurn($match);
});
