# Pilot Summary Report Example

> Contoh pengisian `pilot-summary.md` untuk membantu tim saat pilot nyata.
> 
> Catatan: ini **contoh format**, bukan hasil observasi nyata.
> Jangan copy-paste angka tanpa data lapangan.

---

## 1. Pilot Overview

```text
Build Version       : v0.9.0
Period              : 2026-08-11 — 2026-08-18
Phase A completed   : done
Phase B completed   : in progress
Phase C completed   : Pending Real Classroom Validation
Number of Sessions  : 3
Total Players Seen  : 18
Total Teams Seen    : 6
Classes Tested      : Kecil, Madya
Stories Tested      : creation, noah
Mechanics Tested    : multiple_choice, true_false
```

### Pilot Status

| Phase | Status |
|-------|--------|
| A — Internal Teacher Test | Done |
| B — Small Group Pilot | In Progress |
| C — Real Classroom Pilot | Pending |

---

## 2. Participants

```text
Teachers         : 2 (kelas Kecil, Madya)
Coordinators     : 1
Developers (obs) : 1 (AB)
Classes Piloted  : Kecil (n=8), Madya (n=10), Besar (n=0)
```

---

## 3. Stories Tested

| Story | Class | Mechanics | Notes |
|-------|-------|-----------|-------|
| creation | Kecil | multiple_choice | Intro mudah dipahami |
| noah | Madya | multiple_choice, true_false | Butuh penjelasan tambahan di soal 2 |

---

## 4. Game Mechanics Tested

| Mechanic | Sesi yang Pakai | Catatan |
|----------|-----------------|---------|
| multiple_choice | 3 sesi | Paling stabil |
| true_false | 1 sesi | Anak cepat paham |
| sequence | 0 sesi | Belum diuji |
| matching | 0 sesi | Belum diuji |
| timeline | 0 sesi | Belum diuji |
| verse_puzzle | 0 sesi | Belum diuji |

---

## 5. What Worked

```text
- Anak-anak cepat memahami tombol jawaban.
- Guru bisa menjalankan sesi tanpa bantuan developer setelah briefing singkat.
- Mode tim memancing diskusi antaranak.
```

### Success Signal — Usability

```text
- anak dapat join tanpa bantuan
- guru tahu control utama
- map dapat dipahami
```

### Success Signal — Engagement

```text
- anak berdiskusi
- anak mengikuti story
- anak ingin melanjutkan
```

### Success Signal — Learning

```text
- anak dapat menceritakan kembali inti cerita
- anak memahami urutan peristiwa sederhana
- anak menjawab learning check
```

### Success Signal — Technical

```text
- tidak ada session crash
- refresh dapat pulih
- answer tersimpan
- teacher controls sinkron
```

---

## 6. What Did Not Work

```text
- Setup sesi masih memakan waktu terlalu lama pada percobaan pertama.
- Beberapa anak masih bingung membedakan instruksi soal dan opsi jawaban.
- Audio tidak selalu terdengar jelas di ruangan besar.
```

### Failure Signal

```text
- guru perlu bantuan saat create session pertama
- anak menunggu terlalu lama sebelum game mulai
- beberapa perangkat lambat saat reconnect
```

---

## 7. Learning Findings

```text
- Learning check      : cukup / perlu perbaikan
- Story recall        : sebagian besar anak bisa menyebut inti cerita
- Value understanding : anak menangkap pesan sederhana
- Wrong concept       : tidak ada / ada pada bagian ...
```

---

## 8. Classroom Fit

```text
- Session length    : sesuai / terlalu panjang
- Teacher workload   : ringan / sedang / berat
- Group management   : mudah / perlu bantuan
- Device friction    : rendah / sedang / tinggi
- Noise level        : terkontrol / cukup ramai / mengganggu
```

---

## 9. Child Feedback Summary

```text
- Bagian paling seru  : menjawab soal bersama tim
- Bagian sulit        : soal yang terlalu panjang
- Pemahaman UI        : cukup jelas
- Preferensi tim      : senang main bareng
- Cerita yang diingat : creation, Noah
- Mau main lagi?      : ya
```

---

## 10. Team Collaboration Findings

```text
- Collaboration level rata-rata : MEDIUM
- Diskusi tim        : sering
- Dominasi satu anak : kadang pernah
- Keputusan bersama  : kadang
```

---

## 11. Technical Findings

```text
- API stability    : cukup stabil
- Polling latency  : kadang lambat saat jaringan turun
- Reconnect        : bekerja
- Double submit    : tidak terlihat
- Projector legibility : oke
- Audio sync       : oke
- Browser/device compat : Chrome Android dan Safari iPhone berjalan baik pada percobaan ini
```

### Analytics vs Real Usage

```text
- Analytics mencatat 18 join
- Observer melihat 18 join
- Selisih: 0
```

---

## 12. Bible Content Review

| Story | Story Facts | References | Questions | Feedback | Timeline |
|-------|-------------|------------|-----------|----------|----------|
| creation | OK | checked | OK | OK | OK |
| noah | needs_review | checked | needs_review | OK | OK |

---

## 13. Priority Issues (rangkuman)

| ID | Severity | Area | Ringkas |
|----|----------|------|---------|
| ID-001 | HIGH | teacher-flow | Setup awal masih butuh bantuan |
| ID-002 | MEDIUM | question-copy | Sebagian instruksi terlalu panjang |
| ID-003 | MEDIUM | audio | Audio kurang jelas di ruangan besar |

---

## 14. Recommended Changes (untuk Iterasi 14)

### MUST FIX

```text
- ID-001 perbaiki alur setup awal agar guru bisa mulai sendiri.
```

### SHOULD FIX

```text
- ID-002 ringkas instruksi soal.
- ID-003 tingkatkan kejelasan audio di kelas besar.
```

### COULD FIX

```text
- Tambahkan feedback visual kecil saat jawaban benar.
```

### NOT NOW

```text
- chat antar pemain
- leaderboard global
- avatar lanjutan
```

---

## 15. Recommendation for Iteration 14

```text
Iterasi 14 sebaiknya fokus pada penyederhanaan teacher flow, perbaikan wording soal, dan penguatan stabilitas saat dipakai di kelas nyata. Real classroom pilot masih perlu dilakukan sebelum keputusan fitur besar berikutnya.
```

---

## 16. Limitation

```text
- Phase C (Real Classroom) belum dilakukan karena jadwal kelas belum tersedia.
- Hanya 2 story diuji, jadi generalisasi ke seluruh konten belum lengkap.
- Device matrix masih terbatas pada Android dan iPhone.
```

---

## Lampiran

- Doc index: [`pilot-index.md`](./pilot-index.md)
- Observation mentah: [`pilot-observation.md`](./pilot-observation.md)
- Issue log: [`pilot-issues.md`](./pilot-issues.md)
- Session analytics: `public/teacher/analytics.php`
- Deployment reference: [`deployment.md`](./deployment.md)
- Backup reference: [`backup-recovery.md`](./backup-recovery.md)

---

**Gunakan file ini hanya sebagai contoh format.**
**Untuk hasil nyata, isi berdasarkan observasi lapangan dan analytics aktual.**

