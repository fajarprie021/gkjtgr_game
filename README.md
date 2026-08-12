# Bible Adventure - Sekolah Minggu GKJ Tangerang

Prototype web game edukasi untuk pembelajaran Alkitab anak-anak.

## Technology Stack

- **Backend**: PHP 8.3
- **Frontend**: HTML5, Bootstrap 5.3, Vanilla JavaScript ES6+
- **Database**: MySQL 8 (prepared, currently using localStorage)
- **Assets**: SVG, WebP, Bootstrap Icons

## Requirements

- PHP 8.0+
- Web server (Apache/Nginx) or PHP built-in server
- Modern web browser with ES6+ support

## Installation

1. Clone repository
2. Configure database connection in `config/database.php`
3. Start development server:

```bash
cd public
php -S localhost:8000
```

4. Open browser: `http://localhost:8000`
5. Health check: `http://localhost:8000/health.php`

## Project Structure

```
gkjtgr_game/
│
├── config/
│   └── database.php          # Database configuration
│
├── docs/
│   └── prompts/              # Product requirements documentation
│       ├── 01-master-prompt.md
│       ├── 02-game-mechanics.md
│       ├── 03-dashboard-map.md
│       ├── 04-bible-story-learning-content.md
│       ├── 05-question-generator.md
│       ├── 06-ui-visual-direction.md
│       ├── 07-coding-assistant.md
│       └── 08-playtest-qa.md
│
├── public/
│   ├── index.php             # Main entry point
│   │
│   ├── api/                  # PHP API endpoints
│   │   ├── eras.php          # Bible timeline eras
│   │   ├── stories.php       # Story data with relationships
│   │   └── questions.php     # Class-based questions
│   │
│   └── assets/
│       ├── css/
│       │   ├── theme.css         # Design tokens & base styles
│       │   ├── components.css    # Reusable UI components
│       │   └── game.css          # Game-specific styles
│       │
│       └── js/
│           ├── progress.js   # Progress management service
│           ├── api.js        # API communication layer
│           ├── map.js        # Data-driven map renderer
│           ├── game.js       # Game mechanic logic
│           └── app.js        # Main application controller
│
├── ITERATION_2_REPORT.md     # Iteration 2 audit report
├── ITERATION_3_REPORT.md     # Iteration 3 architecture report
└── README.md
```

## Bible Map Architecture

### Data Flow

```
API Endpoints (PHP)
    ↓
API Service (JavaScript)
    ↓
Application State
    ↓
Progress Service (localStorage)
    ↓
Map Renderer (data-driven)
    ↓
User Interface
```

### Core Concepts

#### 1. Timeline Eras

Stories are organized into biblical eras:

- **Permulaan** (Beginning): Creation, Noah
- **Para Leluhur** (Patriarchs): Abraham, Isaac, Jacob, Joseph
- **Keluaran** (Exodus): Moses, Wilderness
- **Tanah Perjanjian** (Promised Land): Joshua, Judges
- **Kerajaan** (Kingdom): Samuel, Saul, David, Solomon
- **Nabi-nabi** (Prophets): Divided Kingdom era
- **Pembuangan** (Exile): Babylon & Return
- **Yesus Kristus** (Jesus): Birth, Ministry, Cross, Resurrection
- **Gereja Mula-mula** (Early Church): Pentecost, Paul

#### 2. Story Data Structure

Each story contains:

```javascript
{
  id: 'creation',
  slug: 'creation',
  era_id: 'beginning',
  title: 'Penciptaan',
  reference: 'Kejadian 1-2',
  order: 1,
  previous_id: null,
  next_id: 'noah',
  map_x: 10,  // Position percentage
  map_y: 50,
  icon: 'globe-americas',
  is_active: true,
  summary: '...',
  learning_value: '...'
}
```

#### 3. Story Status Calculation

Status is calculated dynamically based on progress:

- **completed**: Story is in `completedStories` array
- **available**: First story OR previous story is completed
- **locked**: Previous story not yet completed

```javascript
ProgressService.getStoryStatus(story, allStories);
```

#### 4. Progress Storage

Progress is stored in localStorage:

```javascript
{
  selectedClass: 'small|medium|large',
  completedStories: ['creation', 'noah'],
  lastStory: 'noah',
  createdAt: '2026-08-10T...',
  updatedAt: '2026-08-10T...'
}
```

