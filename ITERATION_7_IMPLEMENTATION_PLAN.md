# ITERATION 7 IMPLEMENTATION PLAN

## Classroom Game Session - Teacher Login & Team Mode

**Status:** Planning Complete - Ready for Implementation  
**Scope:** Large-scale feature (estimated 8-12 hours development)  
**Priority:** High - Core multiplayer feature

---

## EXECUTIVE SUMMARY

Iteration 7 adds **classroom multiplayer mode** where a teacher can host a live Bible Adventure game session with students divided into teams.

### Key Features

✅ Teacher authentication & dashboard  
✅ Session creation with unique codes  
✅ Player join without accounts (nickname only)  
✅ Automatic team assignment  
✅ Synchronized gameplay via polling  
✅ Team-based scoring  
✅ Results & learning summary

---

## ARCHITECTURE OVERVIEW

### User Types

**STAFF (Teacher/Admin)**

- Requires authentication (email + password)
- Can create and manage sessions
- Controls game flow

**PLAYER (Student)**

- No account needed
- Joins with session code + nickname
- Assigned to team automatically

### Flow Diagram

```
TEACHER                          PLAYER
  ↓                                ↓
Login                           Open /join
  ↓                                ↓
Create Session              Enter Code + Name
  ↓                                ↓
Generate Code (e.g. DAVID7)    Join Session
  ↓                                ↓
Lobby (waiting)              Lobby (waiting)
  ↓                                ↓
Start Game                    [Polling begins]
  ↓                                ↓
Display Question            Display Question
  ↓                                ↓
Wait for Answers            Team Discusses
  ↓                                ↓
Reveal Answer               See Feedback
  ↓                                ↓
Next Question               [Sync to next]
  ↓                                ↓
Complete Session            See Results
```

---

## DATABASE SCHEMA

### Tables Created

**`staff_users`**

- Authentication for teachers/admins
- Roles: admin, teacher

**`game_sessions`**

- Session metadata
- Status: draft, lobby, active, paused, completed
- Tracks current question

**`game_teams`**

- Teams per session
- Score tracking

**`game_players`**

- Players per session
- Token-based identity (no password)

**`team_answers`**

- One answer per team per question
- Prevents duplicate scoring

### Schema File

Created: `database/schema_iteration7.sql`

**Default Accounts:**

- Admin: admin@gkjtangerang.org / admin123
- Teacher: maria@gkjtangerang.org / teacher123

**⚠️ SECURITY: Change passwords in production!**

---

## API ENDPOINTS TO CREATE

### Authentication APIs

**`POST /api/auth/login.php`**

- Staff login (teacher/admin)
- Returns session token

**`POST /api/auth/logout.php`**

- Logout staff user

**`GET /api/auth/check.php`**

- Check current authentication status

### Teacher Session APIs

**`POST /api/teacher/session-create.php`**

- Create new game session
- Input: class_group, story_id, team_names[]
- Output: session_code, session_id

**`GET /api/teacher/sessions.php`**

- List teacher's sessions
- Filter by status

**`GET /api/teacher/session-detail.php?id={session_id}`**

- Get session details, teams, players

**`POST /api/teacher/session-start.php`**

- Change status: lobby → active

**`POST /api/teacher/session-next.php`**

- Move to next question
- Input: session_id

**`POST /api/teacher/session-reveal.php`**

- Reveal answer to players

**`POST /api/teacher/session-complete.php`**

- End session
- Calculate final scores

**`POST /api/teacher/team-move.php`**

- Move player between teams (lobby only)

### Player Session APIs

**`POST /api/player/join.php`**

- Join session with code + nickname
- Returns: player_token, team assignment

**`GET /api/player/session-state.php?code={code}&token={token}`**

- Get current session state
- Returns: status, current_question_index, question data

**`POST /api/player/answer.php`**

- Submit team answer
- Input: session_code, team_id, player_token, question_id, answer

**`GET /api/player/results.php?code={code}&token={token}`**

- Get final results

### Shared APIs

**`GET /api/session/state.php?code={code}`**

- Public session state (for polling)
- Returns: status, player_count, current_question_index

