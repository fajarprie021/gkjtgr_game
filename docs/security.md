# Security Notes

> Ringkasan keamanan Bible Adventure untuk rilis v1.
> Dokumen ini bukan pengganti audit keamanan profesional.

---

## Area yang Sudah Diterapkan

```text
- Session cookie params (httponly, samesite)
- session_regenerate_id saat login staff/player
- .env tidak di-commit
- APP_DEBUG=false di production
- Role check untuk halaman staff/admin
- Validasi input dan method untuk API publik
- Health endpoint minim tanpa rahasia
- Security headers dasar (lihat config/security.php)
- Backup di luar public/
```

---

## Area yang Perlu Diperhatikan

```text
- HTTPS wajib di production
- CSP masih permisif untuk kompatibilitas Bootstrap/CDN
- Logging tidak boleh menulis rahasia
- Limit login attempt belum diterapkan
```

---

## Prinsip

- Jangan tulis rahasia ke dalam kode.
- Jangan ekspos stack trace ke user.
- Rotasi credential berkala.
- Backup sebelum perubahan produksi.

---

## Referensi

- [`production-readiness.md`](./production-readiness.md)
- [`deployment.md`](./deployment.md)
- [`operations.md`](./operations.md)
