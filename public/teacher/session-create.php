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

$errors  = [];
$success = '';
$newSessionCode = '';

// Load stories from DB
$stories = [];
if ($pdo) {
    $stmt = $pdo->query("SELECT slug, title, reference FROM stories WHERE is_active = TRUE ORDER BY story_order ASC");
    $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $classGroup    = $_POST['class_group'] ?? '';
    $storyId       = trim($_POST['story_id'] ?? '');
    $teamCount     = (int)($_POST['team_count'] ?? 2);
    $teamNamesRaw  = $_POST['team_names'] ?? [];

    $allowedClasses = ['small', 'medium', 'large'];

    if (!in_array($classGroup, $allowedClasses, true)) $errors[] = 'Pilih kelompok kelas yang valid.';
    if ($storyId === '') $errors[] = 'Pilih cerita untuk sesi ini.';
    if ($teamCount < 2 || $teamCount > 6) $errors[] = 'Jumlah tim harus antara 2 dan 6.';

    // Validate story exists
    if ($storyId !== '' && $pdo) {
        $storyCheck = $pdo->prepare("SELECT slug FROM stories WHERE slug = ? AND is_active = 1");
        $storyCheck->execute([$storyId]);
        if (!$storyCheck->fetch()) $errors[] = 'Cerita tidak ditemukan atau tidak aktif.';
    }

    // Build team names
    $teamNames = [];
    $defaultNames = ['Merah', 'Biru', 'Hijau', 'Kuning', 'Ungu', 'Oranye'];
    $teamColors   = ['#ef4444', '#3b82f6', '#22c55e', '#eab308', '#a855f7', '#f97316'];
    for ($i = 0; $i < $teamCount; $i++) {
        $name = trim($teamNamesRaw[$i] ?? '');
        $teamNames[] = $name !== '' ? $name : ('Tim ' . $defaultNames[$i]);
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Generate unique session code
            do {
                $sessionCode = generateSessionCode();
                $codeCheck = $pdo->prepare("SELECT id FROM game_sessions WHERE session_code = ?");
                $codeCheck->execute([$sessionCode]);
            } while ($codeCheck->fetch());

            // Count questions for the story
            $qCount = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE story_id = (SELECT id FROM stories WHERE slug = ?) AND is_active = 1");
            $qCount->execute([$storyId]);
            $totalQuestions = (int)$qCount->fetchColumn();

            // Insert session
            $insertSession = $pdo->prepare("
                INSERT INTO game_sessions (session_code, teacher_id, class_group, story_id, play_mode, status, total_questions)
                VALUES (?, ?, ?, ?, 'team', 'lobby', ?)
            ");
            $insertSession->execute([$sessionCode, $staff['id'], $classGroup, $storyId, $totalQuestions]);
            $sessionId = (int)$pdo->lastInsertId();

            // Insert teams
            $insertTeam = $pdo->prepare("
                INSERT INTO game_teams (session_id, name, color, order_index) VALUES (?, ?, ?, ?)
            ");
            foreach ($teamNames as $idx => $teamName) {
                $insertTeam->execute([$sessionId, $teamName, $teamColors[$idx] ?? '#999', $idx]);
            }

            $pdo->commit();
            $newSessionCode = $sessionCode;
            $success = true;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Gagal membuat sesi. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Sesi Baru - Bible Adventure</title>
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

<div class="container my-4" style="max-width: 700px;">
    <div class="mb-4">
        <a href="dashboard.php" class="text-decoration-none text-muted small">
            <i class="bi bi-chevron-left me-1"></i>Dashboard
        </a>
        <h2 class="mb-0 mt-1">Buat Sesi Baru</h2>
    </div>

    <?php if ($success && $newSessionCode): ?>
    <!-- Success State -->
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <div class="display-1 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
            <h3 class="fw-bold mb-2">Sesi Berhasil Dibuat!</h3>
            <p class="text-muted mb-4">Bagikan kode berikut kepada pemain untuk bergabung:</p>

            <div class="bg-light rounded-3 p-4 mb-4 d-inline-block w-100">
                <div class="text-muted small mb-1">Kode Sesi</div>
                <div class="display-4 fw-bold text-primary letter-spacing-wide" id="sessionCodeDisplay">
                    <?= h($newSessionCode) ?>
                </div>
                <button class="btn btn-outline-primary btn-sm mt-2" onclick="copyCode()">
                    <i class="bi bi-clipboard me-1"></i>Salin Kode
                </button>
            </div>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="sessions.php" class="btn btn-outline-primary">
                    <i class="bi bi-list-check me-2"></i>Lihat Semua Sesi
                </a>
                <a href="session-create.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Buat Sesi Lagi
                </a>
            </div>
        </div>
    </div>

    <script>
    function copyCode() {
        navigator.clipboard.writeText('<?= h($newSessionCode) ?>').then(() => {
            const btn = document.querySelector('#sessionCodeDisplay + button');
            btn.innerHTML = '<i class="bi bi-check me-1"></i>Tersalin!';
            setTimeout(() => btn.innerHTML = '<i class="bi bi-clipboard me-1"></i>Salin Kode', 2000);
        });
    }
    </script>

    <?php else: ?>
    <!-- Create Form -->

    <?php if (!$pdo): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Database tidak terhubung. Pastikan MySQL berjalan.
    </div>
    <?php endif; ?>

    <?php if ($errors): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="post" class="card shadow-sm">
        <div class="card-body">

            <!-- Story Selection -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Cerita <span class="text-danger">*</span></label>
                <?php if (empty($stories)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Belum ada cerita tersedia. Import schema dan data cerita terlebih dahulu.
                </div>
                <input type="hidden" name="story_id" value="">
                <?php else: ?>
                <select name="story_id" class="form-select" required>
                    <option value="">-- Pilih Cerita --</option>
                    <?php foreach ($stories as $story): ?>
                    <option value="<?= h($story['slug']) ?>"
                        <?= ($_POST['story_id'] ?? '') === $story['slug'] ? 'selected' : '' ?>>
                        <?= h($story['title']) ?>
                        <?php if ($story['reference']): ?> — <?= h($story['reference']) ?><?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Cerita yang akan dimainkan dalam sesi ini.</div>
                <?php endif; ?>
            </div>

            <!-- Class Group -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Kelompok Kelas <span class="text-danger">*</span></label>
                <div class="row g-2">
                    <?php
                    $classOptions = [
                        'small'  => ['label' => 'Small', 'desc' => 'Kelas Kecil', 'icon' => 'bi-emoji-smile'],
                        'medium' => ['label' => 'Medium', 'desc' => 'Kelas Sedang', 'icon' => 'bi-people'],
                        'large'  => ['label' => 'Large', 'desc' => 'Kelas Besar', 'icon' => 'bi-people-fill'],
                    ];
                    $selectedClass = $_POST['class_group'] ?? 'medium';
                    foreach ($classOptions as $val => $opt):
                    ?>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="class_group" id="class_<?= $val ?>"
                               value="<?= $val ?>" <?= $selectedClass === $val ? 'checked' : '' ?> required>
                        <label class="btn btn-outline-secondary w-100 py-3" for="class_<?= $val ?>">
                            <i class="bi <?= $opt['icon'] ?> d-block fs-4 mb-1"></i>
                            <strong><?= $opt['label'] ?></strong><br>
                            <small class="text-muted"><?= $opt['desc'] ?></small>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Team Count -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Jumlah Tim</label>
                <div class="d-flex align-items-center gap-3">
                    <input type="range" class="form-range flex-grow-1" id="teamCountRange"
                           name="team_count" min="2" max="6"
                           value="<?= (int)($_POST['team_count'] ?? 2) ?>"
                           oninput="updateTeamCount(this.value)">
                    <span class="badge bg-primary fs-6 px-3" id="teamCountBadge">
                        <?= (int)($_POST['team_count'] ?? 2) ?>
                    </span>
                </div>
                <div class="form-text">2–6 tim per sesi.</div>
            </div>

            <!-- Team Names -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Nama Tim</label>
                <div id="teamNamesContainer">
                    <?php
                    $defaultNames = ['Merah', 'Biru', 'Hijau', 'Kuning', 'Ungu', 'Oranye'];
                    $savedCount   = (int)($_POST['team_count'] ?? 2);
                    $savedNames   = $_POST['team_names'] ?? [];
                    for ($i = 0; $i < 6; $i++):
                    ?>
                    <div class="input-group mb-2 team-name-row" id="teamRow<?= $i ?>"
                         style="display: <?= $i < $savedCount ? 'flex' : 'none' ?>;">
                        <span class="input-group-text">Tim <?= $i + 1 ?></span>
                        <input type="text" class="form-control" name="team_names[]"
                               maxlength="30"
                               placeholder="Tim <?= $defaultNames[$i] ?>"
                               value="<?= h($savedNames[$i] ?? '') ?>">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

        </div>
        <div class="card-footer bg-white d-flex gap-3 justify-content-end">
            <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" <?= !$pdo ? 'disabled' : '' ?>>
                <i class="bi bi-play-circle me-2"></i>Buat Sesi
            </button>
        </div>
    </form>

    <script>
    function updateTeamCount(count) {
        count = parseInt(count);
        document.getElementById('teamCountBadge').textContent = count;
        for (let i = 0; i < 6; i++) {
            const row = document.getElementById('teamRow' + i);
            if (row) row.style.display = i < count ? 'flex' : 'none';
        }
    }
    // Init on load
    updateTeamCount(document.getElementById('teamCountRange').value);
    </script>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>