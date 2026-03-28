<?php

use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchCompartmentCard;
use App\Models\MatchStatus;
use App\Models\MatchTokenInventory;
use App\Models\ParticipantType;
use App\Models\QuotationCard;
use App\Models\User;
use App\Services\AiOpponentService;
use App\Services\CardPurchaseService;
use App\Services\MatchInitializationService;
use App\Services\ScoringService;
use App\Services\TurnService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->playerType = ParticipantType::where('slug', 'player')->first();
    $this->aiType = ParticipantType::where('slug', 'ai')->first();
    $this->purchaseService = new CardPurchaseService(new ScoringService);
});

function createMatchForGameEnd(): GameMatch
{
    $user = User::factory()->create();
    $quotationCardIds = QuotationCard::query()->limit(2)->pluck('id')->toArray();
    $tier = DifficultyTier::first();

    $match = (new MatchInitializationService)->createMatch($user, $quotationCardIds, $tier->id);
    $match->update([
        'current_participant_type_id' => ParticipantType::where('slug', 'player')->first()->id,
        'has_acted_this_turn' => true,
    ]);

    return $match;
}

function emptyCompartment(GameMatch $match, int $compartmentIndex, int $participantTypeId): void
{
    $compartment = $match->compartments()->orderBy('position')->skip($compartmentIndex)->first();
    $cards = $compartment->cards()->orderBy('position')->get();

    foreach ($cards as $card) {
        $card->update([
            'is_purchased' => true,
            'purchased_by_participant_type_id' => $participantTypeId,
            'purchased_at' => now(),
            'points_scored' => 1,
        ]);
    }

    $compartment->update(['is_star_bonus_active' => true]);
}

function giveTokensForGameEndCard(GameMatch $match, int $participantTypeId, MatchCompartmentCard $card): void
{
    foreach ($card->card->tokens as $cardToken) {
        MatchTokenInventory::where('match_id', $match->id)
            ->where('participant_type_id', $participantTypeId)
            ->where('token_color_id', $cardToken->token_color_id)
            ->update(['quantity' => $cardToken->quantity]);
    }
}

it('game does NOT end when only 1 compartment is emptied', function () {
    $match = createMatchForGameEnd();
    $match->update(['compartments_emptied' => 1]);

    $aiMock = Mockery::mock(AiOpponentService::class);
    $aiMock->shouldReceive('executeTurn')->once();
    app()->instance(AiOpponentService::class, $aiMock);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();
    $completedStatus = MatchStatus::completed()->first();
    expect($match->match_status_id)->not->toBe($completedStatus->id);
});

it('game ends when 2nd compartment becomes empty via endTurn', function () {
    $match = createMatchForGameEnd();
    $match->update(['compartments_emptied' => 2]);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();
    $completedStatus = MatchStatus::completed()->first();
    expect($match->match_status_id)->toBe($completedStatus->id)
        ->and($match->completed_at)->not->toBeNull()
        ->and($match->match_result_type_id)->not->toBeNull();
});

it('no further turns can be taken after game ends', function () {
    $match = createMatchForGameEnd();
    $match->update(['compartments_emptied' => 2]);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();

    $match->update(['has_acted_this_turn' => true]);

    $service->endTurn($match);

    $match->refresh();
    $completedStatus = MatchStatus::completed()->first();
    expect($match->match_status_id)->toBe($completedStatus->id);
});

it('match status changes to completed on game end', function () {
    $match = createMatchForGameEnd();
    $match->update(['compartments_emptied' => 2]);

    $inProgressStatus = MatchStatus::inProgress()->first();
    expect($match->match_status_id)->toBe($inProgressStatus->id);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();
    $completedStatus = MatchStatus::completed()->first();
    expect($match->match_status_id)->toBe($completedStatus->id);
});

it('game end is triggered regardless of whose turn it is', function () {
    $match = createMatchForGameEnd();
    $match->update([
        'current_participant_type_id' => $this->aiType->id,
        'has_acted_this_turn' => true,
        'compartments_emptied' => 2,
    ]);

    $service = app(TurnService::class);
    $service->endTurn($match);

    $match->refresh();
    $completedStatus = MatchStatus::completed()->first();
    expect($match->match_status_id)->toBe($completedStatus->id);
});