---

## FRONTEND PAGES TO CREATE

### Teacher Pages

**`/teacher/login.php`**

- Staff login form
- Redirect to dashboard after login

**`/teacher/dashboard.php`**

- List of sessions
- Create new session button
- Session history

**`/teacher/session-create.php`**

- Select class (small/medium/large)
- Select story (only active/verified)
- Configure teams (names, count)
- Create session

**`/teacher/lobby.php?id={session_id}`**

- Display session code prominently
- Show teams and players
- Real-time player join updates (polling)
- Team management (move players)
- Start game button

**`/teacher/game.php?id={session_id}`**

- Current question display
- Team answer status (who answered)
- Reveal answer button
- Next question button
- Pause/End session

**`/teacher/results.php?id={session_id}`**

- Final scores by team
- Learning summary
- Option to replay

**`/teacher/present.php?code={code}`**

- Projector/TV view
- Large text
- Minimal controls
- Current question/scores

### Player Pages

**`/join.php`** or `/player/join.php`

- Input session code
- Input nickname
- Join button

**`/player/lobby.php`**

- Show: nickname, team name
- Wait for game start message
- No controls needed

**`/player/game.php`**

- Display current question
- Show answer options
- Team discussion prompt
- Lock answer button

**`/player/results.php`**

- Team score
- Learning summary
- Encouraging message

---

## JAVASCRIPT MODULES TO CREATE

### Teacher Module (`/assets/js/teacher.js`)

```javascript
const TeacherSession = {
  async createSession(classGroup, storyId, teams) {},
  async startSession(sessionId) {},
  async nextQuestion(sessionId) {},
  async revealAnswer(sessionId) {},
  async completeSession(sessionId) {},
  pollSessionState(sessionId, callback) {},
  stopPolling() {},
};
```

### Player Module (`/assets/js/player.js`)

```javascript
const PlayerSession = {
  async join(sessionCode, nickname) {},
  async submitAnswer(answer) {},
  pollGameState(callback) {},
  stopPolling() {},
};
```

### Session Polling (`/assets/js/session-polling.js`)

```javascript
const SessionPoller = {
  start(endpoint, interval, callback) {},
  stop() {},
  isRunning() {},
};
```

---

## IMPLEMENTATION PHASES

### Phase 1: Database & Core Auth (2-3 hours)

1. ✅ Create schema file
2. Run schema on database
3. Create `config/auth.php` helper functions
4. Create `/api/auth/login.php`
5. Create `/api/auth/check.php`
6. Create `/api/auth/logout.php`
7. Test authentication flow

### Phase 2: Session Management (2-3 hours)

1. Create session code generator utility
2. Create `/api/teacher/session-create.php`
3. Create `/api/teacher/sessions.php`
4. Create `/api/teacher/session-detail.php`
5. Create `/api/session/state.php` (public polling endpoint)
6. Test session creation and retrieval

### Phase 3: Teacher UI (2 hours)

1. Create `/teacher/login.php`
2. Create `/teacher/dashboard.php`
3. Create `/teacher/session-create.php`
4. Create `/teacher/lobby.php`
5. Create `/assets/js/teacher.js`
6. Test teacher flow: login → create → lobby

### Phase 4: Player Join (1-2 hours)

1. Create `/api/player/join.php`
2. Create `/player/join.php` UI
3. Create `/player/lobby.php` UI
4. Create `/assets/js/player.js`
5. Test: player join → auto team assignment → lobby

### Phase 5: Game Session (2-3 hours)

1. Create `/api/teacher/session-start.php`
2. Create `/api/teacher/session-next.php`
3. Create `/api/player/answer.php`
4. Create `/teacher/game.php` UI
5. Create `/player/game.php` UI
6. Create `/assets/js/session-polling.js`
7. Implement polling on both teacher and player sides
8. Test: start → question → answer → next

### Phase 6: Results & Completion (1 hour)

1. Create `/api/teacher/session-complete.php`
2. Create `/api/player/results.php`
3. Create `/teacher/results.php` UI
4. Create `/player/results.php` UI
5. Test: complete session → show scores

