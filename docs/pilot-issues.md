# Pilot Issue Log

> Log terpusat untuk seluruh issue yang ditemukan selama pilot
> Bible Adventure Sekolah Minggu GKJ Tangerang.
>
> Setiap issue di [`pilot-observation.md`](./pilot-observation.md)
> harus dipindahkan ke sini dengan **ID unik** agar bisa dilacak
> lintas sesi dan lintas fase pilot.
>
> **Prinsip:** "Issue kecil tetapi sering dapat lebih penting
> daripada issue besar yang hanya terjadi sekali."

---

## Severity Levels

| Severity | Contoh |
|----------|--------|
| **CRITICAL** | game tidak dapat dimulai, progress hilang, player tidak dapat join, teacher tidak dapat mengontrol session, security issue |
| **HIGH** | banyak anak tidak memahami satu mechanic, guru sering membutuhkan bantuan, session sync sering gagal, question instruction membingungkan |
| **MEDIUM** | button kurang jelas, setup sedikit lambat, feedback sering dilewati, spacing bermasalah |
| **LOW** | minor animation, visual detail, copy kecil, cosmetic alignment |

## Frequency Levels

| Frequency | Arti |
|-----------|------|
| **ONCE** | terjadi satu kali, sulit direproduksi |
| **OCCASIONAL** | kadang-kadang terjadi |
| **FREQUENT** | sering terjadi pada hampir tiap sesi |
| **ALMOST ALWAYS** | terjadi hampir setiap kali diuji |

> Frekuensi tinggi dengan severity rendah **tetap masuk rekomendasi SHOULD FIX**.

---

## Issue Format

Setiap issue menggunakan format tetap:

```text
ID         : ID-NNN
Area       : teacher-flow | player-flow | mechanic | content | ui | audio | network | analytics | security
Date Found : YYYY-MM-DD
Pilot Build: v0.x.y
Phase      : A | B | C
Observation: [deskripsi netral tentang apa yang diamati]
Severity   : CRITICAL | HIGH | MEDIUM | LOW
Frequency  : ONCE | OCCASIONAL | FREQUENT | ALMOST ALWAYS
Evidence   : [bukti konkret — jumlah kasus, kutipan, nomor baris, dsb]
Suggested  : [langkah selanjutnya yang disarankan — bukan implementasi]
Status     : OPEN | TRIAGED | DEFERRED | RESOLVED
```

---

## Open Issues

### ID-001

```text
ID         : ID-001
Area       : [mis. teacher-flow]
Date Found : YYYY-MM-DD
Pilot Build: v0.x.y
Phase      : A
Observation: [contoh] Tombol "Mulai Sesi" sulit ditemukan di lobby.
Severity   : HIGH
Frequency  : FREQUENT
Evidence   : 2 dari 3 guru meminta bantuan untuk menemukan tombol.
Suggested  : Pindahkan CTA Start ke area utama lobby.
Status     : OPEN
```

> Salin blok di atas untuk issue baru. Jangan hapus issue yang sudah ada — ubah `Status` jika selesai ditangani.

---

## Triaged Issues (siap masuk Iterasi 14)

```text
ID-002  HIGH  mechanic          [ringkas]
ID-003  MED   ui                [ringkas]
```

## Deferred Issues

```text
[daftar issue yang ditunda dengan alasan]
```

## Resolved Issues

```text
[daftar issue yang sudah selesai ditangani]
```

---

## Referensi

- Doc index: [`pilot-index.md`](./pilot-index.md)
- Observation source: [`pilot-observation.md`](./pilot-observation.md)
- Summary: [`pilot-summary.md`](./pilot-summary.md)
- Summary example: [`pilot-summary-example.md`](./pilot-summary-example.md)
- Prioritization framework: `prompt/13_prompt.txt` sections #89–#93 (MUST / SHOULD / COULD / NOT NOW)
