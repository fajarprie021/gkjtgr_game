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

// Handle session actions (end/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $action = $_POST['action'] ?? '';
    $sessionId = (int)($_POST['session_id'] ?? 0);

    if ($action === 'end' && $sessionId > 0) {
        $stmt = $pdo->prepare("UPDATE game_sessions SET status='completed', completed_at=NOW() WHERE id=? AND teacher_id=?");
        $stmt->execute([$sessionId, $staff['id']]);
    } elseif ($action === 'delete' && $sessionId > 0 && $staff['role'] === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM game_sessions WHERE id=?");
        $stmt->execute([$sessionId]);
    }
    header('Location: sessions.php');
    exit;
}

// Filters
$statusFilter = $_GET['status'] ?? '';
$allowedStatuses = ['lobby', 'active', 'paused', 'completed'];

$where = [];
$params = [];

// Teachers only see their own sessions unless admin
if ($staff['role'] !== 'admin') {
    $where[] = 'gs.teacher_id = ?';
    $params[] = $staff['id'];
}
if ($statusFilter !== '' && in_array($statusFilter, $allowedStatuses, true)) {
    $where[] = 'gs.status = ?';
    $params[] = $statusFilter;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sessions = [];
if ($pdo) {
    $stmt = $pdo->prepare("
        SELECT gs.id, gs.session_code, gs.class_group, gs.story_id, gs.play_mode,
               gs.status, gs.current_question_index, gs.total_questions,
               gs.created_at, gs.started_at, gs.completed_at,
               su.name AS teacher_name,
               COUNT(DISTINCT gt.id) AS team_count,
               COUNT(DISTINCT gp.id) AS player_count
        FROM game_sessions gs
        JOIN staff_users su ON su.id = gs.teacher_id
        LEFT JOIN game_teams gt ON gt.session_id = gs.id
        LEFT JOIN game_players gp ON gp.session_id = gs.id
        $whereSql
        GROUP BY gs.id
        ORDER BY gs.created_at DESC
        LIMIT 50
    ");
    $stmt->execute($params);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$statusBadge = [
    'lobby'     => 'secondary',
    'active'    => 'success',
    'paused'    => 'warning',
    'completed' => 'primary',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Sesi - Bible Adventure</title>
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
            <h2 class="mb-0 mt-1">Riwayat Sesi</h2>
        </div>
        <a href="session-create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Buat Sesi Baru
        </a>
    </div>

    <!-- Filter -->
    <form class="card mb-4" method="get">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <?php foreach ($allowedStatuses as $s): ?>
                        <option value="<?= h($s) ?>" <?= $statusFilter === $s ? 'selected' : '' ?>>
                            <?= ucfirst(h($s)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="sessions.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Sessions Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Daftar Sesi (<?= count($sessions) ?>)</strong>
        </div>
        <?php if (!$pdo): ?>
        <div class="card-body">
            <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Database tidak terhubung. Pastikan MySQL berjalan.
            </div>
        </div>
        <?php elseif (empty($sessions)): ?>
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-inbox display-4 d-block mb-3"></i>
            Belum ada sesi<?= $statusFilter ? ' dengan status "' . h($statusFilter) . '"' : '' ?>.
            <div class="mt-3">
                <a href="session-create.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Buat Sesi Pertama
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Story</th>
                        <th>Tim / Pemain</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $row): ?>
                    <tr>
                        <td><code class="fs-6"><?= h($row['session_code']) ?></code></td>
                        <td><?= h($row['teacher_name']) ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= ucfirst(h($row['class_group'])) ?></span>
                        </td>
                        <td><small class="text-muted"><?= h($row['story_id']) ?></small></td>
                        <td>
                            <i class="bi bi-people me-1 text-muted"></i><?= (int)$row['team_count'] ?> tim /
                            <?= (int)$row['player_count'] ?> pemain
                        </td>
                        <td>
                            <span class="badge bg-<?= $statusBadge[$row['status']] ?? 'secondary' ?>">
                                <?= ucfirst(h($row['status'])) ?>
                            </span>
                        </td>
                        <td><small><?= h(date('d/m/Y H:i', strtotime($row['created_at']))) ?></small></td>
                        <td>
                            <?php if (in_array($row['status'], ['lobby','active','paused'], true)): ?>
                            <form method="post" class="d-inline" onsubmit="return confirm('Akhiri sesi ini?')">
                                <input type="hidden" name="action" value="end">
                                <input type="hidden" name="session_id" value="<?= (int)$row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-stop-circle me-1"></i>Akhiri
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php if ($staff['role'] === 'admin'): ?>
                            <form method="post" class="d-inline" onsubmit="return confirm('Hapus sesi ini? Semua data tim dan jawaban akan terhapus.')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="session_id" value="<?= (int)$row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger ms-1">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>