### Phase 7: Polish & Edge Cases (1-2 hours)

1. Add reveal answer API + UI
2. Add team move functionality
3. Add presentation mode (`/teacher/present.php`)
4. Handle refresh scenarios
5. Add error messages
6. Add loading states
7. Test edge cases

### Phase 8: Testing & Documentation (1 hour)

1. End-to-end testing
2. Update README
3. Create ITERATION_7_REPORT
4. Git commit

---

## KEY DESIGN DECISIONS

### 1. Polling Instead of WebSockets

**Reason:** Keep stack simple (PHP + Fetch API)

**Implementation:**

- Poll every 2-3 seconds
- Stop polling when session complete
- Prevent request overlap

### 2. One Answer Per Team

**Reason:** Encourages discussion

**Implementation:**

- Database constraint: unique (session_id, team_id, question_id)
- First valid answer locks team response
- Display "Jawaban sudah dikirim" after lock

### 3. Teacher-Controlled Flow

**Reason:** Ensures learning focus

**Implementation:**

- Teacher manually clicks "Next Question"
- Teacher decides when to reveal answers
- Players always sync to teacher's current state

### 4. Token-Based Player Identity

**Reason:** No accounts needed for children

**Implementation:**

- Generate random token on join
- Store in localStorage
- Allows reconnect after refresh
- Expires when session ends

### 5. Session Codes

**Format:** 6 characters, alphanumeric, uppercase  
**Examples:** `DAVID7`, `MUSA42`, `ABC123`

**Generation:**

```php
function generateSessionCode() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}
```

**Validation:**

- Must check uniqueness for active sessions
- Retry generation if collision

### 6. Auto Team Assignment

**Algorithm:**

```php
// Assign to team with fewest members
$teams = getTeamsBySession($sessionId);
$smallestTeam = array_reduce($teams, function($min, $team) {
    return ($team['player_count'] < $min['player_count']) ? $team : $min;
});
```

### 7. Scoring System (MVP)

**Simple:**

- Correct answer: 10 points
- Wrong answer: 0 points
- No speed bonus
- No penalties

**Future enhancements:**

- Teamwork bonus (teacher awarded)
- Consecutive correct streak
- Time-based bonus

---

## SECURITY CONSIDERATIONS

### Staff Authentication

✅ Password hashing (bcrypt)  
✅ Session-based auth  
✅ CSRF tokens on mutations  
✅ Role-based access control

### Player Security

✅ Token validation on every action  
✅ No sensitive data exposure  
✅ Session code validation  
✅ Nickname sanitization  
✅ Rate limiting (prevent spam joins)

### API Security

✅ Verify teacher owns session  
✅ Verify player belongs to session  
✅ Validate question_id exists  
✅ Prevent answer modification after submission  
✅ Escape all output (XSS prevention)

---

## STATE MANAGEMENT

### Session States

```
draft → lobby → active → completed
              ↓
           paused (optional)
```

**Rules:**

- `draft`: Created but not ready
- `lobby`: Waiting for players, accepting joins
- `active`: Game in progress, no new joins
- `paused`: Teacher paused (optional for MVP)
- `completed`: Game ended, show results

### Status Transitions

```php
// Only teacher can trigger
lobby → active   (Start Game)
active → paused  (Pause - optional)
paused → active  (Resume - optional)
active → completed (End Session)
```

### Question State

Session tracks: `current_question_index`

**Flow:**

1. Session starts: index = 0
2. Teacher clicks "Next": index++
3. Players poll and sync to current index
4. When index >= total_questions: game complete

---

## POLLING ARCHITECTURE

### Teacher Polling

**Endpoint:** `/api/teacher/session-detail.php?id={id}`  
**Interval:** 2000ms (2 seconds)  
**Data:**

- Player count
- Team player counts
- Answer submission status

**Stop Conditions:**

- Session completed
- Teacher leaves page
- Error occurred

### Player Polling

**Endpoint:** `/api/player/session-state.php?code={code}&token={token}`  
**Interval:** 2500ms (2.5 seconds)  
**Data:**

- Session status
- Current question index
- Current question data (if active)
- Results (if completed)