Access via `ProgressService`:

```javascript
ProgressService.getProgress();
ProgressService.completeStory(storyId);
ProgressService.isStoryCompleted(storyId);
ProgressService.getStoryStatus(story, allStories);
ProgressService.canAccessStory(storyId, allStories);
```

### How to Add a New Story

1. **Add story data** in `public/api/stories.php`:

```php
[
    'id' => 'new_story',
    'slug' => 'new-story',
    'era_id' => 'beginning',
    'title' => 'New Story Title',
    'reference' => 'Bible Reference',
    'order' => 6,
    'previous_id' => 'moses',
    'next_id' => null,
    'map_x' => 95,
    'map_y' => 45,
    'icon' => 'book',
    'is_active' => true,
    'summary' => '...',
    'learning_value' => '...'
]
```

2. **Add questions** in `public/api/questions.php`:

```php
'new_story' => [
    'small' => [...],
    'medium' => [...],
    'large' => [...]
]
```

3. **Map automatically renders** the new checkpoint with correct status.

No HTML or map layout changes needed!

### How to Add a New Era

1. **Add era data** in `public/api/eras.php`:

```php
[
    'id' => 'new_era',
    'title' => 'New Era Title',
    'order' => 9,
    'description' => 'Era description'
]
```

2. **Assign stories** to the era using `era_id` in story data.

### Class System

Three difficulty levels share the same timeline:

- **Kelas Kecil** (Small): SD 1–2, simpler language
- **Kelas Madya** (Medium): SD 3–4, moderate complexity
- **Kelas Besar** (Large): SD 5–6, deeper concepts

Questions differ by class, but story content and map are identical.

## Current Prototype Scope

### Iteration 2: UI Foundation

- ✅ Custom design system (not Bootstrap default)
- ✅ Adventure-themed UI components
- ✅ Mobile-first responsive design
- ✅ SVG winding map path
- ✅ Touch-friendly interactions

### Iteration 3: Data Architecture

- ✅ Era-based timeline structure
- ✅ Data-driven map rendering
- ✅ Centralized progress management
- ✅ Dynamic status calculation
- ✅ Story relationship system
- ✅ Easy story addition workflow

### Active Stories

Currently playable:

1. **Penciptaan** (Creation) - Full content
2. **Nuh** (Noah) - Unlocked after Creation
3. **Abraham** - Unlocked after Noah
4. **Yusuf** (Joseph) - Unlocked after Abraham
5. **Musa** (Moses) - Unlocked after Joseph

## Development Commands

### Start Server

```bash
cd public
php -S localhost:8000
```

### Reset Progress (for testing)

Open browser console:

```javascript
ProgressService.resetProgress();
location.reload();
```

### Check Progress

```javascript
ProgressService.getProgress();
ProgressService.getStats(App.state.stories);
```

## Testing Checklist

- [x] New user sees Creation available, others locked
- [x] Complete Creation unlocks Noah
- [x] Progress persists after page refresh
- [x] Locked stories cannot be accessed via URL
- [x] Class selection affects question difficulty
- [x] Map renders from story data
- [x] Timeline context displays correctly
- [x] Mobile responsive (360px+)
- [x] Touch targets ≥ 60px
- [x] No console errors

## Known Limitations

1. **Content**: Story content needs Bible verification
2. **Questions**: Currently 2-3 questions per story (need 5-7)
3. **Authentication**: No user accounts (localStorage only)
4. **Database**: Not yet using MySQL (prepared for future)
5. **Game Mechanics**: Only multiple-choice questions implemented

## Documentation

