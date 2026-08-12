# Pilot Checklist

> Daftar cek untuk tiga fase pilot sesuai `prompt/13_prompt.txt`
> sections #115 (Before), #116 (During), #117 (After).
>
> Gunakan sebagai one-page reference saat menjalankan sesi pilot.
>
> Prinsip: **OBSERVE → MEASURE → LEARN → DOCUMENT**

---

## BEFORE Pilot

Centang setiap item sebelum sesi pilot dimulai.

```text
[ ] Verified story ready
[ ] Questions reviewed
[ ] Teacher account ready
[ ] Player/team devices ready
[ ] Network tested
[ ] Projector tested
[ ] Audio tested
[ ] Database backup available
[ ] Application build frozen (pilot version dicatat, mis. v0.9.0)
```

### Build & Change Freeze

> Sebelum classroom pilot, buat temporary change freeze.
> Jangan deploy perubahan tepat sebelum kelas kecuali critical fix.
> Catat versi build di observation sheet.

### Database Backup

> Backup tersedia sebelum pilot nyata.
> Lihat [`backup-recovery.md`](./backup-recovery.md) untuk prosedur.

---

## DURING Pilot

```text
[ ] Observe teacher setup
[ ] Record join issues
[ ] Record mechanic confusion
[ ] Observe team collaboration
[ ] Track technical problems
[ ] Record session duration
[ ] Perform learning check
```

### Observer Rules (Section #52)

Observer **JANGAN**:

```text
- mengajari terus
- menjawab untuk anak
- mengubah UI saat pilot
- menginterupsi guru
```

> **Catat dahulu.** Jangan fix-on-the-fly kecuali blocker.

---

## AFTER Pilot

```text
[ ] Teacher feedback collected
[ ] Child feedback collected
[ ] Analytics reviewed
[ ] Bible content reviewed
[ ] Issues categorized (CRITICAL / HIGH / MEDIUM / LOW)
[ ] Pilot summary written
[ ] Recommendations prioritized (MUST / SHOULD / COULD / NOT NOW)
```

### Privacy

```text
- Gunakan label "Player A", "Team Bintang" untuk laporan umum.
- Jangan merekam foto/video/audio anak kecuali ada proses izin.
- Catatan observasi tertulis sudah cukup untuk prototype.
```

---

## Issue Severity (Sections #55–#58)

```text
CRITICAL  game tidak dapat dimulai, progress hilang,
          player tidak dapat join, teacher tidak dapat
          mengontrol session, security issue

HIGH      banyak anak tidak memahami satu mechanic,
          guru sering membutuhkan bantuan, session sync
          sering gagal, question instruction membingungkan

MEDIUM    button kurang jelas, setup sedikit lambat,
          feedback sering dilewati, spacing bermasalah

LOW       minor animation, visual detail, copy kecil,
          cosmetic alignment
```

## Issue Frequency (Section #60)

```text
ONCE              terjadi satu kali
OCCASIONAL        kadang-kadang
FREQUENT          sering terjadi
ALMOST ALWAYS     hampir setiap kali
```

> Issue kecil tapi sering **lebih penting** dari issue besar yang hanya sekali.

---

## Definition of Done — Iteration 13 (Section #118)

Iterasi 13 dianggap selesai jika:

```text
[ ] Teacher internal test dilakukan
[ ] Small group test dilakukan
[ ] Real classroom test dilakukan atau siap berdasarkan kondisi nyata
[ ] Teacher usability dicatat
[ ] Child usability dicatat
[ ] Team collaboration diamati
[ ] Learning check dilakukan
[ ] Game duration dicatat
[ ] Technical stability dicatat
[ ] Analytics dibandingkan dengan observasi
[ ] Bible content review dilakukan
[ ] Issue log dibuat (pilot-issues.md)
[ ] Issues diprioritaskan
[ ] Pilot summary tersedia (pilot-summary.md)
```

> **Jika real classroom belum memungkinkan:**
> selesaikan fase yang tersedia dan dokumentasikan sebagai
> `Pending Real Classroom Validation`.
> **Jangan membuat hasil fiktif.**

---

## Lihat Juga

- [`pilot-index.md`](./pilot-index.md) — peta dan urutan baca semua dokumen pilot
- [`pilot-observation.md`](./pilot-observation.md) — template observasi per sesi
- [`pilot-issues.md`](./pilot-issues.md) — issue log terpusat
- [`pilot-summary.md`](./pilot-summary.md) — laporan rangkuman
- [`pilot-summary-example.md`](./pilot-summary-example.md) — contoh format rangkuman
- [`analytics.md`](./analytics.md) — cara membaca analytics dashboard
- [`deployment.md`](./deployment.md) — cara deploy ke server pilot
- [`backup-recovery.md`](./backup-recovery.md) — backup sebelum pilot
- `prompt/13_prompt.txt` — source prompt untuk iterasi ini