**Stop Conditions:**

- Session completed
- Player leaves page
- Token invalid

### Implementation Pattern

```javascript
let pollingInterval = null;

function startPolling(endpoint, callback, interval = 2500) {
  if (pollingInterval) return; // Already polling

  const poll = async () => {
    try {
      const response = await fetch(endpoint);
      const data = await response.json();
      callback(data);
    } catch (error) {
      console.error("Polling error:", error);
    }
  };

  poll(); // Initial call
  pollingInterval = setInterval(poll, interval);
}

function stopPolling() {
  if (pollingInterval) {
    clearInterval(pollingInterval);
    pollingInterval = null;
  }
}

// Stop on page unload
window.addEventListener("beforeunload", stopPolling);
```

---

## UI/UX GUIDELINES

### Teacher Interface

**Priorities:**

1. Clear control buttons
2. Real-time status updates
3. Minimal clutter
4. Professional appearance

**Key Elements:**

- Large session code display
- Player/team counts
- Answer status indicators
- Prominent action buttons

### Player Interface

**Priorities:**

1. Large, touch-friendly buttons
2. Clear team identity
3. Minimal distractions
4. Encouraging messages

**Key Elements:**

- Team name/color badge
- Question with large text
- Simple answer options
- "Discuss with team" prompt

### Presentation Mode

**Priorities:**

1. Maximum readability from distance
2. High contrast
3. Minimal UI chrome
4. Auto-updating

**Key Elements:**

- Very large question text (3-4rem)
- Prominent answer options
- Score display
- Current status

---

## ERROR HANDLING

### Common Errors

**Invalid Session Code**

```
"Game tidak ditemukan. Periksa kembali kode dari guru."
```

**Session Already Started**

```
"Game sudah dimulai. Silakan hubungi guru."
```

**Duplicate Nickname**

```
"Nama ini sudah digunakan. Pilih nama lain."
```

**Team Already Answered**

```
"Tim sudah menjawab pertanyaan ini."
```

**Token Invalid**

```
"Sesi tidak valid. Silakan bergabung kembali."
```

### Error Response Format

```json
{
  "success": false,
  "error": {
    "code": "INVALID_SESSION",
    "message": "Game tidak ditemukan."
  }
}
```

---

## TESTING CHECKLIST

### Authentication

- [ ] Teacher can login with correct credentials
- [ ] Teacher cannot login with wrong password
- [ ] Admin can access all features
- [ ] Teacher has appropriate permissions
- [ ] Logout works correctly

### Session Creation

- [ ] Teacher can create session
- [ ] Session code is unique
- [ ] Teams are created automatically
- [ ] Session appears in teacher's list

### Player Join

- [ ] Player can join with valid code
- [ ] Player cannot join with invalid code
- [ ] Player cannot join started session
- [ ] Duplicate nicknames are prevented
- [ ] Players are auto-assigned to teams
- [ ] Teams are balanced

### Lobby

- [ ] Teacher sees players join in real-time
- [ ] Player count updates
- [ ] Player can see their team assignment
- [ ] Teacher can move players between teams
- [ ] Refresh preserves state

### Game Flow

- [ ] Start button transitions to active
- [ ] All players see first question
- [ ] Team can submit answer
- [ ] Duplicate submissions prevented
- [ ] Teacher sees answer status
- [ ] Next question works
- [ ] All players sync to new question

### Scoring

- [ ] Correct answer awards points
- [ ] Wrong answer awards 0 points
- [ ] Score calculation is correct
- [ ] Scores persist after refresh

### Completion

- [ ] Teacher can end session
- [ ] Results show correct scores
- [ ] Learning summary displays
- [ ] Players see results

### Edge Cases

- [ ] Page refresh during lobby
- [ ] Page refresh during game
- [ ] Player closes browser
- [ ] Teacher closes browser
- [ ] Network interruption
- [ ] Concurrent answer submissions
- [ ] Late joiners (should be blocked)

---

## FILES TO CREATE

### Database

- [x] `database/schema_iteration7.sql`

### PHP APIs (18 files)

