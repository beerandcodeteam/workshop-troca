# TROCA — Project Description

## Overview

TROCA is a web-based digital adaptation of a Brazilian tabletop token-trading game, reimagined as a **single-player vs AI** experience. The player competes against an AI opponent in a strategic game of resource management, where the goal is to collect colored tokens through dice rolls and smart trades, then spend them to purchase cards and earn the highest score.

The core loop revolves around a simple but deep decision each turn: **roll the dice to gain a new token, or trade tokens you already have** using the active quotation cards. Quotation cards define exchange rates between token colors (e.g., 1 red = 1 blue + 1 green + 1 yellow + 1 white), and the player must choose which 2 of the 10 available quotation cards to play with at the start of each match. Cards on the board require specific combinations of 5 colored tokens to purchase, and their point value depends on how few tokens the player retains after buying — rewarding efficient resource management.

The game features a **neon noir visual identity** with a dark, glowing UI aesthetic. Players register, log in, select their quotation cards and target objective, then enter the arena to play against the AI. The project is built for **educational purposes**, serving as a full-stack development exercise covering real-time game logic, AI decision-making, authentication, and modern UI design.

### Key Concepts

- **Tokens (Fichas):** 5 colors — red, green, white, yellow, blue. Each player can hold a maximum of 10 at any time.
- **Quotation Cards (Cartões de Cotação):** 10 cards defining exchange rates between token colors. Each match uses exactly 2, chosen by the player before the game starts.
- **Cards (Cartas):** 45 cards distributed across 4 compartments. Each card shows 5 colored dots representing the token cost to purchase it. Some cards are marked with 1 or 2 stars, which increase their scoring potential.
- **Dice:** A color die with 5 color faces + 1 "free choice" face.
- **Scoring:** Points awarded per card purchase based on how many tokens the player holds afterward (fewer tokens = more points), multiplied by the card's star rating.
- **Game End:** The game ends when cards from 2 of the 4 compartments are fully sold. Highest score wins.

---

## Tech Stack

| Layer | Technology                                           |
|---|------------------------------------------------------|
| **Backend** | PHP 8.5, Laravel 13                                  |
| **Frontend** | Livewire 4, Alpine.js, Tailwind CSS                  |
| **Database** | PostgresSql (via Laravel Sail)                       |
| **Testing** | Pest 4                                               |
| **Dev Environment** | Laravel Sail (Docker)                                |
| **Code Formatting** | Laravel Pint                                         |
| **Asset Bundling** | Vite                                                 |
| **AI Opponent** | Server-side game engine with AI strategy logic (PHP) |

---

## Core Workflows

### 1. Authentication

The player registers with a username, email, and password, or logs in via email/password or social providers (Google, Apple). After authentication, the player enters the main arena dashboard.

### 2. Match Setup (Pre-Game Configuration)

Before starting a match, the player configures the game session:

1. **Select 2 Quotation Cards** — Choose from the 10 available quotation cards. Each card defines a set of token exchange rates that will be active during the match. The AI opponent will use the same 2 quotation cards.
2. **Select Target Card (Objective)** — Choose a difficulty tier for the match objective (e.g., "Padrão Primário", "Cadeia Cruzada", "Mestre do Caos"), which determines the target card configuration and XP reward.
3. **Start Match** — The game board initializes with shuffled cards distributed across 4 compartments and the selected quotation cards in play.

### 3. Gameplay Loop (Turn-Based)

Each match alternates turns between the player and the AI. On each turn, the active participant must choose **one** of the following actions:

- **Roll the Dice** — Roll the color die to receive 1 token of the resulting color. If the die lands on "free", the player chooses any color.
- **Trade Tokens** — Use one of the 2 active quotation cards to exchange tokens. The trade follows the exact rates defined on the chosen quotation card (bidirectional). Only one trade is allowed per turn.

After rolling or trading, the player may optionally **purchase a card** if they hold the 5 tokens matching the card's color requirements. Purchasing a card:
- Returns the 5 matching tokens to the board supply.
- Awards points based on how many tokens the player retains after the purchase (see scoring table).
- Must be preceded by a dice roll or trade in the same turn.

**Token Limit:** A player may never hold more than 10 tokens. If they exceed this limit, they must reduce their count by purchasing a card or returning excess tokens.

**Compartment Stars:** When all cards in a compartment are sold, a star bonus activates — all future card purchases score as if they had an additional star.

### 4. AI Opponent Logic

The AI acts as a fully autonomous opponent that follows the same rules as the player. It evaluates the board state each turn and decides whether to roll the dice or trade tokens, using strategy heuristics such as:

- Prioritizing trades that move toward completing a purchasable card.
- Evaluating the point efficiency of available card purchases (fewer remaining tokens = higher score).
- Managing token count to stay within the 10-token limit.
- Considering star bonuses from nearly emptied compartments.

### 5. Scoring & Game End

The game ends when cards from **2 of the 4 compartments** have been fully purchased. At that point:

- Each card purchase is scored based on the number of tokens the buyer held after the purchase, cross-referenced with the card's star rating:

| Remaining Tokens | Normal | 1 Star | 2 Stars |
|---|---:|---:|---:|
| 3 or more | 1 | 2 | 3 |
| 2 | 2 | 3 | 5 |
| 1 | 3 | 5 | 8 |
| 0 | 5 | 8 | 12 |

- The player with the **highest total score** wins.
- **Tiebreaker:** Fewest cards purchased. If still tied, the match is a draw.

### 6. Post-Game & Progression

After a match concludes, the player sees a results summary with their score breakdown, the AI's score, and XP earned. Match history and stats are tracked per player profile, feeding into a **leaderboard** system for competitive ranking.
