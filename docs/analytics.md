# Analytics & Learning Insights

## Goals
- Help teachers and admins improve Bible Adventure content
- Highlight stories, questions, and mechanics that need review
- Keep data minimal and privacy-first

## Events
- story_started
- story_completed
- question_viewed
- answer_submitted
- question_completed
- session_started
- session_completed
- player_joined_session
- technical_error

## Metrics
- Stories played
- Story completion rate
- Question correct rate
- Mechanic attempts and correct rate
- Recent classroom sessions
- Questions needing review

## Definitions
- **Completion rate** = completed stories / started stories
- **Correct rate** = correct answers / total answered questions
- **Low sample size** = insufficient attempts to judge performance reliably

## Privacy
- Aggregate where possible
- Limit player identity exposure
- No PINs, tokens, or secrets in exports
- Staff-only access

## Known Limitations
- Prototype analytics are best-effort
- No data warehouse or complex ETL
- Retention policy still needs formal production decision
- Teacher-scoped filtering is intentionally simple