- [ ] `public/api/auth/login.php`
- [ ] `public/api/auth/logout.php`
- [ ] `public/api/auth/check.php`
- [ ] `public/api/teacher/session-create.php`
- [ ] `public/api/teacher/sessions.php`
- [ ] `public/api/teacher/session-detail.php`
- [ ] `public/api/teacher/session-start.php`
- [ ] `public/api/teacher/session-next.php`
- [ ] `public/api/teacher/session-reveal.php`
- [ ] `public/api/teacher/session-complete.php`
- [ ] `public/api/teacher/team-move.php`
- [ ] `public/api/player/join.php`
- [ ] `public/api/player/session-state.php`
- [ ] `public/api/player/answer.php`
- [ ] `public/api/player/results.php`
- [ ] `public/api/session/state.php`
- [ ] `config/auth.php` (helper functions)
- [ ] `config/session-utils.php` (utilities)

### PHP Pages (11 files)

- [ ] `public/teacher/login.php`
- [ ] `public/teacher/dashboard.php`
- [ ] `public/teacher/session-create.php`
- [ ] `public/teacher/lobby.php`
- [ ] `public/teacher/game.php`
- [ ] `public/teacher/results.php`
- [ ] `public/teacher/present.php`
- [ ] `public/player/join.php`
- [ ] `public/player/lobby.php`
- [ ] `public/player/game.php`
- [ ] `public/player/results.php`

### JavaScript (3 files)

- [ ] `public/assets/js/teacher.js`
- [ ] `public/assets/js/player.js`
- [ ] `public/assets/js/session-polling.js`

### CSS (1 file)

- [ ] `public/assets/css/session.css`

### Documentation

- [x] `ITERATION_7_IMPLEMENTATION_PLAN.md` (this file)
- [ ] `ITERATION_7_REPORT.md` (after completion)
- [ ] Update `README.md`

**Total:** ~33 new files

---

## ESTIMATED EFFORT

| Phase                 | Hours | Complexity |
| --------------------- | ----- | ---------- |
| Phase 1: Auth         | 2-3   | Medium     |
| Phase 2: Session      | 2-3   | Medium     |
| Phase 3: Teacher UI   | 2     | Medium     |
| Phase 4: Player Join  | 1-2   | Low        |
| Phase 5: Game Session | 2-3   | High       |
| Phase 6: Results      | 1     | Low        |
| Phase 7: Polish       | 1-2   | Medium     |
| Phase 8: Testing      | 1     | Low        |

**Total: 12-17 hours**

**Recommended approach:** Break into multiple sub-iterations

---

## NEXT STEPS

### Option A: Implement in One Go

- Requires 12-17 hours focused development
- High risk of bugs
- Need extensive testing

### Option B: Sub-Iterations (Recommended)

1. **Iteration 7A:** Auth + Session Management (4-5 hours)
2. **Iteration 7B:** Teacher Flow (3-4 hours)
3. **Iteration 7C:** Player Flow (3-4 hours)
4. **Iteration 7D:** Polish + Testing (2-3 hours)

### Option C: MVP First

1. Build simplest working version
2. Test with real users
3. Iterate based on feedback

---

## SUCCESS CRITERIA

Iteration 7 is complete when:

✅ Teacher can login  
✅ Teacher can create session with code  
✅ Players can join with code + nickname  
✅ Players auto-assigned to teams  
✅ Game starts and questions display  
✅ Teams can submit answers  
✅ Scoring works correctly  
✅ Results display properly  
✅ Refresh doesn't break flow  
✅ Solo mode still works  
✅ No console errors  
✅ Documentation complete

---

## NOTES

- This is the largest iteration so far
- Consider breaking into sub-iterations
- Prioritize core flow over polish
- Test each phase before proceeding
- Real-time sync via polling (not WebSocket)
- Keep existing solo mode intact
- Security is critical (auth + validation)

---

**Status:** Ready to begin implementation  
**Estimated Timeline:** 2-3 development sessions  
**Risk Level:** Medium-High (large scope)  
**Dependencies:** Database, auth system, polling architecture

**Recommendation:** Start with Phase 1 (Auth + Session Management) and verify before proceeding to UI.
