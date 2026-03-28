## Phase 1: Frontend Foundation (No Tests Required)

> Design system setup, reusable UI components, and base layouts following the neon noir visual identity.
> Reference: `docs/design/*` screens for colors, typography, and component styles.

### Phase 1.1: Tailwind Theme Configuration

- [ ] Configure custom color palette in `resources/css/app.css` for neon noir theme:
    - Background colors (deep dark blues/blacks)
    - Primary accent (neon blue/purple glow)
    - Success/green accent
    - Danger/red accent
    - Warning/yellow accent
    - Text colors (light grays, whites)
    - Border/glow colors for card outlines
    - Token-specific colors: red (`#EF4444`), green (`#22C55E`), white (`#F8FAFC`), yellow (`#EAB308`), blue (`#3B82F6`)
- [ ] Configure custom font (from design screens)
- [ ] Set up dark theme as the default (no light mode toggle needed)

### Phase 1.2: Base UI Components (Blade Components)

- [ ] **Button component** (`<x-button>`) — primary, secondary, danger, ghost variants; sizes (sm, md, lg); disabled state; loading state with spinner; glow effect on hover
- [ ] **Text input component** (`<x-input>`) — with label, placeholder, error state, icon slot (left); dark-themed with subtle border glow
- [ ] **Select component** (`<x-select>`) — with label, options, placeholder, error state; dark-themed
- [ ] **Checkbox component** (`<x-checkbox>`) — with label, checked state; neon accent color
- [ ] **Radio button component** (`<x-radio>`) — with label, selected state; neon accent color
- [ ] **Modal component** (`<x-modal>`) — overlay backdrop, centered content, close button, title slot, body slot, footer slot; animation (fade/slide); Alpine.js for open/close toggle
- [ ] **Card component** (`<x-card>`) — dark background, subtle border glow, header slot, body slot; hover effect
- [ ] **Badge component** (`<x-badge>`) — for ranks, statuses; color variants
- [ ] **Alert/Flash message component** (`<x-alert>`) — success, error, warning, info variants; dismissible with Alpine.js

### Phase 1.3: Guest Layout (Unauthenticated)

- [ ] Create `resources/views/layouts/guest.blade.php`
    - Centered content card on dark background
    - TROCA logo/title at top
    - Minimal layout matching login/register design screens
    - Flash message area
    - Livewire and Vite asset directives

### Phase 1.4: Authenticated Layout (App Shell)

- [ ] Create/update `resources/views/layouts/app.blade.php`
    - Left sidebar navigation with:
        - Player avatar/username area at top
        - Arena link (active state)
        - Leaderboard link
        - Settings link
        - "Roll Dice" CTA button at bottom of sidebar
        - Support link
        - Logout link
    - Top bar with notifications icon and player username/rank
    - Main content area
    - Flash message area
    - Neon noir styling matching `docs/design/troca_jogo/screen.png`
- [ ] Create sidebar navigation Blade component (`<x-sidebar>`)
- [ ] Create top bar Blade component (`<x-topbar>`)

### Phase 1.5: Token Color Dot Component

- [ ] Create `<x-token-dot>` component — renders a colored circle for token display
    - Props: color (red/green/white/yellow/blue), size (sm, md, lg)
    - Uses the token hex colors from the theme
    - Reused across the entire game UI (cards, quotation cards, inventory, etc.)

---

