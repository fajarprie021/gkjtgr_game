# Working Instruction — Bible Adventure
## Sekolah Minggu GKJ Tangerang

> **Versi:** v1.0  
> **Berlaku untuk:** Guru, Pemain (Anak), dan Admin  
> **URL Aplikasi:** `http://gkjtgr-game.test`

---

## DAFTAR ISI

1. [Gambaran Umum Aplikasi](#1-gambaran-umum-aplikasi)
2. [Persyaratan Teknis](#2-persyaratan-teknis)
3. [Alur Kerja Guru (Teacher)](#3-alur-kerja-guru-teacher)
4. [Alur Kerja Pemain (Player)](#4-alur-kerja-pemain-player)
5. [Alur Kerja Admin](#5-alur-kerja-admin)
6. [Cara Bermain](#6-cara-bermain)
7. [Troubleshooting Umum](#7-troubleshooting-umum)
8. [Referensi Cepat (Quick Reference)](#8-referensi-cepat-quick-reference)

---

## 1. Gambaran Umum Aplikasi

**Bible Adventure** adalah web game edukasi berbasis tim untuk pembelajaran Alkitab anak-anak Sekolah Minggu. Pemain menjelajahi peta perjalanan Alkitab, menjawab pertanyaan per cerita, dan mengumpulkan poin sebagai tim.

### Peran Pengguna

| Peran | Tanggung Jawab |
|---|---|
| **Guru (Teacher)** | Membuat sesi, memilih cerita, memantau jalannya game |
| **Pemain (Player)** | Bergabung ke sesi, menjawab pertanyaan dalam tim |
| **Admin** | Mengelola konten, akun guru, dan monitoring sistem |

### Alur Sesi Singkat

```
Admin → Konten siap (story + pertanyaan terverifikasi)
  ↓
Guru → Login → Buat Sesi → Bagikan Kode
  ↓
Pemain → Buka URL → Masukkan Kode → Bergabung
  ↓
Guru → Mulai Game → Kontrol Pertanyaan
  ↓
Sesi Selesai → Guru Tinjau Analytics
```

---

## 2. Persyaratan Teknis

### Perangkat Guru
- Laptop atau tablet
- Browser: Chrome 90+ / Firefox 88+ / Safari 14+ / Edge 90+
- Koneksi internet (atau jaringan lokal)
- Proyektor (opsional, untuk menampilkan papan skor)

### Perangkat Pemain
- Smartphone, tablet, atau laptop
- Browser modern dengan dukungan JavaScript
- Koneksi ke jaringan yang sama dengan server

### Server / Teknis
- PHP 8.0+, MySQL 8
- Apache/Laragon berjalan di komputer server
- URL: `http://gkjtgr-game.test` (lokal) atau domain production

---

## 3. Alur Kerja Guru (Teacher)

### 3.1 Persiapan Sebelum Kelas

- [ ] Pastikan laptop/tablet guru terhubung ke jaringan
- [ ] Pastikan server Laragon berjalan (Apache + MySQL hijau)
- [ ] Konfirmasi cerita yang akan dipakai sudah **aktif** di sistem
- [ ] Cek proyektor tersambung (jika digunakan)
- [ ] Siapkan daftar nama tim peserta

---

### 3.2 Login ke Dashboard Guru

1. Buka browser, ketik:
   ```
   http://gkjtgr-game.test/public/teacher/login.php
   ```
2. Masukkan **Username** dan **Password** akun guru.
3. Klik **Login**.
4. Anda akan diarahkan ke **Dashboard Guru**.

> 💡 **Jika lupa password:** Hubungi admin untuk reset.

---

### 3.3 Membuat Sesi Baru

1. Di Dashboard, klik **Buat Sesi Baru** (atau navigasi ke `session-create.php`).
2. Isi formulir:

   | Field | Keterangan |
   |---|---|
   | **Cerita** | Pilih cerita Alkitab yang akan dimainkan |
   | **Kelompok Kelas** | Small (SD 1–2) / Medium (SD 3–4) / Large (SD 5–6) |
   | **Jumlah Tim** | 2–6 tim (geser slider) |
   | **Nama Tim** | Isi nama tiap tim (opsional, default: Tim Merah, dll.) |

3. Klik **Buat Sesi**.
4. **Kode Sesi** akan muncul — **catat atau tampilkan ke layar**.

> ⚠️ **Penting:** Kode sesi hanya berlaku untuk satu sesi. Setiap sesi baru menghasilkan kode berbeda.

---

### 3.4 Membagikan Kode ke Pemain

- Tulis kode sesi di papan tulis, atau
- Tampilkan via proyektor, atau
- Ucapkan kode langsung ke peserta.

Format kode: **6 karakter alfanumerik kapital** (contoh: `BA4X7K`)

---

### 3.5 Memantau Pemain Bergabung

1. Dari Dashboard, buka menu **Pemain** atau klik **Kelola Pemain**.
2. URL: `http://gkjtgr-game.test/public/teacher/players.php`
3. Pantau daftar pemain yang masuk secara real-time.
4. Jika ada nama yang salah, minta pemain daftar ulang.

---

### 3.6 Memulai Game

1. Setelah semua tim siap, klik **Mulai Game**.
2. Sistem akan menampilkan pertanyaan pertama.
3. Kontrol sesi menggunakan tombol:

   | Tombol | Fungsi |
   |---|---|
   | **Next** | Lanjut ke pertanyaan berikutnya |
   | **Reveal** | Tampilkan jawaban benar |
   | **End** | Akhiri sesi lebih awal |
   | **Lock** | Kunci layar tim (opsional) |

---

### 3.7 Setelah Sesi Selesai

1. Klik **Akhiri Sesi**.
2. Papan skor final ditampilkan.
3. Beri tepuk tangan untuk semua tim!
4. Tinjau hasil di menu **Analytics**:
   ```
   http://gkjtgr-game.test/public/teacher/analytics.php
   ```
5. Catat observasi jika ini bagian dari sesi pilot.

---

### 3.8 Melihat Riwayat Sesi

- Buka **Riwayat Sesi** dari Dashboard.
- URL: `http://gkjtgr-game.test/public/teacher/sessions.php`
- Lihat daftar sesi lama, skor, dan partisipan.

---

## 4. Alur Kerja Pemain (Player)

### 4.1 Bergabung ke Sesi

1. Buka browser di smartphone/tablet.
2. Ketik URL yang diberikan guru (atau scan QR code jika tersedia):
   ```
   http://gkjtgr-game.test
   ```
3. Pilih **Bergabung ke Sesi** atau **Join Game**.
4. Masukkan **Kode Sesi** dari guru.
5. Masukkan **Nama Pemain** dan pilih **Tim**.
6. Klik **Bergabung**.

---

### 4.2 Menunggu Game Dimulai

- Setelah bergabung, layar akan menampilkan **ruang tunggu** (lobby).
- Tunggu guru menekan tombol **Mulai Game**.
- Jangan tutup browser selama menunggu.

---

### 4.3 Menjawab Pertanyaan

1. Pertanyaan akan muncul di layar.
2. Baca pertanyaan dengan seksama.
3. Pilih jawaban (A, B, C, atau D) sebelum waktu habis.
4. Poin diberikan untuk jawaban yang **benar dan cepat**.

> 💡 **Tips:** Diskusikan jawaban dengan anggota tim sebelum memilih!

---

### 4.4 Melihat Skor

- Skor tim ditampilkan setelah setiap pertanyaan.
- Papan skor final muncul saat sesi berakhir.

---

## 5. Alur Kerja Admin

### 5.1 Login Admin

1. Buka:
   ```
   http://gkjtgr-game.test/public/admin/
   ```
2. Login dengan akun admin.

---

### 5.2 Mengelola Konten

| Tugas | Langkah |
|---|---|
| **Tambah Cerita** | Admin → Content → Story → Tambah Baru |
| **Edit Pertanyaan** | Admin → Content → Question → Edit |
| **Verifikasi Konten** | Admin → Content → Review → Ubah status menjadi `verified` |
| **Atur Urutan Peta** | Admin → Map Order → Drag & Drop urutan |

> ⚠️ **Aturan:** Cerita hanya dapat dimainkan jika statusnya `active` DAN `verified`.

---

### 5.3 Mengelola Akun Guru

| Tugas | Langkah |
|---|---|
| Tambah guru baru | Admin → Users → Teacher → Tambah |
| Reset password | Admin → Users → Teacher → Reset Password |
| Nonaktifkan akun | Admin → Users → Deactivate |

---

### 5.4 Monitoring Harian

- **Health Check:** `http://gkjtgr-game.test/public/health.php`
- **Error Log:** `C:/laragon/etc/apache2/logs/gkjtgr_game-error.log`
- **Analytics:** `http://gkjtgr-game.test/public/teacher/analytics.php`

---

### 5.5 Backup Database

1. Jalankan backup sebelum setiap sesi besar.
2. Simpan backup di luar folder `public/`.
3. Format file backup: `backup_YYYYMMDD_HHII.sql`
4. Lihat prosedur lengkap di [`backup-recovery.md`](./backup-recovery.md).

---

## 6. Cara Bermain

### Sistem Kelas

| Kelas | Target | Tingkat Soal |
|---|---|---|
| **Small** | SD Kelas 1–2 | Mudah, bahasa sederhana |
| **Medium** | SD Kelas 3–4 | Sedang |
| **Large** | SD Kelas 5–6 | Lebih dalam, konsep teologis |

### Urutan Cerita (Story Map)

Pemain menjelajahi peta Alkitab secara berurutan. Cerita terbuka satu per satu setelah cerita sebelumnya diselesaikan:

```
Penciptaan → Nuh → Abraham → Yusuf → Musa → ...
```

### Poin & Skor

- Jawaban benar = **+10 poin**
- Jawaban salah = **0 poin**
- Bonus kecepatan = poin tambahan jika menjawab lebih awal

### Mode Tim

- Setiap tim mendiskusikan jawaban bersama.
- Salah satu anggota tim mengirimkan jawaban.
- Skor tim adalah akumulasi semua jawaban benar.

---

## 7. Troubleshooting Umum

| Masalah | Solusi |
|---|---|
| **Halaman tidak terbuka** | Cek Laragon berjalan (Apache + MySQL hijau) |
| **Kode sesi salah** | Minta guru konfirmasi kode, perhatikan huruf O vs angka 0 |
| **Pemain tidak muncul di daftar** | Muat ulang halaman Players di dashboard guru |
| **Database error** | Cek `config/database.php`, pastikan nama DB dan password benar |
| **Lupa password guru** | Admin reset via `Admin → Users → Teacher → Reset Password` |
| **Pertanyaan tidak muncul** | Pastikan cerita sudah punya pertanyaan aktif di database |
| **Layar putih / error PHP** | Lihat error log di `C:/laragon/etc/apache2/logs/` |

> 📋 Lihat panduan lengkap di [`troubleshooting.md`](./troubleshooting.md)

---

## 8. Referensi Cepat (Quick Reference)

### URL Penting

| Halaman | URL |
|---|---|
| Halaman Utama | `http://gkjtgr-game.test/public/` |
| Login Guru | `http://gkjtgr-game.test/public/teacher/login.php` |
| Dashboard Guru | `http://gkjtgr-game.test/public/teacher/dashboard.php` |
| Buat Sesi | `http://gkjtgr-game.test/public/teacher/session-create.php` |
| Riwayat Sesi | `http://gkjtgr-game.test/public/teacher/sessions.php` |
| Kelola Pemain | `http://gkjtgr-game.test/public/teacher/players.php` |
| Analytics | `http://gkjtgr-game.test/public/teacher/analytics.php` |
| Login Admin | `http://gkjtgr-game.test/public/admin/` |
| Health Check | `http://gkjtgr-game.test/public/health.php` |

---

### Checklist Pra-Sesi (Guru)

```
[ ] Server Laragon berjalan (Apache + MySQL)
[ ] Browser terbuka dan dapat akses URL aplikasi
[ ] Cerita yang dipilih sudah aktif & terverifikasi
[ ] Akun guru dapat login
[ ] Proyektor/layar besar terhubung (jika dipakai)
[ ] Daftar tim peserta sudah siap
[ ] Perangkat pemain terhubung ke jaringan
```

---

### Kontak & Eskalasi

| Masalah | Hubungi |
|---|---|
| Akun / login | Admin sistem |
| Konten Alkitab | Pengampu kurikulum / Pendeta |
| Masalah teknis | Developer / Admin sistem |
| Laporan bug | Catat di [`docs/pilot-issues.md`](./pilot-issues.md) |

---

*Dokumen ini merupakan instruksi kerja resmi Bible Adventure — GKJ Tangerang Sekolah Minggu.*  
*Revisi terakhir: Agustus 2026*