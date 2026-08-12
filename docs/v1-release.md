# Bible Adventure v1.0 Release Scope

> Dokumen final untuk iterasi 15.
> Tujuan: menentukan scope v1, fitur di luar scope, dan status rilis.

---

## Version

```text
Release        : Bible Adventure v1.0.0
Status         : READY WITH MINOR NON-BLOCKING ISSUES
Release Build  : v1.0.0
Release Date   : YYYY-MM-DD
```

---

## V1 Scope

Yang termasuk dalam rilis v1:

```text
Bible Map
Kelas Kecil
Kelas Madya
Kelas Besar

Solo Mode
Classroom Mode
Team Mode

Guest Player
Registered Player

Teacher
Admin

Story Content
Questions
Game Mechanics

Player Progress
Teacher Session

Content Management
Analytics

Backup
Security
Deployment
```

### Yang tersedia saat ini

```text
- Bible Map
- Kelas Kecil / Madya / Besar (dukungan dasar)
- Solo Mode
- Classroom Mode
- Team Mode
- Guest Player
- Registered Player
- Teacher login
- Admin login
- Story content (yang berstatus active + verified)
- Player progress (session-based dan registered)
- Teacher session control
- Content management sederhana
- Analytics dashboard
- Backup procedure (lihat backup-recovery.md)
- Security baseline (lihat security.md)
- Deployment (lihat deployment.md)
```

---

## V1 Non-Scope

Yang sengaja **belum** ada di v1:

```text
- public leaderboard
- chat
- friend system
- parent account
- advanced avatar
- currency
- shop
- AI content generation
- offline-first PWA
- WebSocket realtime
- mobile native app
```

> Non-scope membantu mencegah ekspektasi yang tidak realistis
> dari guru, admin, dan orang tua.

---

## Content Release Status

```text
Verified Stories : [lihat stories-release-list.md]
Needs Review     : [lihat stories-release-list.md]
Draft            : tidak masuk release
Inactive         : tidak masuk release
```

> Hanya konten berstatus `active` dan `verified` yang dimasukkan.

---

## Release Blocker Check

| Area | Status | Catatan |
|------|--------|---------|
| Security | OK | lihat security.md |
| Bible Content | OK | lihat content-release.md |
| Authentication | OK | teacher & admin login tersedia |
| Player Progress | OK | session-based + registered |
| Classroom Session | OK | polling-based |
| Database | OK | schema digabung |
| Backup | OK | prosedur terdokumentasi |
| Deployment | OK | health endpoint tersedia |

---

## Versioning Rules

```text
PATCH : bug fix, security fix kecil, copy fix, koreksi konten minor
MINOR : fitur baru yang backward compatible, story baru, mechanic baru
MAJOR : perubahan arsitektur, breaking change, redesign besar
```

---

## Definition of Done — Iteration 15

Iterasi 15 dianggap selesai jika:

```text
[ ] V1 scope final terdokumentasi
[ ] V1 non-scope terdokumentasi
[ ] Tidak ada P0 terbuka
[ ] Tidak ada content wrong/draft yang lolos release
[ ] Documentation final tersedia
[ ] Teacher guide tersedia
[ ] Admin guide tersedia
[ ] Developer guide tersedia
[ ] Operations / runbook tersedia
[ ] v2-backlog tersedia
```

---

## Lihat Juga

- [`teacher-guide.md`](./teacher-guide.md)
- [`admin-guide.md`](./admin-guide.md)
- [`developer-guide.md`](./developer-guide.md)
- [`troubleshooting.md`](./troubleshooting.md)
- [`v2-backlog.md`](./v2-backlog.md)
- [`security.md`](./security.md)
- [`operations.md`](./operations.md)
