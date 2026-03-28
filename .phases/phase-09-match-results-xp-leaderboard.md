## Phase 9: Match Results, XP & Leaderboard

> Implements post-game results, XP progression, and the leaderboard.
> Reference: US-5.3, US-6.1, US-6.2

### Phase 9.1: Match Finalization Service (US-5.3)

- [ ] Create `App\Services\MatchFinalizationService`
- [ ] Method `finalizeMatch(GameMatch $match): void`
    - Determine winner: compare `player_score` vs `ai_score`
    - Apply tiebreaker: fewer cards purchased wins; if still tied, result = draw
    - Set `match_result_type_id`
    - Calculate XP: `base_xp_reward` from difficulty tier + `win_bonus_xp` if player won
    - Set `xp_earned` on match
    - Add XP to user's `total_xp`
    - Update user's `player_rank_id` based on new total XP
    - Set match status to `completed`, set `completed_at`

**Tests (`tests/Feature/Services/MatchFinalizationServiceTest.php`):**
- [ ] Test player wins when player_score > ai_score
- [ ] Test AI wins when ai_score > player_score
- [ ] Test tiebreaker: fewer cards purchased wins on score tie
- [ ] Test draw when both scores and card counts are equal
- [ ] Test XP is calculated correctly: base + win bonus
- [ ] Test XP is added to user's total_xp
- [ ] Test user's player_rank_id is updated when crossing a rank threshold
- [ ] Test match status is set to `completed`
- [ ] Test `completed_at` timestamp is set
- [ ] Test losing player still earns base XP (no win bonus)

### Phase 9.2: Match Results Page (US-5.3)

- [ ] Create Livewire full-page component `pages::arena.match-results`
- [ ] Register route: `Route::livewire('/arena/match/{match}/results', 'pages::arena.match-results')`
- [ ] Display:
    - Winner announcement (Player Win / AI Win / Draw)
    - Player final score with per-card breakdown (card, tokens remaining, star count, points)
    - AI final score with per-card breakdown
    - Total cards purchased by each side
    - Difficulty tier played
    - XP earned
- [ ] "Play Again" button → redirect to match setup
- [ ] "Back to Arena" button → redirect to dashboard

**Tests (`tests/Feature/Match/MatchResultsTest.php`):**
- [ ] Test results page renders for completed match
- [ ] Test results page shows correct winner
- [ ] Test results page shows player and AI scores
- [ ] Test results page shows XP earned
- [ ] Test results page returns 404 for in-progress match
- [ ] Test results page returns 403 for non-owner
- [ ] Test "Play Again" link navigates to match setup
- [ ] Test "Back to Arena" link navigates to dashboard

### Phase 9.3: Leaderboard Page (US-6.1)

- [ ] Create Livewire full-page component `pages::leaderboard.index`
- [ ] Register route: `Route::livewire('/leaderboard', 'pages::leaderboard.index')`
- [ ] Query users ordered by `total_xp` descending with pagination
- [ ] For each player, compute:
    - Rank position
    - Username
    - Player rank name (from `player_ranks`)
    - Total matches played (count of completed matches)
    - Total wins (count of matches with `player_win` result)
    - Total XP
- [ ] Highlight the current authenticated player's row
- [ ] Paginate results (e.g., 20 per page)

**Tests (`tests/Feature/Leaderboard/LeaderboardTest.php`):**
- [ ] Test leaderboard page renders for authenticated user
- [ ] Test players are sorted by total_xp descending
- [ ] Test current player's row is visually marked
- [ ] Test leaderboard shows correct match counts and win counts
- [ ] Test leaderboard paginates correctly
- [ ] Test leaderboard handles users with 0 matches

### Phase 9.4: XP & Rank Progression (US-6.2)

- [ ] Ensure `MatchFinalizationService` correctly updates XP (from Phase 9.1)
- [ ] Create an Eloquent Observer or integrate into finalization: recalculate `player_rank_id` whenever `total_xp` changes
- [ ] Display XP and rank on the arena dashboard (from Phase 4.1)
- [ ] Display XP and rank on the leaderboard (from Phase 9.3)

**Tests (`tests/Feature/Progression/XpProgressionTest.php`):**
- [ ] Test user starts at Bronze rank (0 XP)
- [ ] Test user rank updates to Silver at 500 XP
- [ ] Test user rank updates to Gold at 1500 XP
- [ ] Test user rank updates to Platinum at 3500 XP
- [ ] Test user rank updates to Diamond at 7000 XP
- [ ] Test rank doesn't downgrade (XP is cumulative, never decreases)
- [ ] Test XP from multiple matches accumulates correctly

---

