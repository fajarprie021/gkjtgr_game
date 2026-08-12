# Pilot Observation Sheet

> Template observasi untuk mencatat data lapangan selama pilot
> Bible Adventure Sekolah Minggu GKJ Tangerang.
>
> Gunakan satu file per sesi pilot. Duplikat file ini bila perlu.
>
> Prinsip utama: **OBSERVE → MEASURE → LEARN → DOCUMENT**

---

## Metadata Sesi

```text
Date                : YYYY-MM-DD
Phase               : A (Internal Teacher) | B (Small Group) | C (Real Classroom)
Class               : Kecil | Madya | Besar
Story Tested        : [slug cerita, mis. creation / noah]
Teacher             : [nama inisial]
Observer            : [nama inisial]
Pilot Build         : [versi aplikasi, mis. v0.9.0]
Number of Teams     : [jumlah tim]
Approximate Players : [perkiraan jumlah anak]
Device Setup        : [lihat Device Matrix di bawah]
Network             : Wi-Fi | Mobile Hotspot | Mixed
                     kualitas: stable | sometimes slow | unstable
Duration (total)    : [mm menit]
```

### Device Matrix

```text
Teacher Device : [mis. Laptop Chrome 120]
Presentation   : [mis. Projector HDMI, atau None]
Team 1 Device  : [mis. Android phone, Chrome]
Team 2 Device  : [mis. iPhone, Safari]
Team 3 Device  : [opsional]
Team 4 Device  : [opsional]
```

> **Privasi:** Jangan catat device identifier pribadi (mis. nama akun, nomor seri).

---

## SETUP

| Tahap | Hasil | Catatan |
|-------|-------|---------|
| Teacher login | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Create session | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Choose class | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Choose story | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Generate code | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Share code to players | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Player join | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Team arrangement | SUCCESS / SUCCESS WITH HELP / FAILED | |

**Setup time:** [mm menit dari "session created" sampai "all players joined"]

---

## GAMEPLAY

| Tahap | Hasil | Catatan |
|-------|-------|---------|
| Start game | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Story intro | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Question display | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Answer input | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Reveal answer | SUCCESS / SUCCESS WITH HELP / FAILED | |
| Next question | SUCCESS / SUCCESS WITH HELP / FAILED | |
| End session | SUCCESS / SUCCESS WITH HELP / FAILED | |

**Gameplay time:** [mm menit dari "game started" sampai "game completed"]

---

## Question Comprehension

Untuk tiap soal, catat jika anak:

```text
- langsung memahami
- meminta guru mengulang
- salah memahami instruksi
- tidak tahu cara berinteraksi
```

Bedakan **CONTENT DIFFICULTY** vs **UI / INSTRUCTION DIFFICULTY**.

### Mechanic Observation

| Mechanic | Digunakan? | Observasi |
|----------|------------|-----------|
| multiple_choice | yes/no | |
| true_false | yes/no | |
| sequence | yes/no | |
| matching | yes/no | |
| timeline | yes/no | |
| verse_puzzle | yes/no | |

### Engagement & Attention

```text
Child attention    : focused | sometimes distracted | frequently distracted
Engagement moments : [exited, laughing, discussing, asking questions, wanting to continue]
Distraction        : [kapan, pada bagian apa]
Noise source       : diskusi | kebingungan | kompetisi | masalah perangkat
```

### Team Collaboration

```text
Collaboration signal: HIGH | MEDIUM | LOW
- Apakah anak berdiskusi?
- Apakah satu anak mengambil alih device?
- Apakah semua anggota memahami pertanyaan?
- Apakah tim benar-benar mengambil keputusan bersama?
```

### Score Effect

```text
Score motivasi  : motivating | no-effect | too-dominating
Anak fokus pada : who-wins / points / feedback
Feedback dilewati: yes/no
```

---

## LEARNING (Learning Check)

Setelah game selesai, lakukan mini learning check. Sesuaikan pertanyaan dengan kelas.

### Pertanyaan yang Ditanyakan

```text
1. [pertanyaan]
   Jawaban anak: [...]
   Level: Understood | Partially Understood | Unclear

2. [pertanyaan]
   Jawaban anak: [...]
   Level: Understood | Partially Understood | Unclear

3. [pertanyaan]
   Jawaban anak: [...]
   Level: Understood | Partially Understood | Unclear
```

### Adaptasi per Kelas

- **Kelas Kecil (SD 1–2):** siapa, apa, mana, sebelum/sesudah sederhana.
- **Kelas Madya (SD 3–4):** urutan, sebab-akibat sederhana, nilai karakter.
- **Kelas Besar (SD 5–6):** mengapa, keputusan tokoh, sebab-akibat, timeline, penerapan.

