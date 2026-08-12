<?php
require_once '../../config/security.php';
sendSecurityHeaders([
    'csp' => "default-src 'self' https:; img-src 'self' https: data:; style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' https:; font-src 'self' https: data:; connect-src 'self' https:; frame-ancestors 'self';"
]);
require_once '../../config/database.php';
require_once '../../config/auth.php';

requireStaffAuth();
$staff = getStaffUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - Bible Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-mortarboard-fill me-2"></i>Bible Adventure Guru
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($staff['name']) ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row mb-4">
            <div class="col">
                <h2>Dashboard Guru</h2>
                <p class="text-muted">Kelola sesi game kelas dan pemain</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-play-circle display-1 text-primary mb-3"></i>
                        <h5 class="card-title">Buat Sesi Baru</h5>
                        <p class="card-text">Mulai game classroom untuk kelas Anda</p>
                        <a href="session-create.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Buat Sesi
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-list-check display-1 text-success mb-3"></i>
                        <h5 class="card-title">Riwayat Sesi</h5>
                        <p class="card-text">Lihat sesi yang sudah berlangsung</p>
                        <a href="sessions.php" class="btn btn-outline-success">
                            <i class="bi bi-clock-history me-2"></i>Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-1 text-info mb-3"></i>
                        <h5 class="card-title">Kelola Pemain</h5>
                        <p class="card-text">Tambah dan kelola akun pemain</p>
                        <a href="players.php" class="btn btn-outline-info">
                            <i class="bi bi-person-plus me-2"></i>Kelola Pemain
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up-arrow display-1 text-primary mb-3"></i>
                        <h5 class="card-title">Analytics</h5>
                        <p class="card-text">Lihat ringkasan sesi, konten, dan performa mekanik</p>
                        <a href="analytics.php" class="btn btn-outline-primary">
                            <i class="bi bi-bar-chart me-2"></i>Buka Analytics
                        </a>
                    </div>
                </div>
            </div>

            <?php if ($staff['role'] === 'admin'): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-gear display-1 text-warning mb-3"></i>
                        <h5 class="card-title">Admin</h5>
                        <p class="card-text">Kelola konten dan pengaturan</p>
                        <a href="../admin/" class="btn btn-outline-warning">
                            <i class="bi bi-sliders me-2"></i>Admin Panel
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row mt-5">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Sesi Aktif</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center py-4">
                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                            Belum ada sesi aktif. Buat sesi baru untuk memulai.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>