# Production Readiness Checklist

## Security
- [ ] `.env` is not committed
- [ ] Debug is disabled in production
- [ ] Sensitive errors are not shown to users
- [ ] Staff-only pages are protected by role checks
- [ ] Public APIs validate input and methods
- [ ] No hardcoded secrets in source

## Configuration
- [ ] `APP_ENV` is set correctly
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_URL` matches the deployed site
- [ ] Database credentials are set in environment config
- [ ] Timezone is documented and consistent

## Backup
- [ ] Database backup procedure exists
- [ ] Backup location is outside `public/`
- [ ] Restore has been tested
- [ ] Rollback plan is documented

## Deployment
- [ ] Migration steps are documented
- [ ] Production document root points to `public/`
- [ ] HTTPS is enabled
- [ ] Assets load without mixed content
- [ ] Post-deployment smoke test is defined

## Logging
- [ ] Application logs are written outside web root
- [ ] Secrets are not written to logs
- [ ] Log rotation is handled by the server or hosting platform

## Verification
- [ ] Health endpoint (`public/health.php`) returns a safe JSON response
- [ ] Home page loads
- [ ] Teacher login works
- [ ] Player flow works
- [ ] Analytics dashboard loads
- [ ] No PHP notices or stack traces are exposed
