# Troubleshooting Guide

> Panduan singkat untuk masalah yang sering muncul saat pemakaian
> Bible Adventure di Sekolah Minggu GKJ Tangerang.

---

## 1. Login Guru / Admin Gagal

**Gejala:** tidak bisa login atau session logout sendiri.

**Cek:**
- Kredensial benar
- Akun masih aktif
- `.env` sudah benar
- `config/auth.php` tidak berubah

**Tindakan:**
- Reset password oleh admin jika perlu.
- Lihat log error aplikasi.

---

## 2. Pemain Tidak Bisa Join

**Gejala:** kode sesi tidak diterima, atau pemain stuck di layar join.

**Cek:**
- Kode sesi benar
- Session masih aktif di sisi guru
- Jaringan device pemain stabil

**Tindakan:**
- Minta pemain refresh halaman.
- Buat sesi baru jika session rusak.

---

## 3. Game Tidak Lanjut ke Pertanyaan Berikutnya

**Gejala:** guru sudah tekan Next, tetapi player masih di soal lama.

**Cek:**
- Koneksi jaringan device
- Apakah polling bekerja

**Tindakan:**
- Refresh player device.
- Guru refresh halaman kontrol dan ulangi.

---

## 4. Audio Tidak Terdengar

**Gejala:** suara tidak keluar dari device.

**Cek:**
- Volume device
- Mute preference
- Browser tidak memblokir autoplay

**Tindakan:**
- Naikkan volume.
- Gunakan teacher-device audio untuk ruang besar.

---

## 5. Proyektor Tidak Terbaca dari Belakang

**Gejala:** teks di layar guru tidak terbaca dari belakang ruangan.

**Cek:**
- Ukuran font di halaman kontrol
- Resolusi proyektor

**Tindakan:**
- Perbesar font tampilan kontrol.
- Dekatkan proyektor jika memungkinkan.

---

## 6. Progress Tidak Tersimpan

**Gejala:** pemain harus mengulang dari awal.

**Cek:**
- Apakah pemain terdaftar login dengan benar
- Apakah device menyimpan localStorage (untuk guest)

**Tindakan:**
- Login sebagai pemain terdaftar.
- Jangan clear browser data di device pemain.

---

## 7. Health Endpoint Tidak Sehat

**Gejala:** `public/health.php` menunjukkan status tidak sehat.

**Cek:**
- Koneksi database
- Kredensial DB
- Status service PHP/MySQL

**Tindakan:**
- Restart service jika perlu.
- Cek log server.
- Lihat [`operations.md`](./operations.md).

---

## 8. Konten Tidak Tepat / Alkitab Salah

**Gejala:** ditemukan konten yang keliru.

**Tindakan:**
- Ubah status konten ke `needs_review`.
- Jangan biarkan konten meragukan tampil di production.
- Lapor ke admin untuk verifikasi.

---

## Referensi

- [`operations.md`](./operations.md)
- [`security.md`](./security.md)
- [`v1-release.md`](./v1-release.md)
- [`teacher-guide.md`](./teacher-guide.md)
- [`admin-guide.md`](./admin-guide.md)
