# Operations Runbook

> Prosedur operasional harian untuk Bible Adventure v1.

---

## Health Check Harian

1. Cek `public/health.php`.
2. Pastikan status response JSON aman.
3. Jika status tidak sehat, lihat log dan mulai troubleshooting.

---

## Backup

1. Ikuti [`backup-recovery.md`](./backup-recovery.md).
2. Backup disimpan di luar `public/`.
3. Backup sebelum perubahan produksi.

---

## Monitoring

- Pantau analytics di `public/teacher/analytics.php`.
- Cek log aplikasi berkala.
- Cek error rate di server.

---

## Incident Handling

```text
1. Identifikasi gejala
2. Cek health endpoint
3. Cek log
4. Cek backup
5. Tentukan rollback atau fix
6. Catat ke troubleshooting
```

---

## Perubahan Produksi

```text
1. Buat branch perubahan
2. Uji di server pilot/staging
3. Backup database
4. Deploy
5. Jalankan smoke test
6. Catat perubahan di CHANGELOG.md
```

---

## Referensi

- [`deployment.md`](./deployment.md)
- [`security.md`](./security.md)
- [`backup-recovery.md`](./backup-recovery.md)
- [`troubleshooting.md`](./troubleshooting.md)
