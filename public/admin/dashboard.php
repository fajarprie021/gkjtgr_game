<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

// Must be admin
if (!isset($_SESSION['staff_id']) || ($_SESSION['staff_role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

$admin = getStaffUser();

// Stats
$stats = [
    'teachers'  => 0,
    'players'   => 0,
    'sessions'  => 0,
    'stories'   => 0,
    'questions' => 0,
];

if ($pdo) {
    try {
        $stats['teachers']  = (int)$pdo->query("SELECT COUNT(*) FROM staff_users WHERE role='teacher' AND is_active=1")->fetchColumn();
        $stats['players']   = (int)$pdo->query("SELECT COUNT(*) FROM players WHERE is_active=1")->fetchColumn();
        $stats['sessions']  = (int)$pdo->query("SELECT COUNT(*) FROM game_sessions")->fetchColumn();
        $stats['stories']   = (int)$pdo->query("SELECT COUNT(*) FROM stories WHERE is_active=1")->fetchColumn();
        $stats['questions'] = (int)$pdo->query("SELECT COUNT(*) FROM questions WHERE is_active=1")->fetchColumn();
    } catch (Exception $e) {
        // Tables may not exist yet; silently ignore
    }

    // Recent sessions
    try {
        $recentSessions = $pdo->query("
            SELECT gs.session_code, gs.class_group, gs.status, gs.created_at,
                   su.name AS teacher_name
            FROM game_sessions gs
            LEFT JOIN staff_users su ON su.id = gs.teacher_id
            ORDER BY gs.created_at DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $recentSessions = [];
    }

    // Recent staff
    try {
        $recentStaff = $pdo->query("
            SELECT id, name, email, role, is_active, created_at
            FROM staff_users
            ORDER BY created_at DESC
            LIMIT 8
        ")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $recentStaff = [];
    }
} else {
    $recentSessions = [];
    $recentStaff    = [];
}

function statusBadge(string $status): string {
    $map = [
        'lobby'     => 'secondary',
        'active'    => 'success',
        'paused'    => 'warning',
        'completed' => 'primary',
    ];
    $color = $map[$status] ?? 'light';
    return "<span class=\"badge bg-{$color}\">{$status}</span>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bible Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <style>
        :root { --admin-dark: #1e3a5f; --admin-green: #2d6a4f; }
        body { background: #f0f2f5; }

        /* Sidebar */
        .admin-sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--admin-dark);
            color: #fff;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h5 { font-size: 1rem; margin: 0; }
        .sidebar-brand small { opacity: .6; font-size: .75rem; }
        .sidebar-nav { flex: 1; padding: .5rem 0; }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.75);
            padding: .6rem 1.25rem;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .9rem;
            transition: background .15s;
        }
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,.12);
            color: #fff;
        }
        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.1);
            font-size: .8rem;
            opacity: .6;
        }

        /* Main content */
        .admin-main {
            margin-left: 240px;
            min-height: 100vh;
        }
        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .admin-content { padding: 1.5rem; }

        /* Stat cards */
        .stat-card {
            border: none;
            border-radius: .75rem;
            padding: 1.25rem 1.5rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-card i { font-size: 2.2rem; opacity: .85; }
        .stat-card .stat-num { font-size: 2rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-label { font-size: .8rem; opacity: .85; margin-top: .2rem; }

        .bg-navy { background: #1e3a5f; }
        .bg-forest { background: #2d6a4f; }
        .bg-amber { background: #b45309; }
        .bg-indigo { background: #4338ca; }
        .bg-rose { background: #be185d; }

        @media (max-width: 768px) {
            .admin-sidebar { display: none; }
            .admin-main { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<div class="admin-sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-shield-lock-fill fs-4"></i>
            <h5>Bible Adventure</h5>
        </div>
        <small>Admin Portal</small>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-link active">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="users.php" class="nav-link">
            <i class="bi bi-people"></i> Kelola Staff
        </a>
        <a href="players-manage.php" class="nav-link">
            <i class="bi bi-person-badge"></i> Kelola Pemain
        </a>
        <hr style="border-color:rgba(255,255,255,.15);margin:.5rem 1rem;">
        <a href="../teacher/sessions.php" class="nav-link" target="_blank">
            <i class="bi bi-collection-play"></i> Riwayat Sesi
        </a>
        <a href="../teacher/analytics.php" class="nav-link" target="_blank">
            <i class="bi bi-bar-chart"></i> Analytics
        </a>
        <a href="../health.php" class="nav-link" target="_blank">
            <i class="bi bi-heart-pulse"></i> Health Check
        </a>
    </nav>

    <div class="sidebar-footer">
        GKJ Tangerang &copy; <?= date('Y') ?>
    </div>
</div>

<!-- ===== MAIN ===== -->
<div class="admin-main">
    <!-- Topbar -->
    <div class="admin-topbar">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-grid-1x2 me-2 text-secondary"></i>Dashboard Admin
        </h6>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">
                <i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars($admin['name']) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>

    <div class="admin-content">

        <?php if (!$pdo): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            <div>
                <strong>Database tidak terhubung.</strong>
                Periksa konfigurasi di <code>config/database.php</code>.
            </div>
        </div>
        <?php endif; ?>

        <!-- Welcome -->
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Selamat datang, <?= htmlspecialchars($admin['name']) ?>!</h4>
            <p class="text-muted mb-0">
                <i class="bi bi-clock me-1"></i>
                <?= date('l, d F Y — H:i') ?> WIB
            </p>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg">
                <div class="stat-card bg-navy h-100">
                    <i class="bi bi-person-workspace"></i>
                    <div>
                        <div class="stat-num"><?= $stats['teachers'] ?></div>
                        <div class="stat-label">Guru Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <div class="stat-card bg-forest h-100">
                    <i class="bi bi-people-fill"></i>
                    <div>
                        <div class="stat-num"><?= $stats['players'] ?></div>
                        <div class="stat-label">Pemain Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <div class="stat-card bg-amber h-100">
                    <i class="bi bi-collection-play-fill"></i>
                    <div>
                        <div class="stat-num"><?= $stats['sessions'] ?></div>
                        <div class="stat-label">Total Sesi</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <div class="stat-card bg-indigo h-100">
                    <i class="bi bi-book-fill"></i>
                    <div>
                        <div class="stat-num"><?= $stats['stories'] ?></div>
                        <div class="stat-label">Cerita Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg">
                <div class="stat-card bg-rose h-100">
                    <i class="bi bi-patch-question-fill"></i>
                    <div>
                        <div class="stat-num"><?= $stats['questions'] ?></div>
                        <div class="stat-label">Pertanyaan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightning-charge me-2 text-warning"></i>Aksi Cepat</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="users.php?action=add" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-person-plus me-1"></i>Tambah Guru
                    </a>
                    <a href="players-manage.php?action=add" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-person-badge me-1"></i>Tambah Pemain
                    </a>
                    <a href="../teacher/analytics.php" class="btn btn-outline-secondary btn-sm" target="_blank">
                        <i class="bi bi-bar-chart me-1"></i>Lihat Analytics
                    </a>
                    <a href="../health.php" class="btn btn-outline-info btn-sm" target="_blank">
                        <i class="bi bi-heart-pulse me-1"></i>Health Check
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Recent Sessions -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom fw-semibold">
                        <i class="bi bi-collection-play text-primary me-2"></i>Sesi Terakhir
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($recentSessions)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Belum ada sesi
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Guru</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($recentSessions as $s): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($s['session_code']) ?></code></td>
                                    <td class="text-truncate" style="max-width:120px">
                                        <?= htmlspecialchars($s['teacher_name'] ?? '—') ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= ucfirst(htmlspecialchars($s['class_group'])) ?>
                                        </span>
                                    </td>
                                    <td><?= statusBadge($s['status']) ?></td>
                                    <td class="text-muted small">
                                        <?= date('d/m H:i', strtotime($s['created_at'])) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <a href="../teacher/sessions.php" class="btn btn-link btn-sm" target="_blank">
                            Lihat semua →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Staff List -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom fw-semibold">
                        <i class="bi bi-people text-success me-2"></i>Akun Staff
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($recentStaff)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-person-x fs-2 d-block mb-2"></i>
                            Belum ada data staff
                        </div>
                        <?php else: ?>
                        <ul class="list-group list-group-flush">
                        <?php foreach ($recentStaff as $st): ?>
                            <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                                <div>
                                    <div class="fw-semibold small"><?= htmlspecialchars($st['name']) ?></div>
                                    <div class="text-muted" style="font-size:.75rem">
                                        <?= htmlspecialchars($st['email']) ?>
                                    </div>
                                </div>
                                <div class="d-flex gap-1 align-items-center">
                                    <span class="badge bg-<?= $st['role'] === 'admin' ? 'dark' : 'primary' ?>">
                                        <?= $st['role'] ?>
                                    </span>
                                    <?php if (!$st['is_active']): ?>
                                    <span class="badge bg-danger">nonaktif</span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <a href="users.php" class="btn btn-link btn-sm">
                            Kelola staff →
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /.admin-content -->
</div><!-- /.admin-main -->

</body>
</html>