### Timeline Check (jika applicable)

```text
Q: Mana cerita yang lebih dulu?
A: [...]

Q: Apa cerita setelah ini?
A: [...]
```

### Character Value Check

```text
Q: Apa yang bisa kita lakukan dari pelajaran cerita ini?
A: [...]
```

### Memory Check (jika ada ayat hafalan)

```text
- mengenali referensi
- mengenali sebagian kata
- mengingat gagasan utama
```

---

## TECHNICAL

| Aspek | Observasi |
|-------|-----------|
| Join failures | [jumlah, penyebab] |
| API failures | [endpoint, error] |
| Polling delays | [lokasi, durasi] |
| Disconnects | [jumlah, recovery] |
| Double submits | [jumlah, dampak] |
| Loading issues | [layar, waktu] |
| Session sync issues | [teacher vs player] |

### Reconnect Test (Internal)

```text
Action    : refresh player device
Expected  : player kembali ke session yang sama
Actual    : [...]
Pass/Fail : [...]
```

### Network Interruption Test (Internal)

```text
Action    : Wi-Fi diputus sebentar
Expected  : UI menampilkan reconnect state, session tidak rusak
Actual    : [...]
Pass/Fail : [...]
```

### Double Tap Test

```text
Action    : tekan tombol KUNCI JAWABAN berulang
Expected  : tidak ada multiple answers / score
Actual    : [...]
Pass/Fail : [...]
```

### Teacher Refresh Test

```text
Action    : teacher refresh halaman
Expected  : session tetap tersedia
Actual    : [...]
Pass/Fail : [...]
```

### Projector Test

```text
Dari belakang ruangan:
- judul terbaca? yes/no
- pertanyaan terbaca? yes/no
- jawaban terbaca? yes/no
- score terbaca? yes/no
```

### Audio Test

```text
- volume cukup?
- delay terdengar?
- multi-device sync?
- distraction dari audio?
Rekomendasi: gunakan teacher-device audio saja? yes/no
```

### Mute Test

```text
- mute preference mudah ditemukan? yes/no
- mute berfungsi? yes/no
```

---

## TEACHER FEEDBACK INTERVIEW

Tanyakan singkat (≤ 5 menit) setelah sesi:

```text
1. Bagian apa yang paling mudah?
   [...]

2. Bagian apa yang membingungkan?
   [...]

3. Apakah setup terlalu lama?
   [...]

4. Apakah anak terlihat terlibat?
   [...]

5. Apakah game membantu menjelaskan cerita?
   [...]

6. Bagian apa yang akan Anda ubah sebelum memakai lagi?
   [...]
```

---

## CHILD FEEDBACK

Gunakan pertanyaan sederhana, netral, dan **JANGAN LEADING**:

```text
1. Bagian mana paling seru?
   [...]

2. Bagian mana sulit?
   [...]

3. Kamu tahu harus menekan apa?
   [...]

4. Kamu suka main bersama tim?
   [...]

5. Cerita apa yang kamu ingat?
   [...]

6. Kamu mau main lagi?
   [...]
```

> **Catatan privasi:** Gunakan label `Player A`, `Team Bintang` untuk laporan umum.
> Jangan merekam foto/video/audio anak kecuali ada proses izin yang sesuai.

---

## SESSION DURATION BREAKDOWN

```text
Story Intro      : [mm menit]
Mission          : [mm menit]
Questions        : [mm menit]
Feedback         : [mm menit]
Results          : [mm menit]

Setup time       : [mm menit — teacher login → all players joined]
Gameplay time    : [mm menit — game started → game completed]
Total session    : [mm menit]
```

---

## ISSUES (Ringkas)

Pindahkan detail ke [`pilot-issues.md`](./pilot-issues.md) dengan ID unik.

```text
- [ID-001] [ringkas satu baris]
- [ID-002] [ringkas satu baris]
```

---

## CATATAN OBSERVER

```text
[Catatan bebas observer tentang hal yang tidak masuk kolom di atas.
Misalnya: reaksi spontan anak, komentar guru di sela-sela, kejadian tak terduga.]
```

---

**Setelah sesi selesai:**

1. Pindahkan issue ke `docs/pilot-issues.md`.
2. Tambahkan rangkuman ke `docs/pilot-summary.md`.
3. Tandai checklist di README bagian "Pilot Testing & Classroom Validation".
4. Lihat peta lengkap di `docs/pilot-index.md`.




