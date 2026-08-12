# Admin Guide

> Panduan singkat untuk admin yang mengelola Bible Adventure.

---

## 1. Tanggung Jawab Admin

- Mengelola akun guru.
- Mengelola akun pemain terdaftar.
- Memvalidasi konten cerita, pertanyaan, dan jawaban.
- Mengatur urutan story di Bible Map.
- Memantau analytics.
- Menjaga backup database.

---

## 2. Login Admin

1. Buka `public/admin/`.
2. Login dengan akun admin.
3. Pilih modul yang ingin dikelola.

---

## 3. Mengelola Konten

| Aksi | Lokasi |
|------|--------|
| Tambah / edit story | Admin → Content → Story |
| Tambah / edit question | Admin → Content → Question |
| Verifikasi konten | Admin → Content → Review |
| Atur urutan map | Admin → Map Order |

> Konten hanya boleh berstatus `active` dan `verified` untuk release.

---

## 4. Mengelola User

| Aksi | Lokasi |
|------|--------|
| Tambah guru | Admin → Users → Teacher |
| Reset password guru | Admin → Users → Teacher |
| Tambah pemain terdaftar | Admin → Users → Player |
| Nonaktifkan akun | Admin → Users → Deactivate |

---

## 5. Monitoring

- Pantau analytics dashboard di `public/teacher/analytics.php`.
- Cek error log secara berkala.
- Cek status deployment melalui `public/health.php`.

---

## 6. Backup & Recovery

- Ikuti prosedur di [`backup-recovery.md`](./backup-recovery.md).
- Backup disimpan di luar `public/`.
- Uji restore secara berkala.

---

## 7. Referensi

- [`v1-release.md`](./v1-release.md)
- [`security.md`](./security.md)
- [`operations.md`](./operations.md)
- [`backup-recovery.md`](./backup-recovery.md)
