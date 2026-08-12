# Backup & Recovery

## Backup Scope
- stories
- story contents
- questions
- players
- progress
- sessions
- analytics
- staff users

## Backup Strategy
- Perform regular MySQL backups.
- Keep recent backups daily and older backups weekly if storage allows.
- Store backups outside `public/`.

## Backup Naming
Use a timestamped format such as:

`bible_adventure_YYYY-MM-DD_HHMM.sql`

## Restore Procedure
1. Create a clean test database.
2. Restore the backup into the test database.
3. Run the application against the restored data.
4. Verify core flows and key records.

## Recovery Principles
- Backups are only valid if restore has been tested.
- Prefer additive changes in production.
- Keep a rollback plan for any destructive change.

## Operational Notes
- Do not store database secrets inside version-controlled backup scripts.
- Do not expose backup files in browser-accessible directories.
