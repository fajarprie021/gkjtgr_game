# Post-Pilot Fix Plan

> Rencana perbaikan Iteration 14 berdasarkan evidence dari pilot.
> 
> Prinsip: **Evidence → Priority → Fix → Retest → Validate**.
> 
> Dokumen ini harus diperbarui sebelum coding perbaikan utama dimulai.

---

## Summary of Priorities

```text
P0 Issues      : 0 confirmed in current placeholder set
P1 Issues      : 1 confirmed focus area
P2 Issues      : 2 follow-up improvements
Deferred Issues : P3 polish / non-blocker enhancements
```

> Isi angka di atas dari evidence nyata. Jangan mengarang severity.

---

## P0 Issues

```text
[none confirmed yet]
```

### Validation Rule

- P0 hanya dipakai untuk blocker nyata: gagal start, join gagal, progress hilang, jawaban tidak tersimpan, security issue, konten Alkitab salah.
- Jika ada P0, hentikan pekerjaan lain sampai issue itu divalidasi ulang.

---

## P1 Issues

### ID-001 — Teacher flow setup still needs help

```text
Problem
Teacher masih membutuhkan bantuan saat memulai session pertama.

Evidence
2 dari 3 guru meminta bantuan untuk menemukan/menjalankan alur setup.

Root Cause Hypothesis
- CTA terlalu tersembunyi
- Urutan setup belum cukup jelas
- Copy instruksi terlalu panjang

Proposed Fix
- Perjelas alur setup utama
- Ubah urutan visual CTA jika perlu
- Ringkas copy instruksi guru

Files Likely Affected
- public/teacher/*.php
- public/assets/js/*
- docs/teacher-guide.md (jika ada pembaruan)

Validation Method
- Internal teacher retest
- Observe time-to-start
- Catat apakah guru masih perlu bantuan
```

---

## P2 Issues

### ID-002 — Question wording too long / unclear

```text
Problem
Sebagian instruksi soal terlalu panjang untuk anak.

Evidence
Beberapa anak meminta pengulangan atau salah memahami instruksi.

Root Cause Hypothesis
- Copy terlalu verbose
- Kalimat instruksi campur dengan konteks cerita
- UI instruction tidak dipisahkan jelas dari pertanyaan

Proposed Fix
- Ringkas wording pertanyaan
- Pisahkan konteks cerita dari instruksi aksi
- Tambahkan formatting yang lebih mudah dipindai

Files Likely Affected
- public/assets/js/api.js
- public/assets/js/game-engine.js
- story / content source files

Validation Method
- Small-group retest
- Observe comprehension tanpa bantuan guru
- Bandingkan jumlah pengulangan instruksi
```

### ID-003 — Audio clarity in larger room

```text
Problem
Audio kurang jelas di ruangan besar.

Evidence
Observer mencatat audio tidak selalu terdengar jelas pada setting ruang yang lebih besar.

Root Cause Hypothesis
- Volume default terlalu rendah
- Output hanya dari satu device
- Ruangan besar memerlukan audio guidance yang berbeda

Proposed Fix
- Review default volume / mute behavior
- Tambahkan guidance untuk penggunaan audio classroom
- Jika perlu, batasi audio ke teacher-device

Files Likely Affected
- public/assets/js/audio-manager.js
- docs/troubleshooting.md (jika diperlukan)
- README.md / teacher guide

Validation Method
- Classroom-fit retest
- Periksa kejelasan audio dari belakang ruangan
- Observasi gangguan terhadap fokus anak
```

---

## Deferred Issues

```text
- P3 polish visual
- decorative animation
- optional microcopy improvements
```

---

## Root Cause Notes

### Usability vs Learning

- Jika anak tahu jawaban tetapi tidak tahu cara menekan/menavigasi UI, itu **usability problem**.
- Jika anak paham UI tetapi tidak paham isi cerita atau jawaban, itu **learning problem**.
- Jangan memperbaiki learning problem hanya dengan polish visual.

### Fix Strategy

- Prioritaskan issue yang memengaruhi guru dapat memulai session tanpa bantuan.
- Prioritaskan wording dan flow sebelum menambah visual baru.
- Jangan melakukan major feature work sampai issue utama tervalidasi.

---

## Retest Plan

```text
1. Perbaiki P1 dahulu.
2. Turunkan wording / clarity issue P2.
3. Jalankan regression test.
4. Jalankan focused re-test untuk setiap issue yang difix.
5. Tandai VALIDATED hanya jika evidence re-test mendukung.
```

---

## Related Documents

- [`pilot-summary.md`](./pilot-summary.md)
- [`pilot-issues.md`](./pilot-issues.md)
- [`post-pilot-validation.md`](./post-pilot-validation.md)
- [`pilot-index.md`](./pilot-index.md)
- [`CHANGELOG.md`](../CHANGELOG.md)
