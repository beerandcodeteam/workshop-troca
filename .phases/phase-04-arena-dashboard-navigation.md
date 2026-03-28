## Phase 4: Arena Dashboard & Navigation

> Implements the main authenticated dashboard.
> Reference: US-2.1

### Phase 4.1: Arena Dashboard Page (US-2.1)

- [ ] Create Livewire full-page component `pages::arena.index`
- [ ] Display player username and current rank (from `player_ranks` via XP threshold)
- [ ] Display total XP with visual progress indicator
- [ ] Display "New Match" CTA button (links to match setup)
- [ ] If player has an ongoing match (status = `in_progress`), show "Resume Match" button
- [ ] Register route: `Route::livewire('/arena', 'pages::arena.index')`

**Tests (`tests/Feature/Arena/DashboardTest.php`):**
- [ ] Test arena dashboard renders for authenticated user (GET /arena returns 200)
- [ ] Test dashboard displays player username
- [ ] Test dashboard displays player rank based on XP
- [ ] Test dashboard shows "New Match" button
- [ ] Test dashboard shows "Resume Match" button when an in-progress match exists
- [ ] Test dashboard does NOT show "Resume Match" when no active match exists
- [ ] Test unauthenticated user is redirected to login

---

