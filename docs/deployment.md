# Deployment Guide

## Supported Environments
- Apache + PHP
- Nginx + PHP-FPM
- MySQL 8

## Requirements
- PHP 8.3
- PDO MySQL extension
- mbstring
- json
- session

## Environment Setup
1. Copy `.env.example` to `.env`.
2. Set production values.
3. Keep `.env` outside the public directory.
4. Disable debug in production.

## Document Root
- Prefer `public/` as the document root.
- Do not expose config, logs, or backups to browser access.

## Migration
- Apply SQL migrations before opening the app to users.
- Keep migrations additive when possible.
- Record the applied schema version manually if no migration runner exists.

## Admin Initialization
- Create the first admin account using a secure one-time script or controlled database insert.
- Never commit default credentials.

## Deployment Flow
1. Backup current production database.
2. Upload or pull new code.
3. Update environment config.
4. Run migration.
5. Verify assets and login flow.
6. Smoke test gameplay and staff pages.

## Post-Deploy Smoke Test
- Health endpoint: `public/health.php`
- Home page
- Player login
- Teacher login
- Create session
- Join session
- Answer flow
- Analytics dashboard

## Health Check
- Use `public/health.php` for a quick service check.
- The endpoint returns a minimal JSON response without exposing secrets.
- If database connectivity is degraded, the endpoint should still respond with a safe status payload.

## Rollback
- Restore previous code release.
- Restore database backup if migration caused breakage.
- Re-run smoke test after rollback.