- [`docs/v1-release.md`](docs/v1-release.md) — final scope, versioning, release checklist
- [`docs/teacher-guide.md`](docs/teacher-guide.md) — panduan guru
- [`docs/admin-guide.md`](docs/admin-guide.md) — panduan admin
- [`docs/developer-guide.md`](docs/developer-guide.md) — panduan developer
- [`docs/operations.md`](docs/operations.md) — runbook operasional
- [`docs/security.md`](docs/security.md) — catatan keamanan
- [`docs/troubleshooting.md`](docs/troubleshooting.md) — pemecahan masalah umum
- [`docs/deployment.md`](docs/deployment.md) — cara deploy ke server
- [`docs/backup-recovery.md`](docs/backup-recovery.md) — backup & restore
- [`docs/production-readiness.md`](docs/production-readiness.md) — cek kesiapan production
- [`docs/v2-backlog.md`](docs/v2-backlog.md) — kandidat fitur untuk v2
- [`docs/content-release.md`](docs/content-release.md) — daftar story rilis v1
- [`docs/mechanics-release.md`](docs/mechanics-release.md) — daftar mechanic rilis v1
- [`docs/pilot-index.md`](docs/pilot-index.md) — peta dokumen pilot
- [`docs/post-pilot-fix-plan.md`](docs/post-pilot-fix-plan.md) — rencana perbaikan pasca-pilot
- [`docs/post-pilot-validation.md`](docs/post-pilot-validation.md) — laporan validasi pasca-pilot
- [`CHANGELOG.md`](CHANGELOG.md) — catatan perubahan rilis

## Pilot Testing & Classroom Validation

### What this phase is for

Iteration 13 is not about adding features.
It is about proving the prototype can be used in real classroom conditions.

### What we observe

- Teacher usability
- Child usability
- Team collaboration
- Learning evidence
- Technical stability
- Bible content accuracy
- Analytics vs. real behavior

### Pilot documents

- [`docs/pilot-index.md`](docs/pilot-index.md) — peta dan urutan baca semua dokumen pilot
- [`docs/pilot-checklist.md`](docs/pilot-checklist.md) — before / during / after checklist
- [`docs/pilot-observation.md`](docs/pilot-observation.md) — per-session observation sheet
- [`docs/pilot-issues.md`](docs/pilot-issues.md) — triaged issue log
- [`docs/pilot-summary.md`](docs/pilot-summary.md) — final pilot summary
- [`docs/pilot-summary-example.md`](docs/pilot-summary-example.md) — contoh format rangkuman

### Pilot rules

1. Observe before changing anything.
2. Measure real classroom behavior.
3. Learn from the evidence.
4. Document issues with severity and frequency.
5. Only fix blockers or critical problems during the pilot window.

### Success criteria

- Teacher can run the main flow without significant developer help.
- Most children can join with minimal help.
- The chosen story mission can be completed in available class time.
- There are no critical technical failures.
- Learning check shows basic comprehension.
- Team mode creates positive interaction.

### Issue prioritization

Use this order:

- **MUST FIX** — blocker, security, wrong Bible content, broken progression
- **SHOULD FIX** — teacher flow friction, question wording, team UX, map clarity
- **COULD FIX** — extra animation, visual polish, minor copy
- **NOT NOW** — chat, advanced avatars, global leaderboard, new engine

## Next Steps

### Priority 1: Content

- [ ] Verify & complete Bible content for all 5 stories
- [ ] Add more questions per checkpoint (5-7)
- [ ] Add memory verses

### Priority 2: Polish

- [ ] Add SVG decorative elements to map (trees, hills, tents)
- [ ] Implement character value badges
- [ ] Add simple animation effects

### Priority 3: Pilot Support

- [ ] Run internal teacher test
- [ ] Run small-group pilot
- [ ] Run real classroom pilot when ready
- [ ] Record observations in `docs/pilot-observation.md`
- [ ] Log issues in `docs/pilot-issues.md`
- [ ] Write session summaries in `docs/pilot-summary.md`
- [ ] Create post-pilot fix plan in `docs/post-pilot-fix-plan.md`
- [ ] Validate fixes in `docs/post-pilot-validation.md`

### Priority 4: Enhancement

- [ ] Additional question types (matching, sequencing)
- [ ] Simple achievement system
- [ ] Certificate generation

## Design Principles

1. **Data-driven**: Map renders from structured data
2. **Separation of concerns**: Data ≠ Logic ≠ Presentation
3. **Progressive disclosure**: Unlock stories sequentially
4. **Mobile-first**: Designed for smartphones
5. **Educational tone**: Encouraging, not punitive
6. **Accessible**: Semantic HTML, keyboard navigation, ARIA labels

## Visual Identity

- **Not**: Admin dashboard, LMS, traditional church website
- **Is**: Storybook adventure, illustrated Bible journey
- **Colors**: Forest green, sky blue, golden accents
- **Feel**: Warm, inviting, child-friendly

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers with ES6+ support

## License

Private project for GKJ Tangerang Sekolah Minggu.

## Contact

For questions about this prototype, refer to documentation in `docs/prompts/`.
