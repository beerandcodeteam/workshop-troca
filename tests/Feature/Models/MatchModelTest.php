<?php

use App\Models\Card;
use App\Models\DifficultyTier;
use App\Models\GameMatch;
use App\Models\MatchCompartment;
use App\Models\MatchCompartmentCard;
use App\Models\MatchStatus;
use App\Models\MatchTokenInventory;
use App\Models\MatchTurn;
use App\Models\ParticipantType;
use App\Models\TokenColor;
use App\Models\TurnActionType;
use App\Models\User;
use Database\Seeders\CardSeeder;
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

beforeEach(function () {
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
        CardSeeder::class,
    ]);
});

it('can create a GameMatch with required fields', function () {
    $match = GameMatch::factory()->create();
    $match->refresh();

    expect($match->exists)->toBeTrue()
        ->and($match->user_id)->not->toBeNull()
        ->and($match->difficulty_tier_id)->not->toBeNull()
        ->and($match->match_status_id)->not->toBeNull()
        ->and($match->player_score)->toBe(0)
        ->and($match->ai_score)->toBe(0);
});

it('has correct relationships', function () {
    $match = GameMatch::factory()->create();

    expect($match->user)->toBeInstanceOf(User::class)
        ->and($match->difficultyTier)->toBeInstanceOf(DifficultyTier::class)
        ->and($match->matchStatus)->toBeInstanceOf(MatchStatus::class)
        ->and($match->compartments)->toBeEmpty()
        ->and($match->turns)->toBeEmpty()
        ->and($match->tokenInventories)->toBeEmpty();
});

it('MatchCompartment faceUpCard returns the first unpurchased card by position', function () {
    $match = GameMatch::factory()->create();
    $compartment = MatchCompartment::create([
        'match_id' => $match->id,
        'position' => 1,
    ]);

    $card1 = Card::where('number', 1)->first();
    $card2 = Card::where('number', 2)->first();
    $card3 = Card::where('number', 3)->first();

    MatchCompartmentCard::create([
        'match_compartment_id' => $compartment->id,
        'card_id' => $card1->id,
        'position' => 1,
        'is_purchased' => true,
        'purchased_by_participant_type_id' => ParticipantType::where('slug', 'player')->first()->id,
    ]);

    MatchCompartmentCard::create([
        'match_compartment_id' => $compartment->id,
        'card_id' => $card2->id,
        'position' => 2,
        'is_purchased' => false,
    ]);

    MatchCompartmentCard::create([
        'match_compartment_id' => $compartment->id,
        'card_id' => $card3->id,
        'position' => 3,
        'is_purchased' => false,
    ]);

    $faceUp = $compartment->faceUpCard();
    expect($faceUp)->not->toBeNull()
        ->and($faceUp->position)->toBe(2)
        ->and($faceUp->card_id)->toBe($card2->id);
});

it('MatchTokenInventory has unique constraint on match, participant, color', function () {
    $match = GameMatch::factory()->create();
    $participantType = ParticipantType::where('slug', 'player')->first();
    $color = TokenColor::where('slug', 'red')->first();

    MatchTokenInventory::create([
        'match_id' => $match->id,
        'participant_type_id' => $participantType->id,
        'token_color_id' => $color->id,
        'quantity' => 3,
    ]);

    MatchTokenInventory::create([
        'match_id' => $match->id,
        'participant_type_id' => $participantType->id,
        'token_color_id' => $color->id,
        'quantity' => 5,
    ]);
})->throws(UniqueConstraintViolationException::class);

it('MatchTurn action_data is cast to array', function () {
    $match = GameMatch::factory()->create();
    $participantType = ParticipantType::where('slug', 'player')->first();
    $actionType = TurnActionType::where('slug', 'roll_dice')->first();

    $turn = MatchTurn::create([
        'match_id' => $match->id,
        'turn_number' => 1,
        'participant_type_id' => $participantType->id,
        'turn_action_type_id' => $actionType->id,
        'action_data' => ['dice_result' => 'blue', 'token_color_id' => 5],
    ]);

    $turn->refresh();
    expect($turn->action_data)->toBeArray()
        ->and($turn->action_data['dice_result'])->toBe('blue')
        ->and($turn->action_data['token_color_id'])->toBe(5);
});
