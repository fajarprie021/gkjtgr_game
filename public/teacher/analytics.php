<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

requireStaffRole(['admin','teacher']);
$staff = getStaffUser();
$isAdmin = ($staff['role'] ?? 'teacher') === 'admin';

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function p($key,$default=''){ return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default; }

$classGroup = p('class_group');
$storyId = p('story_id');
$gameMode = p('game_mode');
$from = p('from');
$to = p('to');
$scope = p('scope', $isAdmin ? 'global' : 'staff');

$allowedClassGroups = ['small'=>'Small','medium'=>'Medium','large'=>'Large'];
$allowedGameModes = ['solo'=>'Solo','classroom'=>'Classroom'];

$filters = [];
$params = [];
if ($classGroup !== '' && isset($allowedClassGroups[$classGroup])) { $filters[] = 'class_group = ?'; $params[] = $classGroup; }
if ($storyId !== '') { $filters[] = 'story_id = ?'; $params[] = $storyId; }
if ($gameMode !== '' && isset($allowedGameModes[$gameMode])) { $filters[] = 'game_mode = ?'; $params[] = $gameMode; }
if ($from !== '') { $filters[] = 'created_at >= ?'; $params[] = $from . ' 00:00:00'; }
if ($to !== '') { $filters[] = 'created_at <= ?'; $params[] = $to . ' 23:59:59'; }
$whereSql = $filters ? 'WHERE ' . implode(' AND ', $filters) : '';
$filterPrefix = $whereSql ? $whereSql . ' AND ' : 'WHERE ';

$stmt = $pdo->prepare("SELECT story_id, COUNT(CASE WHEN event_type='story_started' THEN 1 END) times_played, COUNT(CASE WHEN event_type='story_completed' THEN 1 END) completions FROM analytics_events {$filterPrefix} story_id IS NOT NULL GROUP BY story_id ORDER BY times_played DESC LIMIT 10");
$stmt->execute($params);
$storyRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$metrics = $pdo->query("SELECT COUNT(CASE WHEN event_type='story_started' THEN 1 END) stories_started, COUNT(CASE WHEN event_type='story_completed' THEN 1 END) stories_completed, COUNT(DISTINCT CASE WHEN event_type IN ('story_started','story_completed') THEN player_id END) unique_players, ROUND(100*COUNT(CASE WHEN event_type='story_completed' THEN 1 END)/NULLIF(COUNT(CASE WHEN event_type='story_started' THEN 1 END),0),1) completion_rate, ROUND(100*SUM(CASE WHEN event_type='question_completed' AND result='correct' THEN 1 ELSE 0 END)/NULLIF(COUNT(CASE WHEN event_type='question_completed' THEN 1 END),0),1) question_correct_rate, COUNT(CASE WHEN event_type='session_started' THEN 1 END) sessions_started, COUNT(CASE WHEN event_type='session_completed' THEN 1 END) sessions_completed, COUNT(CASE WHEN event_type='technical_error' THEN 1 END) technical_errors FROM analytics_events")->fetch(PDO::FETCH_ASSOC) ?: [];

