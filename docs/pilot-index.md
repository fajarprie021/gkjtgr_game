# Pilot Docs Index

> Daftar dan urutan baca dokumen pilot Bible Adventure.
> Gunakan ini sebagai peta saat menjalankan, mencatat, atau menutup
> fase pilot Iteration 13.

---

## Urutan Pakai

```text
1. pilot-checklist.md      → cek sebelum / selama / setelah sesi
2. pilot-observation.md    → catat data lapangan per sesi
3. pilot-issues.md         → log issue lintas sesi dengan ID unik
4. pilot-summary.md        → rangkuman akhir untuk input Iterasi 14
5. pilot-summary-example.md→ contoh format rangkuman akhir
```

---

## Daftar File

| File | Tujuan |
|------|--------|
| [`pilot-checklist.md`](./pilot-checklist.md) | Before / During / After checklist, severity & frequency, Definition of Done Iteration 13 |
| [`pilot-observation.md`](./pilot-observation.md) | Template observasi per sesi (metadata, setup, gameplay, learning, technical) |
| [`pilot-issues.md`](./pilot-issues.md) | Log issue terpusat dengan ID dan status |
| [`pilot-summary.md`](./pilot-summary.md) | Template rangkuman akhir pilot |
| [`pilot-summary-example.md`](./pilot-summary-example.md) | Contoh pengisian rangkuman akhir |
| [`post-pilot-fix-plan.md`](./post-pilot-fix-plan.md) | Rencana perbaikan pasca-pilot (Iteration 14) |
| [`post-pilot-validation.md`](./post-pilot-validation.md) | Laporan validasi pasca-pilot |

---

## Referensi Pendukung

| Topik | Lokasi |
|-------|--------|
| Cara deploy ke server pilot | [`deployment.md`](./deployment.md) |
| Backup sebelum pilot | [`backup-recovery.md`](./backup-recovery.md) |
| Cara baca analytics dashboard | [`analytics.md`](./analytics.md) |
| Cek kesiapan production | [`production-readiness.md`](./production-readiness.md) |
| Health endpoint | `public/health.php` |
| Catatan keamanan | [`security.md`](./security.md) |
| Runbook operasional | [`operations.md`](./operations.md) |
| Scope dan rilis v1 | [`v1-release.md`](./v1-release.md) |
| Konten rilis v1 | [`content-release.md`](./content-release.md) |
| Mechanic rilis v1 | [`mechanics-release.md`](./mechanics-release.md) |
| Panduan guru | [`teacher-guide.md`](./teacher-guide.md) |
| Panduan admin | [`admin-guide.md`](./admin-guide.md) |
| Panduan developer | [`developer-guide.md`](./developer-guide.md) |
| Troubleshooting | [`troubleshooting.md`](./troubleshooting.md) |
| Backlog v2 | [`v2-backlog.md`](./v2-backlog.md) |
| Catatan perubahan | [`../CHANGELOG.md`](../CHANGELOG.md) |

---

## Aturan Konsistensi Antar File

- **ID issue** ditulis konsisten sebagai `ID-NNN` di `pilot-issues.md` dan dirujuk ulang di `pilot-summary.md`.
- **Severity** hanya memakai: `CRITICAL`, `HIGH`, `MEDIUM`, `LOW`.
- **Frequency** hanya memakai: `ONCE`, `OCCASIONAL`, `FREQUENT`, `ALMOST ALWAYS`.
- **Status issue** hanya memakai: `OPEN`, `TRIAGED`, `DEFERRED`, `RESOLVED`.
- **Rekomendasi** dikelompokkan ke: `MUST FIX`, `SHOULD FIX`, `COULD FIX`, `NOT NOW`.
- **Phase** ditulis sebagai: `A` (Internal Teacher), `B` (Small Group), `C` (Real Classroom).
- Jika Phase C belum memungkinkan, tandai `Pending Real Classroom Validation` di summary.

---

## Sumber Prompt

- `prompt/13_prompt.txt` — pilot testing & classroom validation
- `prompt/14_prompt.txt` — input dari pilot dipakai untuk Iterasi 14
