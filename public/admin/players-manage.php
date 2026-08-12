<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

if (!isset($_SESSION['staff_id']) || ($_SESSION['staff_role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

$admin  = getStaffUser();
$action = $_GET['action'] ?? 'list';
$msg    = '';
$error  = '';

// ── Handle POST ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $act = $_POST['act'] ?? '';

    // ADD PLAYER
    if ($act === 'add') {
        $nickname    = trim($_POST['nickname'] ?? '');
        $pin         = trim($_POST['pin'] ?? '');
        $class_group = in_array($_POST['class_group'] ?? '', ['small','medium','large'])
                       ? $_POST['class_group'] : 'medium';

        if (!$nickname || !$pin) {
            $error  = 'Nama panggilan dan PIN wajib diisi.';
            $action = 'add';
        } elseif (!preg_match('/^\d{4,6}$/', $pin)) {
            $error  = 'PIN harus 4–6 digit angka.';
            $action = 'add';
        } else {
            try {
                $code = generatePlayerCode();
                $hash = password_hash($pin, PASSWORD_DEFAULT);
                $pdo->prepare("
                    INSERT INTO players (player_code, nickname, pin_hash, class_group, created_by)
                    VALUES (?,?,?,?,?)
                ")->execute([$code, $nickname, $hash, $class_group, $admin['id']]);
                $msg    = "Pemain <strong>" . htmlspecialchars($nickname) . "</strong> ditambahkan dengan kode <code>{$code}</code>.";
                $action = 'list';
            } catch (PDOException $e) {
                $error  = 'Gagal menyimpan: ' . $e->getMessage();
                $action = 'add';
            }
        }
    }

    // RESET PIN
    if ($act === 'reset_pin') {
        $uid = (int)($_POST['uid'] ?? 0);
        $pin = trim($_POST['new_pin'] ?? '');
        if ($uid && preg_match('/^\d{4,6}$/', $pin)) {
            $hash = password_hash($pin, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE players SET pin_hash=? WHERE id=?")->execute([$hash, $uid]);
            $msg = 'PIN pemain berhasil direset.';
        } else {
            $error = 'PIN harus 4–6 digit angka.';
        }
        $action = 'list';
    }

    // TOGGLE ACTIVE
    if ($act === 'toggle') {
        $uid = (int)($_POST['uid'] ?? 0);
        if ($uid) {
            $pdo->prepare("UPDATE players SET is_active = NOT is_active WHERE id=?")->execute([$uid]);
            $msg = 'Status pemain diperbarui.';
        }
        $action = 'list';
    }
}

// ── Fetch Players ─────────────────────────────────────────────────────────────
$players = [];
$search  = trim($_GET['q'] ?? '');
if ($pdo) {
    try {
        if ($search) {
            $stmt = $pdo->prepare("
                SELECT p.id, p.player_code, p.nickname, p.class_group, p.is_active, p.created_at,
                       su.name AS created_by_name
                FROM players p
                LEFT JOIN staff_users su ON su.id = p.created_by
                WHERE p.nickname LIKE ? OR p.player_code LIKE ?
                ORDER BY p.created_at DESC
            ");
            $stmt->execute(["%{$search}%", "%{$search}%"]);
        } else {
            $stmt = $pdo->query("
                SELECT p.id, p.player_code, p.nickname, p.class_group, p.is_active, p.created_at,
                       su.name AS created_by_name
                FROM players p
                LEFT JOIN staff_users su ON su.id = p.created_by
                ORDER BY p.created_at DESC
            ");
        }
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}

$classLabel = ['small' => 'Small (SD 1–2)', 'medium' => 'Medium (SD 3–4)', 'large' => 'Large (SD 5–6)'];
$classBadge = ['small' => 'info',           'medium' => 'primary',          'large' => 'dark'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemain - Admin Bible Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <style>
        :root { --admin-dark: #1e3a5f; }
        body { background: #f0f2f5; }
        .admin-sidebar {
            width: 240px; min-height: 100vh;
            background: var(--admin-dark); color: #fff;
            position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column; z-index: 100;
        }
        .sidebar-brand { padding: 1.5rem 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,.1); }
        .sidebar-brand h5 { font-size: 1rem; margin: 0; }
        .sidebar-nav { flex: 1; padding: .5rem 0; }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.75); padding: .6rem 1.25rem;
            display: flex; align-items: center; gap: .6rem; font-size: .9rem;
        }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,.12); color: #fff;
        }
        .sidebar-footer { padding: 1rem 1.25rem; border-top: 1px solid rgba(255,255,255,.1); font-size: .8rem; opacity: .6; }
        .admin-main { margin-left: 240px; }
        .admin-topbar {
            background: #fff; border-bottom: 1px solid #dee2e6;
            padding: .75rem 1.5rem; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 50;
        }
        .admin-content { padding: 1.5rem; }
        @media (max-width: 768px) { .admin-sidebar { display: none; } .admin-main { margin-left: 0; } }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="admin-sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-shield-lock-fill fs-4"></i>
            <h5>Bible Adventure</h5>
        </div>
        <small>Admin Portal</small>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-link"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        <a href="users.php" class="nav-link"><i class="bi bi-people"></i> Kelola Staff</a>
        <a href="players-manage.php" class="nav-link active"><i class="bi bi-person-badge"></i> Kelola Pemain</a>
        <hr style="border-color:rgba(255,255,255,.15);margin:.5rem 1rem;">
        <a href="../teacher/sessions.php" class="nav-link" target="_blank"><i class="bi bi-collection-play"></i> Riwayat Sesi</a>
        <a href="../teacher/analytics.php" class="nav-link" target="_blank"><i class="bi bi-bar-chart"></i> Analytics</a>
        <a href="../health.php" class="nav-link" target="_blank"><i class="bi bi-heart-pulse"></i> Health Check</a>
    </nav>
    <div class="sidebar-footer">GKJ Tangerang &copy; <?= date('Y') ?></div>
</div>

<!-- Main -->
<div class="admin-main">
    <div class="admin-topbar">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-person-badge me-2 text-secondary"></i>Kelola Pemain
        </h6>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small"><i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($admin['name']) ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>

    <div class="admin-content">

        <?php if ($msg): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i><?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($action === 'add'): ?>
        <!-- ── ADD PLAYER FORM ── -->
        <div class="card border-0 shadow-sm" style="max-width:480px">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-badge me-2 text-success"></i>Tambah Pemain Terdaftar
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="act" value="add">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Panggilan</label>
                        <input type="text" name="nickname" class="form-control"
                               value="<?= htmlspecialchars($_POST['nickname'] ?? '') ?>"
                               placeholder="Contoh: Budi" required maxlength="50">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">PIN <small class="text-muted">(4–6 digit angka)</small></label>
                        <input type="text" name="pin" class="form-control"
                               value=""
                               placeholder="Contoh: 1234"
                               pattern="\d{4,6}" inputmode="numeric" required>
                        <div class="form-text">PIN digunakan pemain untuk login di perangkat mereka.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Kelompok Kelas</label>
                        <select name="class_group" class="form-select">
                            <option value="small"  <?= ($_POST['class_group'] ?? '') === 'small'  ? 'selected' : '' ?>>Small — SD Kelas 1–2</option>
                            <option value="medium" <?= ($_POST['class_group'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium — SD Kelas 3–4</option>
                            <option value="large"  <?= ($_POST['class_group'] ?? '') === 'large'  ? 'selected' : '' ?>>Large — SD Kelas 5–6</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                        <a href="players-manage.php" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        <?php else: ?>
        <!-- ── PLAYER LIST ── -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="fw-bold mb-0">
                Daftar Pemain
                <span class="badge bg-secondary ms-1"><?= count($players) ?></span>
            </h5>
            <div class="d-flex gap-2">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="q" class="form-control form-control-sm"
                           placeholder="Cari nama / kode..." value="<?= htmlspecialchars($search) ?>" style="width:180px">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if ($search): ?>
                    <a href="players-manage.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i>
                    </a>
                    <?php endif; ?>
                </form>
                <a href="players-manage.php?action=add" class="btn btn-success btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Tambah Pemain
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php if (empty($players)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                    <?= $search ? 'Tidak ditemukan hasil untuk "' . htmlspecialchars($search) . '"' : 'Belum ada pemain terdaftar.' ?>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Dibuat oleh</th>
                                <th>Tanggal</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($players as $i => $pl): ?>
                        <tr class="<?= !$pl['is_active'] ? 'table-secondary' : '' ?>">
                            <td class="text-muted small"><?= $i + 1 ?></td>
                            <td><code class="small"><?= htmlspecialchars($pl['player_code']) ?></code></td>
                            <td class="fw-semibold"><?= htmlspecialchars($pl['nickname']) ?></td>
                            <td>
                                <span class="badge bg-<?= $classBadge[$pl['class_group']] ?? 'secondary' ?>">
                                    <?= ucfirst($pl['class_group']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($pl['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small">
                                <?= htmlspecialchars($pl['created_by_name'] ?? '—') ?>
                            </td>
                            <td class="text-muted small">
                                <?= date('d/m/Y', strtotime($pl['created_at'])) ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Reset PIN -->
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalPin"
                                            data-uid="<?= $pl['id'] ?>"
                                            data-name="<?= htmlspecialchars($pl['nickname']) ?>"
                                            title="Reset PIN">
                                        <i class="bi bi-123"></i>
                                    </button>

                                    <!-- Toggle Aktif -->
                                    <form method="POST" class="d-inline"
                                          onsubmit="return confirm('<?= $pl['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?> pemain ini?')">
                                        <input type="hidden" name="act" value="toggle">
                                        <input type="hidden" name="uid" value="<?= $pl['id'] ?>">
                                        <button type="submit"
                                                class="btn btn-sm <?= $pl['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                                title="<?= $pl['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="bi bi-<?= $pl['is_active'] ? 'person-dash' : 'person-check' ?>"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- Modal Reset PIN -->
<div class="modal fade" id="modalPin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="act" value="reset_pin">
                <input type="hidden" name="uid" id="pinUid">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-123 me-2 text-warning"></i>Reset PIN Pemain
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Reset PIN untuk: <strong id="pinName"></strong></p>
                    <label class="form-label fw-semibold">PIN Baru <small class="text-muted">(4–6 digit)</small></label>
                    <input type="text" name="new_pin" class="form-control"
                           placeholder="Contoh: 1234"
                           pattern="\d{4,6}" inputmode="numeric" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-1"></i>Reset PIN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('modalPin')?.addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('pinUid').value       = btn.dataset.uid;
    document.getElementById('pinName').textContent = btn.dataset.name;
});
</script>
</body>
</html>