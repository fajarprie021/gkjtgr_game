<?php
require_once '../../config/security.php';
sendSecurityHeaders([
    'csp' => "default-src 'self' https:; img-src 'self' https: data:; style-src 'self' 'unsafe-inline' https:; script-src 'self' 'unsafe-inline' https:; font-src 'self' https: data:; connect-src 'self' https:; frame-ancestors 'self';"
]);
require_once '../../config/database.php';
require_once '../../config/auth.php';

requireStaffAuth();
$staff = getStaffUser();

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$errors = [];
$success = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nickname  = trim($_POST['nickname'] ?? '');
        $classGroup = $_POST['class_group'] ?? '';
        $pin       = trim($_POST['pin'] ?? '');
        $pinConfirm = trim($_POST['pin_confirm'] ?? '');

        $allowedClasses = ['small', 'medium', 'large'];

        if ($nickname === '') $errors[] = 'Nama/nickname wajib diisi.';
        if (!in_array($classGroup, $allowedClasses, true)) $errors[] = 'Kelompok kelas tidak valid.';
        if (strlen($pin) < 4) $errors[] = 'PIN minimal 4 digit.';
        if ($pin !== $pinConfirm) $errors[] = 'Konfirmasi PIN tidak cocok.';

        if (empty($errors)) {
            $playerCode = generatePlayerCode();
            // Ensure unique
            $check = $pdo->prepare("SELECT id FROM players WHERE player_code = ?");
            $check->execute([$playerCode]);
            while ($check->fetch()) {
                $playerCode = generatePlayerCode();
                $check->execute([$playerCode]);
            }

            $pinHash = password_hash($pin, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("
                INSERT INTO players (player_code, nickname, pin_hash, class_group, created_by)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$playerCode, $nickname, $pinHash, $classGroup, $staff['id']]);
            $success = "Pemain <strong>" . h($nickname) . "</strong> berhasil dibuat dengan kode <code>" . h($playerCode) . "</code>.";
        }

    } elseif ($action === 'toggle' && $staff['role'] === 'admin') {
        $playerId = (int)($_POST['player_id'] ?? 0);
        if ($playerId > 0) {
            $pdo->prepare("UPDATE players SET is_active = NOT is_active WHERE id = ?")->execute([$playerId]);
        }
        header('Location: players.php');
        exit;

    } elseif ($action === 'reset_pin' && $staff['role'] === 'admin') {
        $playerId = (int)($_POST['player_id'] ?? 0);
        $newPin   = trim($_POST['new_pin'] ?? '');
        if ($playerId > 0 && strlen($newPin) >= 4) {
            $pinHash = password_hash($newPin, PASSWORD_BCRYPT);
            $pdo->prepare("UPDATE players SET pin_hash = ? WHERE id = ?")->execute([$pinHash, $playerId]);
            $success = 'PIN pemain berhasil direset.';
        } else {
            $errors[] = 'PIN minimal 4 digit.';
        }
    }
}

// Filters
$classFilter  = $_GET['class_group'] ?? '';
$activeFilter = $_GET['active'] ?? '';
$search       = trim($_GET['q'] ?? '');

$allowedClasses = ['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'];
$where  = [];
$params = [];