$mechanicRows = $pdo->query("SELECT COALESCE(question_type,'unknown') question_type, COUNT(*) attempts, ROUND(100*SUM(CASE WHEN result='correct' THEN 1 ELSE 0 END)/NULLIF(COUNT(*),0),1) correct_rate FROM analytics_events WHERE event_type='question_completed' GROUP BY question_type ORDER BY attempts DESC")->fetchAll(PDO::FETCH_ASSOC);
$recentSessions = $pdo->query("SELECT gs.session_code, su.name teacher_name, gs.class_group, gs.story_id, gs.status, gs.created_at FROM game_sessions gs JOIN staff_users su ON su.id = gs.teacher_id ORDER BY gs.created_at DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
$questionRows = $pdo->query("SELECT question_id, question_type, COUNT(*) attempts, ROUND(100*SUM(CASE WHEN result='correct' THEN 1 ELSE 0 END)/NULLIF(COUNT(*),0),1) correct_rate FROM analytics_events WHERE event_type='question_completed' AND question_id IS NOT NULL GROUP BY question_id, question_type ORDER BY attempts ASC, correct_rate ASC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
$recentEvents = $pdo->query("SELECT event_type, story_id, question_id, question_type, result, class_group, game_mode, created_at FROM analytics_events ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

$baseQuery = $_GET; unset($baseQuery['export']);
$sessionCsvUrl = '?' . http_build_query($baseQuery + ['export' => 'sessions']);
$questionCsvUrl = '?' . http_build_query($baseQuery + ['export' => 'questions']);
$eventCsvUrl = '?' . http_build_query($baseQuery + ['export' => 'events']);

if (isset($_GET['export']) && in_array($_GET['export'], ['sessions','questions','events'], true)) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="analytics-' . $_GET['export'] . '.csv"');
    $out = fopen('php://output', 'w');
    if ($_GET['export'] === 'sessions') { fputcsv($out, ['Session Code','Teacher','Class','Story','Status','Created At']); foreach ($recentSessions as $r) fputcsv($out, [$r['session_code'],$r['teacher_name'],$r['class_group'],$r['story_id'],$r['status'],$r['created_at']]); }
    elseif ($_GET['export'] === 'questions') { fputcsv($out, ['Question ID','Type','Attempts','Correct Rate']); foreach ($questionRows as $r) fputcsv($out, [$r['question_id'],$r['question_type'],$r['attempts'],$r['correct_rate']]); }
    else { fputcsv($out, ['Event Type','Story','Question','Type','Result','Class','Mode','Created At']); foreach ($recentEvents as $r) fputcsv($out, [$r['event_type'],$r['story_id'],$r['question_id'],$r['question_type'],$r['result'],$r['class_group'],$r['game_mode'],$r['created_at']]); }
    fclose($out); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Analytics - Bible Adventure</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/css/theme.css">
<link rel="stylesheet" href="../assets/css/components.css">
</head>
<body>
<div class="container py-4">
  <div class="card shadow-sm mb-4"><div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
    <div><span class="badge bg-primary-subtle text-primary mb-2">Staff Analytics</span><h1 class="h3 mb-1">Analytics & Learning Insights</h1><p class="text-muted mb-0">Ringkasan performa story, pertanyaan, sesi, dan error teknis.</p></div>
    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-outline-primary" href="<?= h($sessionCsvUrl) ?>"><i class="bi bi-download me-1"></i>CSV Session</a>
      <a class="btn btn-outline-primary" href="<?= h($questionCsvUrl) ?>"><i class="bi bi-download me-1"></i>CSV Question</a>
      <a class="btn btn-outline-primary" href="<?= h($eventCsvUrl) ?>"><i class="bi bi-download me-1"></i>CSV Events</a>
    </div>
  </div></div>

  <form class="card shadow-sm mb-4" method="get"><div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-md-3"><label class="form-label">Class Group</label><select name="class_group" class="form-select"><option value="">Semua</option><?php foreach ($allowedClassGroups as $value => $label): ?><option value="<?= h($value) ?>" <?= $classGroup===$value?'selected':'' ?>><?= h($label) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-3"><label class="form-label">Game Mode</label><select name="game_mode" class="form-select"><option value="">Semua</option><?php foreach ($allowedGameModes as $value => $label): ?><option value="<?= h($value) ?>" <?= $gameMode===$value?'selected':'' ?>><?= h($label) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-3"><label class="form-label">From</label><input type="date" name="from" class="form-control" value="<?= h($from) ?>"></div>
      <div class="col-md-3"><label class="form-label">To</label><input type="date" name="to" class="form-control" value="<?= h($to) ?>"></div>
      <div class="col-md-4"><label class="form-label">Story ID</label><input type="text" name="story_id" class="form-control" value="<?= h($storyId) ?>" placeholder="creation"></div>
      <div class="col-md-8 d-flex gap-2 justify-content-end"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Apply Filter</button><a class="btn btn-outline-secondary" href="analytics.php">Reset</a></div>
    </div>
    <?php if ($scope === 'global' && !$isAdmin): ?><div class="alert alert-warning mt-3 mb-0">Global scope hanya tersedia untuk admin.</div><?php endif; ?>
  </div></form>

  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Stories Started</div><div class="fs-3 fw-bold"><?= (int)($metrics['stories_started'] ?? 0) ?></div></div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Story Completion</div><div class="fs-3 fw-bold"><?= h((string)($metrics['completion_rate'] ?? '0')) ?>%</div></div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Question Correct Rate</div><div class="fs-3 fw-bold"><?= h((string)($metrics['question_correct_rate'] ?? '0')) ?>%</div></div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">Unique Players</div><div class="fs-3 fw-bold"><?= (int)($metrics['unique_players'] ?? 0) ?></div></div></div></div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card h-100 border-0 bg-light"><div class="card-body"><div class="text-muted small">Sessions Started</div><div class="fs-4 fw-bold"><?= (int)($metrics['sessions_started'] ?? 0) ?></div></div></div></div>
    <div class="col-md-4"><div class="card h-100 border-0 bg-light"><div class="card-body"><div class="text-muted small">Sessions Completed</div><div class="fs-4 fw-bold"><?= (int)($metrics['sessions_completed'] ?? 0) ?></div></div></div></div>
    <div class="col-md-4"><div class="card h-100 border-0 bg-light"><div class="card-body"><div class="text-muted small">Technical Errors</div><div class="fs-4 fw-bold text-danger"><?= (int)($metrics['technical_errors'] ?? 0) ?></div></div></div></div>
  </div>

  <div class="card shadow-sm mb-4"><div class="card-header bg-white"><strong>Content Performance</strong></div>
    <div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>Story</th><th>Times Played</th><th>Completions</th><th>Completion Rate</th></tr></thead><tbody>
      <?php foreach ($storyRows as $row): $cr = ((int)$row['times_played'] > 0) ? round(((int)$row['completions'] / (int)$row['times_played']) * 100, 1) : null; ?><tr><td><code><?= h($row['story_id']) ?></code></td><td><?= (int)$row['times_played'] ?></td><td><?= (int)$row['completions'] ?></td><td><?= $cr === null ? 'Data Belum Cukup' : h((string)$cr) . '%' ?></td></tr><?php endforeach; if (!$storyRows): ?><tr><td colspan="4" class="text-center text-muted py-4">Belum ada data sesuai filter.</td></tr><?php endif; ?>
    </tbody></table></div></div>

  <div class="card shadow-sm mb-4"><div class="card-header bg-white"><strong>Mechanic Performance</strong></div>
    <div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>Mechanic</th><th>Attempts</th><th>Correct Rate</th></tr></thead><tbody>
      <?php foreach (['multiple_choice','sequence','matching','timeline','verse_puzzle'] as $type): $row = array_values(array_filter($mechanicRows, fn($r) => $r['question_type'] === $type))[0] ?? ['attempts' => 0, 'correct_rate' => null]; ?><tr><td><?= h(ucwords(str_replace('_',' ', $type))) ?></td><td><?= (int)$row['attempts'] ?></td><td><?= $row['correct_rate'] !== null ? h((string)$row['correct_rate']) . '%' : 'Data Belum Cukup' ?></td></tr><?php endforeach; ?>
    </tbody></table></div></div>

  <div class="row g-3">
    <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header bg-white"><strong>Recent Sessions</strong></div><div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>Code</th><th>Teacher</th><th>Class</th><th>Status</th></tr></thead><tbody><?php foreach ($recentSessions as $row): ?><tr><td><code><?= h($row['session_code']) ?></code></td><td><?= h($row['teacher_name']) ?></td><td><?= h($row['class_group']) ?></td><td><?= h($row['status']) ?></td></tr><?php endforeach; if (!$recentSessions): ?><tr><td colspan="4" class="text-center text-muted py-4">Belum ada session.</td></tr><?php endif; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header bg-white"><strong>Recent Events</strong></div><div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>Event</th><th>Story</th><th>Result</th><th>Time</th></tr></thead><tbody><?php foreach ($recentEvents as $row): ?><tr><td><?= h($row['event_type']) ?></td><td><?= h($row['story_id'] ?? '-') ?></td><td><?= h($row['result'] ?? '-') ?></td><td><?= h($row['created_at']) ?></td></tr><?php endforeach; if (!$recentEvents): ?><tr><td colspan="4" class="text-center text-muted py-4">Belum ada event.</td></tr><?php endif; ?></tbody></table></div></div></div>
  </div>
</div>
</body>
</html>
