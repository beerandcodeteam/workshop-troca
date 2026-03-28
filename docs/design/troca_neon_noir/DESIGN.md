# Design System Strategy: Neon Noir & Tonal Precision

## 1. Overview & Creative North Star
**Creative North Star: "The Kinetic Void"**

This design system is built to transform a gaming interface into a premium, immersive environment that feels less like a software application and more like a high-stakes digital arena. We move beyond standard dark modes by utilizing a "Neon Noir" aesthetic—deep, ink-like foundations juxtaposed with hyper-vibrant, glowing interactive tokens.

To break the "template" look, we reject the rigid grid in favor of **Intentional Depth**. This system prioritizes visual breathing room (`Spacing 12` and `16`) and uses asymmetrical layering to guide the eye. Instead of boxing content in, we allow the UI to emerge from the shadows using tonal shifts, making the interface feel expansive, mysterious, and high-end.

---

## 2. Colors: The Depth Palette

Our color strategy is not just about aesthetics; it is about architectural hierarchy.

### The "No-Line" Rule
**Explicit Instruction:** Designers are prohibited from using 1px solid borders to define sections. All separation must be achieved through background shifts. For example:
- A `surface-container-low` (#111417) module should sit directly on a `surface` (#0c0e12) background.
- This creates a soft, sophisticated edge that feels "molded" rather than "outlined."

### Surface Hierarchy & Nesting
Use the surface tiers to stack importance. An inventory card might be `surface-container-high` (#1c2025), nested inside a game board of `surface-container` (#171a1e). This creates a physical sense of "elevation" without the clutter of shadows.

### The Glass & Gradient Rule
For primary actions (e.g., "Lançar Dado"), use a transition from `primary` (#97a9ff) to `primary-container` (#849aff). For floating overlays or tooltips, apply a `backdrop-blur` of 12px-20px over a semi-transparent `surface-variant` to achieve a "frosted glass" effect, allowing the neon tokens beneath to bleed through softly.

---

## 3. Typography: Editorial Impact

We use a dual-font approach to balance brutalist impact with technical clarity.

- **Display & Headlines (Space Grotesk):** This is our "voice." The geometric, wide stance of Space Grotesk should be used for game titles and major section headers. Use `display-lg` for victory states and `headline-sm` for module titles. 
- **Body & Titles (Manrope):** Manrope provides high legibility for technical data. Its neutral, modern tone ensures that long lists of game rules or history logs remain readable under high-contrast conditions.
- **Labeling:** Use `label-sm` in uppercase with wide letter-spacing (0.05em) for category headers (e.g., "SEU INVENTÁRIO") to evoke a technical, "HUD" (Heads-Up Display) feel.

---

## 4. Elevation & Depth: Tonal Layering

Traditional drop shadows are replaced by **Ambient Luminance**.

- **The Layering Principle:** Depth is structural. Use the `surface-container-lowest` (#000000) for the deepest recesses of the UI and `surface-bright` (#282c32) for the most interactive top-level elements.
- **Ambient Shadows:** For floating modals, use a shadow with a blur of 40px and 8% opacity. The shadow color must be a tinted version of `surface_tint` (#97a9ff) to simulate the glow of the neon tokens hitting the surface.
- **The Ghost Border:** If a boundary is required for accessibility, use `outline-variant` (#46484c) at **15% opacity**. It should be felt, not seen.
- **Glow states:** Interactive tokens (Red, Green, Blue) should utilize an inner-glow of their own color to suggest they are light sources within the interface.

---

## 5. Components: Precision Gaming Elements

### Buttons
- **Primary:** High-impact. Background: `primary` (#97a9ff). Text: `on_primary` (#002283). Shape: `md` (0.75rem). Add a subtle outer glow using the `primary` color.
- **Secondary/Tertiary:** Use `surface-container-high` as the base with a `Ghost Border`.

### Interactive Tokens (The Chips)
- **Visuals:** Tokens must be perfectly circular (`full` roundedness). 
- **State:** When active, tokens use a 4px `primary` outer ring or a 10% brightness increase.
- **Grouping:** Use `Spacing 2` between tokens in a sequence.

### Cards & Modules
- **Rule:** Forbid divider lines. Use `Spacing 4` or `6` of vertical white space to separate list items. 
- **Backgrounds:** Use `surface-container-low` for secondary modules. For active selections (like choosing a Cotação), transition the card background to `surface-variant` or add a subtle `primary` glow.

### Game Inputs (Lançar Dado / Comprar Carta)
- These are the core loop. They should be oversized using `headline-sm` typography and feature a `primary_dim` (#3e65ff) gradient to differentiate them from static UI.

---

## 6. Do's and Don'ts

### Do:
- **Use Intentional Asymmetry:** Align technical data to the right and narrative/gameplay data to the left to create a dynamic layout.
- **Leverage the Dark:** Let the `#0c0e12` background breathe. High-end design is defined by what you *don't* fill.
- **Subtle Motion:** Use 200ms ease-in-out transitions for hover states on tokens and cards.

### Don't:
- **Don't use pure white text for body copy:** Use `on_surface_variant` (#a9abb0) for secondary info to reduce eye strain in dark mode.
- **Don't use "Default" Shadows:** Avoid muddy grey shadows. If it glows, it must glow with its own hue.
- **Don't use 1px borders:** Rely on the `surface-container` tiers to define the "architecture" of the screen.