# Stories Release List — Bible Adventure v1

> Daftar story yang masuk rilis v1 berdasarkan data schema dan dokumen release.
> 
> Status mengikuti evidence yang tersedia saat ini pada 20 Agustus 2026.

---

## Release Stories

| Story | Slug | Era | Reference | Order | Previous | Next | Classes Supported | Status | Notes |
|-------|------|-----|-----------|-------|----------|------|-------------------|--------|-------|
| Penciptaan | creation | Beginning | Kej 1-2 | 1 | — | noah | Kecil, Madya, Besar | verified | Konten paling lengkap |
| Nuh | noah | Beginning | Kej 6-9 | 2 | creation | abraham | Kecil | needs_review | Medium/large butuh verifikasi |
| Abraham | abraham | Patriarchs | Kej 12-25 | 3 | noah | yusuf | Pending | needs_review | Butuh kelengkapan content per kelas |
| Yusuf | yusuf | Patriarchs | Kej 37-50 | 4 | abraham | musa | Pending | needs_review | Butuh kelengkapan content per kelas |
| Musa | musa | Exodus | Kel 1-15 | 5 | yusuf | — | Pending | needs_review | Butuh kelengkapan content per kelas |

---

## Release Rules

- Hanya story berstatus `verified` dan `active` yang boleh dipakai tanpa catatan.
- Story berstatus `needs_review` boleh dicatat sebagai kandidat release, tetapi tidak boleh dianggap final tanpa verifikasi.
- Story draft atau inactive tidak masuk daftar release.

---

## Progress Note

- Generated / updated: 20 Agustus 2026
- Source references: `database/schema_stories_questions.sql`, `docs/content-release.md`, `docs/v1-release.md`
- Follow-up: isi jumlah pertanyaan per story setelah audit database lanjutan
