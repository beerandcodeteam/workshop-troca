## Phase 2: Database, Models & Seeders

> Implements all migrations, Eloquent models, factories, and seeders from `docs/database-schema.md`.

### Phase 2.1: Lookup Table Migrations & Seeders

- [ ] Create migration and seeder for `token_colors` (5 colors with hex codes)
- [ ] Create migration and seeder for `difficulty_tiers` (3 tiers with XP rewards)
- [ ] Create migration and seeder for `match_statuses` (pending, in_progress, completed, abandoned)
- [ ] Create migration and seeder for `match_result_types` (player_win, ai_win, draw)
- [ ] Create migration and seeder for `turn_action_types` (roll_dice, trade, purchase_card, return_tokens)
- [ ] Create migration and seeder for `participant_types` (player, ai)
- [ ] Create migration and seeder for `trade_sides` (left, right)
- [ ] Create migration and seeder for `player_ranks` (Bronze through Diamond with min_xp thresholds)
- [ ] Create migration and seeder for `scoring_rules` (12 rows: 4 token brackets × 3 star levels)

**Tests (`tests/Feature/Database/LookupTableSeederTest.php`):**
- [ ] Test that all lookup tables are seeded with the correct number of rows
- [ ] Test that `token_colors` has exactly 5 entries with valid hex codes
- [ ] Test that `difficulty_tiers` has 3 entries ordered by `sort_order`
- [ ] Test that `scoring_rules` has 12 entries covering all bracket/star combinations
- [ ] Test that `player_ranks` has entries with increasing `min_xp` values

### Phase 2.2: Lookup Table Models

- [ ] Create `TokenColor` model with `$fillable`, relationships
- [ ] Create `DifficultyTier` model with `$fillable`, relationships
- [ ] Create `MatchStatus` model with `$fillable`, slug-based helper scopes
- [ ] Create `MatchResultType` model with `$fillable`
- [ ] Create `TurnActionType` model with `$fillable`
- [ ] Create `ParticipantType` model with `$fillable`
- [ ] Create `TradeSide` model with `$fillable`
- [ ] Create `PlayerRank` model with `$fillable`, scope for rank by XP
- [ ] Create `ScoringRule` model with `$fillable`, method to calculate points

**Tests (`tests/Feature/Models/LookupModelTest.php`):**
- [ ] Test `PlayerRank::forXp($xp)` returns the correct rank for given XP thresholds
- [ ] Test `ScoringRule::calculatePoints($remainingTokens, $starCount)` returns correct points for all 12 combinations

### Phase 2.3: User Model Updates & Migration

- [ ] Create migration to add `username` (unique, varchar 20), `total_xp` (default 0), and `player_rank_id` (nullable FK) columns to `users` table
- [ ] Remove `name` column from `users` table (replace with `username`)
- [ ] Update `User` model: update `$fillable`, add `playerRank()` belongsTo relationship, add `matches()` hasMany relationship
- [ ] Update `UserFactory` to generate `username` instead of `name`
- [ ] Update `DatabaseSeeder` test user to use `username`

**Tests (`tests/Feature/Models/UserModelTest.php`):**
- [ ] Test user can be created with `username`, `email`, `password`
- [ ] Test `username` uniqueness constraint
- [ ] Test `username` length validation (3–20 chars)
- [ ] Test user `playerRank` relationship returns correct rank
- [ ] Test user `matches` relationship returns associated matches

### Phase 2.4: Game Component Migrations & Models

- [ ] Create migration for `quotation_cards` table
- [ ] Create migration for `quotation_card_trades` table
- [ ] Create migration for `quotation_card_trade_items` table
- [ ] Create migration for `cards` table
- [ ] Create migration for `card_tokens` table
- [ ] Create `QuotationCard` model with relationships: `trades()` hasMany
- [ ] Create `QuotationCardTrade` model with relationships: `quotationCard()` belongsTo, `items()` hasMany, `leftItems()` / `rightItems()` filtered hasMany
- [ ] Create `QuotationCardTradeItem` model with relationships: `trade()` belongsTo, `tradeSide()` belongsTo, `tokenColor()` belongsTo
- [ ] Create `Card` model with relationships: `tokens()` hasMany
- [ ] Create `CardToken` model with relationships: `card()` belongsTo, `tokenColor()` belongsTo

**Tests (`tests/Feature/Models/GameComponentModelTest.php`):**
- [ ] Test `QuotationCard` has many `QuotationCardTrade`
- [ ] Test `QuotationCardTrade` has `leftItems()` and `rightItems()` returning correct sides
- [ ] Test `Card` has many `CardToken` and each card totals exactly 5 tokens
- [ ] Test `Card` `star_count` accepts values 0, 1, 2

### Phase 2.5: Game Component Seeders

- [ ] Create `QuotationCardSeeder` — seeds all 10 quotation cards with their trade equivalences (from `docs/jogo-original/Exemplo Quotations.jpg`)
- [ ] Create `CardSeeder` — seeds all 45 cards with their token requirements and star ratings (from `docs/jogo-original/Cards.jpg`)
- [ ] Update `DatabaseSeeder` to call all seeders in correct order

**Tests (`tests/Feature/Database/GameComponentSeederTest.php`):**
- [ ] Test that exactly 10 quotation cards are seeded
- [ ] Test each quotation card has at least 2 trade rows
- [ ] Test all trade items reference valid token colors and trade sides
- [ ] Test that exactly 45 cards are seeded
- [ ] Test each card has exactly 5 total tokens (summing quantities)
- [ ] Test cards have appropriate star_count distribution

### Phase 2.6: Match State Migrations & Models

- [ ] Create migration for `matches` table with all columns and indexes
- [ ] Create migration for `match_quotation_cards` pivot table
- [ ] Create migration for `match_compartments` table
- [ ] Create migration for `match_compartment_cards` table
- [ ] Create migration for `match_token_inventories` table
- [ ] Create migration for `match_turns` table
- [ ] Create `Match` model (namespaced as `GameMatch` or `App\Models\Match` with backtick escaping) with relationships:
    - `user()` belongsTo
    - `difficultyTier()` belongsTo
    - `matchStatus()` belongsTo
    - `matchResultType()` belongsTo
    - `currentParticipantType()` belongsTo
    - `quotationCards()` belongsToMany through pivot
    - `compartments()` hasMany
    - `tokenInventories()` hasMany
    - `turns()` hasMany
- [ ] Create `MatchQuotationCard` pivot model
- [ ] Create `MatchCompartment` model with relationships: `match()` belongsTo, `cards()` hasMany, scope `faceUpCard()`
- [ ] Create `MatchCompartmentCard` model with relationships: `compartment()` belongsTo, `card()` belongsTo, `purchasedByParticipantType()` belongsTo
- [ ] Create `MatchTokenInventory` model with relationships: `match()` belongsTo, `participantType()` belongsTo, `tokenColor()` belongsTo
- [ ] Create `MatchTurn` model with relationships and `$casts` for `action_data` as `array`/`json`
- [ ] Create `MatchFactory` for test convenience

**Tests (`tests/Feature/Models/MatchModelTest.php`):**
- [ ] Test `GameMatch` can be created with required fields
- [ ] Test `GameMatch` has correct relationships (user, difficultyTier, compartments, turns, tokenInventories)
- [ ] Test `MatchCompartment` `faceUpCard()` returns the first unpurchased card by position
- [ ] Test `MatchTokenInventory` unique constraint on (match, participant, color)
- [ ] Test `MatchTurn` `action_data` is cast to array

---

