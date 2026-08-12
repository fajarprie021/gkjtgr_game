# Developer Guide

> Panduan singkat untuk developer yang akan memelihara
> Bible Adventure v1.

---

## Stack

- PHP 8.3
- MySQL 8
- Bootstrap 5 (CSS/JS via CDN)
- Polling-based sync untuk session

---

## Struktur Penting

```text
public/                → document root
config/                → konfigurasi internal (jangan dipublish)
database/              → schema SQL
docs/                  → dokumentasi
prompt/                → source prompt iterasi
```

---

## Konvensi

- Hindari hardcoded secret.
- Pakai helper `sendSecurityHeaders()` untuk header aman.
- Pisahkan logika staff dan public.
- Pertahankan kompatibilitas dengan Bootstrap & CDN.

---

## Alur Kerja

1. Baca prompt iterasi yang sedang dijalankan.
2. Baca dokumentasi terkait di `docs/`.
3. Implementasikan perubahan minimum yang dibutuhkan.
4. Uji secara lokal.
5. Update dokumentasi yang terdampak.
6. Catat perubahan ke `CHANGELOG.md`.

---

## Aturan Rilis

```text
PATCH : bug fix, security fix kecil, copy fix
MINOR : fitur baru yang backward compatible
MAJOR : perubahan arsitektur / breaking change
```

---

## Referensi

- [`deployment.md`](./deployment.md)
- [`security.md`](./security.md)
- [`operations.md`](./operations.md)
- [`v1-release.md`](./v1-release.md)