if ($classFilter !== '' && isset($allowedClasses[$classFilter])) {
    $where[] = 'class_group = ?';
    $params[] = $classFilter;
}
if ($activeFilter === '1') { $where[] = 'is_active = 1'; }
if ($activeFilter === '0') { $where[] = 'is_active = 0'; }
if ($search !== '') {
    $where[] = '(nickname LIKE ? OR player_code LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$players = [];
if ($pdo) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.player_code, p.nickname, p.class_group, p.is_active,
               p.created_at, su.name AS created_by_name,
               COUNT(DISTINCT psp.story_id) AS stories_completed
        FROM players p
        LEFT JOIN staff_users su ON su.id = p.created_by
        LEFT JOIN player_story_progress psp ON psp.player_id = p.id AND psp.status = 'completed'
        $whereSql
        GROUP BY p.id
        ORDER BY p.created_at DESC
        LIMIT 100
    ");
    $stmt->execute($params);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemain - Bible Adventure</title>
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
                <i class="bi bi-person-circle me-1"></i><?= h($staff['name']) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="dashboard.php" class="text-decoration-none text-muted small">
                <i class="bi bi-chevron-left me-1"></i>Dashboard
            </a>
            <h2 class="mb-0 mt-1">Kelola Pemain</h2>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlayerModal">
            <i class="bi bi-person-plus me-2"></i>Tambah Pemain
        </button>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($errors): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Filter -->
    <form class="card mb-4" method="get">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <input type="text" name="q" class="form-control" value="<?= h($search) ?>" placeholder="Nama atau kode...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kelompok Kelas</label>
                    <select name="class_group" class="form-select">
                        <option value="">Semua Kelas</option>
                        <?php foreach ($allowedClasses as $val => $lbl): ?>
                        <option value="<?= h($val) ?>" <?= $classFilter === $val ? 'selected' : '' ?>><?= h($lbl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="active" class="form-select">
                        <option value="">Semua</option>
                        <option value="1" <?= $activeFilter === '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= $activeFilter === '0' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Cari</button>
                    <a href="players.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Players Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>Daftar Pemain (<?= count($players) ?>)</strong>
        </div>
        <?php if (!$pdo): ?>
        <div class="card-body">
            <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Database tidak terhubung. Pastikan MySQL berjalan.
            </div>
        </div>
        <?php elseif (empty($players)): ?>
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-people display-4 d-block mb-3"></i>
            Belum ada pemain<?= $search ? ' yang cocok dengan pencarian "' . h($search) . '"' : '' ?>.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Story Selesai</th>
                        <th>Dibuat Oleh</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <?php if ($staff['role'] === 'admin'): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $p): ?>
                    <tr class="<?= $p['is_active'] ? '' : 'table-secondary text-muted' ?>">
                        <td><code><?= h($p['player_code']) ?></code></td>
                        <td><?= h($p['nickname']) ?></td>
                        <td><span class="badge bg-secondary"><?= ucfirst(h($p['class_group'])) ?></span></td>
                        <td><?= (int)$p['stories_completed'] ?></td>
                        <td><small><?= h($p['created_by_name'] ?? '-') ?></small></td>
                        <td>
                            <span class="badge bg-<?= $p['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td><small><?= h(date('d/m/Y', strtotime($p['created_at']))) ?></small></td>
                        <?php if ($staff['role'] === 'admin'): ?>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="player_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-<?= $p['is_active'] ? 'warning' : 'success' ?>">
                                    <?= $p['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </button>
                            </form>
                            <button class="btn btn-sm btn-outline-secondary ms-1"
                                data-bs-toggle="modal" data-bs-target="#resetPinModal"
                                data-player-id="<?= (int)$p['id'] ?>"
                                data-player-name="<?= h($p['nickname']) ?>">
                                <i class="bi bi-key me-1"></i>Reset PIN
                            </button>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Player Modal -->
<div class="modal fade" id="createPlayerModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="action" value="create">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Pemain Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama / Nickname <span class="text-danger">*</span></label>
                    <input type="text" name="nickname" class="form-control" maxlength="50" required placeholder="Contoh: Samuel">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kelompok Kelas <span class="text-danger">*</span></label>
                    <select name="class_group" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <option value="small">Small (Kecil)</option>
                        <option value="medium">Medium (Sedang)</option>
                        <option value="large">Large (Besar)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">PIN <span class="text-danger">*</span></label>
                    <input type="password" name="pin" class="form-control" minlength="4" maxlength="8" required placeholder="Min. 4 digit">
                    <div class="form-text">PIN digunakan pemain untuk login.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Konfirmasi PIN <span class="text-danger">*</span></label>
                    <input type="password" name="pin_confirm" class="form-control" minlength="4" maxlength="8" required placeholder="Ulangi PIN">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-check me-1"></i>Buat Pemain
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reset PIN Modal (admin only) -->
<?php if ($staff['role'] === 'admin'): ?>
<div class="modal fade" id="resetPinModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="post" class="modal-content">
            <input type="hidden" name="action" value="reset_pin">
            <input type="hidden" name="player_id" id="resetPinPlayerId">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-key me-2"></i>Reset PIN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Reset PIN untuk pemain: <strong id="resetPinPlayerName"></strong></p>
                <div class="mb-3">
                    <label class="form-label">PIN Baru</label>
                    <input type="password" name="new_pin" class="form-control" minlength="4" maxlength="8" required placeholder="Min. 4 digit">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning">Reset PIN</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Populate reset PIN modal
document.getElementById('resetPinModal')?.addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('resetPinPlayerId').value = btn.dataset.playerId;
    document.getElementById('resetPinPlayerName').textContent = btn.dataset.playerName;
});
</script>
</body>
</html>