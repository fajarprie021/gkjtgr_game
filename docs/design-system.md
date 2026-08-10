# Bible Adventure — Design System

**Iteration 10** · Bible Adventure · Sekolah Minggu GKJ Tangerang

---

## 1. Colors

### Primary Palette (Forest Green / Sky Blue / Gold)

| Token | Hex | Usage |
|-------|-----|-------|
| `--game-primary` | `#3f6b55` | Brand, primary buttons, headers |
| `--game-secondary` | `#4d82c4` | Map path, secondary buttons, info |
| `--game-accent` | `#f0b54a` | Highlights, stars, available checkpoints |
| `--game-success` | `#4f9d69` | Correct answer, completed state |
| `--game-warning` | `#e4a83b` | Locked state, attention |
| `--game-danger` | `#cf645e` | Wrong answer, errors |

### Class Identity

| Class | Color | Symbol |
|-------|-------|--------|
| Kecil (SD 1-2) | `#63a96b` (Green) | 🌱 |
| Madya (SD 3-4) | `#4c82c5` (Blue) | 🌟 |
| Besar (SD 5-6) | `#7868b8` (Purple) | 🏆 |

### Backgrounds

- `--game-bg`: `#f7f4ea` (warm cream)
- `--game-surface`: `#ffffff` (cards)
- Map background: gradient sky (`#e1f5fe`)

---

## 2. Typography

```css
--font-heading: "Comic Sans MS", "Chalkboard SE", cursive;
--font-body: "Segoe UI", system-ui, sans-serif;
```

### Scale

| Token | Size | Usage |
|-------|------|-------|
| `text-xs` | 0.75rem | Captions, hints |
| `text-sm` | 0.875rem | Helper text |
| `text-base` | 1rem | Body |
| `text-lg` | 1.125rem | Body emphasis |
| `text-xl` | 1.25rem | Subtitle |
| `text-2xl` | 1.5rem | Section title |
| `text-3xl` | 1.875rem | Page title |
| `text-4xl` | 2.25rem | Hero |

---

## 3. Spacing

```css
--space-xs: 4px;
--space-sm: 8px;
--space-md: 16px;
--space-lg: 24px;
--space-xl: 32px;
--space-2xl: 48px;
```

---

## 4. Border Radius

```css
--radius-sm: 12px;  /* small chips, tags */
--radius-md: 18px;  /* buttons, cards */
--radius-lg: 28px;  /* hero cards, modals */
```

---

## 5. Shadows

```css
--shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.06);   /* resting */
--shadow-md: 0 10px 28px rgba(0, 0, 0, 0.1);   /* hover */
--shadow-button: 0 4px 0 rgba(0, 0, 0, 0.15);  /* tactile press */
```

---

## 6. Animation

### Timing

- `--transition-fast`: 150ms (hover, micro)
- `--transition-base`: 250ms (default)
- `--transition-slow`: 400ms (entrance, large)

### Easing

- `--ease-bounce`: `cubic-bezier(0.175, 0.885, 0.32, 1.275)` — playful pop
- `--ease-out`: `cubic-bezier(0, 0, 0.2, 1)` — calm exit

### Standard Animations

- `pulse-node` — available checkpoint (2s infinite)
- `bounce` — celebration (1s)
- `fadeIn` — screen entrance (0.5s)
- `slideIn` — card entrance (0.4s)

### Reduced Motion

All animations disabled when `@media (prefers-reduced-motion: reduce)`.

---

## 7. Touch Targets

Minimum: **44px** (Apple HIG / WCAG)
Comfortable: **56px** (default for game buttons)

---

## 8. Components

### Buttons

```
.game-btn {
  padding: 14px 28px;
  border-radius: var(--radius-md);
  font-family: var(--font-heading);
  font-weight: bold;
  text-transform: uppercase;
  box-shadow: var(--shadow-button);
  transition: all 0.1s;
  min-height: 56px;
}
```

Variants: `primary`, `secondary`, `accent`, `success`, `danger`.

### Cards

```
.game-card {
  background: var(--game-surface);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-sm);
  padding: 20px;
}
```

### Checkpoint States

| State | Background | Animation |
|-------|-----------|-----------|
| Locked | `#eeeeee` | none |
| Available | `var(--game-accent)` | `pulse-node` |
| Completed | `var(--game-success)` | none |

### Answer States

| State | Color | Icon |
|-------|-------|------|
| Correct | `--game-success` | ✓ Benar |
| Wrong | `--game-danger` | ✗ Coba Lagi |
| Neutral | `--game-primary` border | — |

---

## 9. Feedback Pattern

Always combine **icon + text + color**:

```
✓ Benar!
✗ Coba Lagi
```

Color alone is not accessible — icon and text are required.

---

## 10. Z-Index Scale

```
--z-base: 1;
--z-dropdown: 10;
--z-sticky: 20;
--z-modal-backdrop: 100;
--z-modal: 200;
--z-toast: 300;
```

---

## 11. Asset Structure

```
public/assets/
├── images/
│   ├── stories/        (checkpoint art)
│   ├── ui/             (interface illustrations)
│   ├── mascot/         (character)
│   └── backgrounds/    (sky, desert, grass)
├── icons/              (if using custom)
└── audio/
    ├── correct.mp3
    ├── wrong.mp3
    ├── complete.mp3
    └── ambient/
```

---

## 12. Accessibility

- Color contrast minimum **4.5:1** for text
- All interactive elements have focus rings
- All buttons meet **44px touch target**
- Animations respect `prefers-reduced-motion`
- Audio never required for feedback (visual always present)
- Semantic HTML (`button`, `nav`, `main`, `section`)

---

## 13. Class Identity Pattern

Each class card and story view uses:

- Border accent color (top 4-8px)
- Class icon emoji
- Class label badge
- Themed gradient (subtle)

This helps children associate content with their class level visually.

---

## 14. Map Visual States

- **Locked** — grey, lock icon, not tappable
- **Available** — gold pulse, scroll/touch active
- **Completed** — green with star, replayable

Path color: brown `#8d6e63` (dusty road).

---

## 15. Performance Budget

- CSS files: < 50KB total uncompressed
- JS files: < 100KB total uncompressed
- No images > 200KB
- Animation: GPU-accelerated transforms only

---

*Updated: Iteration 10 — Visual Polish & Design System*
