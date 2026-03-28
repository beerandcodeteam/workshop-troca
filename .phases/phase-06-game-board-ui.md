## Phase 6: Game Board UI

> Implements the game board layout and visual components.
> Reference: US-4.1 and `docs/design/troca_jogo/screen.png`

### Phase 6.1: Game Board Page & Layout (US-4.1)

- [ ] Create Livewire full-page component `pages::arena.match-board`
- [ ] Register route: `Route::livewire('/arena/match/{match}', 'pages::arena.match-board')`
- [ ] Validate that the authenticated user owns the match
- [ ] Load match with all relationships (compartments, cards, token inventories, quotation cards, turns)
- [ ] Build the board layout matching the design:
    - **Left column:** Match summary (total actions, dice rolls, trades) + recent history log
    - **Center:** Current objective card + dice roll action area
    - **Bottom:** Active quotation cards (2 cards with trade options and "Execute Trade" buttons)
    - **Top:** Player token inventory (5 color counts)

**Tests (`tests/Feature/Match/GameBoardTest.php`):**
- [ ] Test game board page renders for match owner
- [ ] Test game board returns 403 for non-owner
- [ ] Test game board displays player token inventory (5 colors)
- [ ] Test game board displays 4 card compartments with face-up cards
- [ ] Test game board displays 2 active quotation cards
- [ ] Test game board displays whose turn it is (player or AI)
- [ ] Test game board displays match summary stats
- [ ] Test completed match redirects to results page

### Phase 6.2: Token Inventory Display Component

- [ ] Create Livewire component or Blade partial for token inventory
- [ ] Display each color with count and `<x-token-dot>` icon
- [ ] Highlight colors the player has > 0 of
- [ ] Show total token count with warning if approaching 10

### Phase 6.3: Card Compartment Display Component

- [ ] Create Blade component for a single compartment
- [ ] Show the face-up card (top unpurchased) with its 5 token requirements using `<x-token-dot>`
- [ ] Show star badge if card has stars
- [ ] Show compartment star bonus indicator when activated
- [ ] Show card count remaining in compartment
- [ ] Grey out compartment when all cards purchased

### Phase 6.4: Quotation Card Display Component

- [ ] Create Blade component for an active quotation card
- [ ] Display all trade rows with left/right sides using `<x-token-dot>`
- [ ] "Execute Trade" button for each trade row
- [ ] Visually distinguish available vs unavailable trades (based on player tokens)
- [ ] Label bonus/special trade types (if applicable per design)

### Phase 6.5: Match History Log Component

- [ ] Create Blade component for the recent history log
- [ ] Display last N actions from `match_turns` in reverse chronological order
- [ ] Format action descriptions: "Rolled dice — received 1 blue token", "Trade completed", "Purchased card", etc.
- [ ] Use icons/colors to distinguish action types

### Phase 6.6: Drag-and-Drop Token Return UI (wire:sort)

- [ ] Create Livewire component for returning excess tokens using **Livewire 4's `wire:sort`**
- [ ] Display player's current tokens as draggable items
- [ ] Allow player to drag tokens to a "return" zone to discard them
- [ ] Show live count of tokens remaining and how many need to be returned
- [ ] Confirm button to finalize token return
- [ ] This component is shown only when the player exceeds the 10-token limit (US-4.6)

---

