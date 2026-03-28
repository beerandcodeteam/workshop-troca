## Phase 8: Turn System & AI Stub

> Implements turn management, end turn flow, and the AI opponent stub.
> Reference: US-4.7, US-4.8, US-5.2

### Phase 8.1: Turn Management Service (US-4.7)

- [ ] Create `App\Services\TurnService`
- [ ] Method `endTurn(GameMatch $match): void`
    - Validates current participant has acted this turn
    - Validates current participant is not over the token limit
    - Checks game end condition (2 compartments emptied) — if met, finalize match (delegate to `MatchFinalizationService`)
    - If game continues:
        - Toggle `current_participant_type_id` (player ↔ AI)
        - Increment `current_turn_number`
        - Reset `has_acted_this_turn` to false
        - If new turn belongs to AI, trigger AI stub
- [ ] Method `getCurrentTurnState(GameMatch $match): array` — returns whose turn, has acted, can end turn, is over limit

**Tests (`tests/Feature/Services/TurnServiceTest.php`):**
- [ ] Test `endTurn()` switches participant from player to AI
- [ ] Test `endTurn()` switches participant from AI to player
- [ ] Test `endTurn()` increments turn number
- [ ] Test `endTurn()` resets `has_acted_this_turn` to false
- [ ] Test `endTurn()` fails if participant hasn't acted
- [ ] Test `endTurn()` fails if participant is over token limit
- [ ] Test `endTurn()` triggers game end when 2 compartments are emptied
- [ ] Test `endTurn()` triggers AI stub when turn passes to AI

### Phase 8.2: End Turn Action (Livewire)

- [ ] Add `endTurn()` method to game board component
- [ ] Call `TurnService::endTurn()`
- [ ] If game ends, redirect to match results page
- [ ] If AI turn, show "AI thinking..." state, execute AI stub, then refresh board
- [ ] "End Turn" button visibility: shown only after player has acted and is not over token limit

**Tests (`tests/Feature/Match/EndTurnActionTest.php`):**
- [ ] Test player can end turn after acting via Livewire
- [ ] Test "End Turn" button is hidden before player acts
- [ ] Test "End Turn" button is hidden when player is over token limit
- [ ] Test ending turn transitions to AI turn and back
- [ ] Test ending turn redirects to results when game end condition is met

### Phase 8.3: AI Opponent Stub Service (US-4.8)

- [ ] Create `App\Services\AiOpponentService`
- [ ] Method `executeTurn(GameMatch $match): void`
    - Receives full game state
    - **Stub implementation:** always rolls the dice with a random color result
    - Applies the roll via `DiceService::applyRoll()`
    - Evaluates if AI can purchase any face-up card — if so, purchases the first eligible one via `CardPurchaseService`
    - Checks token limit — if over, returns random excess tokens via `TokenLimitService`
    - Ends the AI's turn (toggles back to player)
- [ ] Method is designed as a clear extension point for future AI strategy implementation
- [ ] Accepts `DifficultyTier` as parameter for future difficulty-based behavior

**Tests (`tests/Feature/Services/AiOpponentServiceTest.php`):**
- [ ] Test AI stub executes a dice roll on its turn
- [ ] Test AI stub creates a turn record with correct participant type (AI)
- [ ] Test AI stub adds a token to AI's inventory
- [ ] Test AI stub purchases a card when it has the required tokens
- [ ] Test AI stub respects the 10-token limit
- [ ] Test AI stub returns turn to player after completing its action
- [ ] Test AI stub receives difficulty tier parameter
- [ ] Test AI stub handles "free" dice result (picks a random color)

### Phase 8.4: Game End Detection (US-5.2)

- [ ] Add game end check to `TurnService::endTurn()` and `CardPurchaseService::purchaseCard()`
- [ ] When `compartments_emptied >= 2`, trigger match finalization
- [ ] No further turns allowed after game end

**Tests (`tests/Feature/Match/GameEndTest.php`):**
- [ ] Test game does NOT end when only 1 compartment is emptied
- [ ] Test game ends when 2nd compartment becomes empty
- [ ] Test no further turns can be taken after game ends
- [ ] Test match status changes to `completed` on game end
- [ ] Test game end is triggered regardless of whose turn it is

---

