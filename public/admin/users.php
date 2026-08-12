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

// ── Handle POST ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $act = $_POST['act'] ?? '';

    // ADD STAFF
    if ($act === 'add') {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = in_array($_POST['role'] ?? '', ['admin', 'teacher']) ? $_POST['role'] : 'teacher';

        if (!$name || !$email || !$password) {
            $error = 'Nama, email, dan password wajib diisi.';
            $action = 'add';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter.';
            $action = 'add';
        } else {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO staff_users (name, email, password_hash, role) VALUES (?,?,?,?)");
                $stmt->execute([$name, $email, $hash, $role]);
                $msg    = "Akun {$role} <strong>" . htmlspecialchars($name) . "</strong> berhasil ditambahkan.";
                $action = 'list';
            } catch (PDOException $e) {
                $error  = str_contains($e->getMessage(), 'Duplicate') ? 'Email sudah terdaftar.' : 'Gagal menyimpan: ' . $e->getMessage();
                $action = 'add';
            }
        }
    }

    // RESET PASSWORD
    if ($act === 'reset_pw') {
        $uid      = (int)($_POST['uid'] ?? 0);
        $password = $_POST['new_password'] ?? '';
        if ($uid && strlen($password) >= 6) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE staff_users SET password_hash=? WHERE id=?")->execute([$hash, $uid]);
            $msg = 'Password berhasil direset.';
        } else {
            $error = 'Password minimal 6 karakter.';
        }
        $action = 'list';
    }

    // TOGGLE ACTIVE
    if ($act === 'toggle') {
        $uid = (int)($_POST['uid'] ?? 0);
        if ($uid && $uid !== (int)$admin['id']) {
            $pdo->prepare("UPDATE staff_users SET is_active = NOT is_active WHERE id=?")->execute([$uid]);
            $msg = 'Status akun diperbarui.';
        } else {
            $error = 'Tidak dapat menonaktifkan akun sendiri.';
        }
        $action = 'list';
    }
}

// ── Fetch Staff List ──────────────────────────────────────────────────────────
$staffList = [];
if ($pdo) {
    try {
        $staffList = $pdo->query("SELECT id, name, email, role, is_active, created_at FROM staff_users ORDER BY role, name")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Staff - Admin Bible Adventure</title>
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
        <a href="users.php" class="nav-link active"><i class="bi bi-people"></i> Kelola Staff</a>
        <a href="players-manage.php" class="nav-link"><i class="bi bi-person-badge"></i> Kelola Pemain</a>
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
            <i class="bi bi-people me-2 text-secondary"></i>Kelola Staff
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
        <!-- ── ADD FORM ── -->
        <div class="card border-0 shadow-sm" style="max-width:520px">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-person-plus me-2 text-primary"></i>Tambah Akun Staff
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="act" value="add">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                               placeholder="Contoh: Maria Dewi" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               placeholder="maria@gkjtangerang.org" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Min. 6 karakter" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Peran</label>
                        <select name="role" class="form-select">
                            <option value="teacher" <?= ($_POST['role'] ?? 'teacher') === 'teacher' ? 'selected' : '' ?>>
                                Guru (Teacher)
                            </option>
                            <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                                Admin
                            </option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                        <a href="users.php" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        <?php else: ?>
        <!-- ── STAFF LIST ── -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Daftar Akun Staff</h5>
            <a href="users.php?action=add" class="btn btn-primary btn-sm">
                <i class="bi bi-person-plus me-1"></i>Tambah Staff
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php if (empty($staffList)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                    Belum ada data staff.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Peran</th>
                                <th>Status</th>
                                <th>Bergabung</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($staffList as $i => $st): ?>
                        <tr class="<?= !$st['is_active'] ? 'table-secondary' : '' ?>">
                            <td class="text-muted small"><?= $i + 1 ?></td>
                            <td class="fw-semibold">
                                <?= htmlspecialchars($st['name']) ?>
                                <?php if ($st['id'] == $admin['id']): ?>
                                <span class="badge bg-info ms-1">Anda</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?= htmlspecialchars($st['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= $st['role'] === 'admin' ? 'dark' : 'primary' ?>">
                                    <?= $st['role'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($st['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small">
                                <?= date('d/m/Y', strtotime($st['created_at'])) ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Reset Password -->
                                    <button class="btn btn-outline-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalReset"
                                            data-uid="<?= $st['id'] ?>"
                                            data-name="<?= htmlspecialchars($st['name']) ?>"
                                            title="Reset Password">
                                        <i class="bi bi-key"></i>
                                    </button>

                                    <!-- Toggle Aktif -->
                                    <?php if ($st['id'] != $admin['id']): ?>
                                    <form method="POST" class="d-inline"
                                          onsubmit="return confirm('<?= $st['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?> akun ini?')">
                                        <input type="hidden" name="act" value="toggle">
                                        <input type="hidden" name="uid" value="<?= $st['id'] ?>">
                                        <button type="submit"
                                                class="btn btn-sm <?= $st['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                                title="<?= $st['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="bi bi-<?= $st['is_active'] ? 'person-dash' : 'person-check' ?>"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
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

<!-- Modal Reset Password -->
<div class="modal fade" id="modalReset" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="act" value="reset_pw">
                <input type="hidden" name="uid" id="resetUid">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-key me-2 text-warning"></i>Reset Password
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Reset password untuk: <strong id="resetName"></strong></p>
                    <label class="form-label fw-semibold">Password Baru</label>
                    <input type="password" name="new_password" class="form-control"
                           placeholder="Min. 6 karakter" required minlength="6">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-key me-1"></i>Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('modalReset')?.addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('resetUid').value  = btn.dataset.uid;
    document.getElementById('resetName').textContent = btn.dataset.name;
});
</script>
</body>
</html>