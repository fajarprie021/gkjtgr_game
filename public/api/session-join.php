<?php
/**
 * API: Session Join
 * GET  ?code=XXXXXX           → validasi kode sesi, kembalikan daftar pemain
 * POST {session_code, player_id, pin} → login pemain ke sesi, kembalikan token
 */

require_once '../../config/database.php';
require_once '../../config/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

if (!$pdo) {
    echo json_encode(['success' => false, 'error' => 'Database tidak tersedia']);
    exit;
}

// ── GET: Cari sesi berdasarkan kode ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $code = strtoupper(trim($_GET['code'] ?? ''));
    if (strlen($code) < 4) {
        echo json_encode(['success' => false, 'error' => 'Kode sesi tidak valid']);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT gs.id, gs.session_code, gs.class_group, gs.story_id,
               gs.status, gs.play_mode,
               su.name AS teacher_name
        FROM game_sessions gs
        LEFT JOIN staff_users su ON su.id = gs.teacher_id
        WHERE gs.session_code = ? AND gs.status IN ('lobby','active')
    ");
    $stmt->execute([$code]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        echo json_encode(['success' => false, 'error' => 'Kode sesi tidak ditemukan atau sudah berakhir']);
        exit;
    }

    // Ambil daftar pemain aktif sesuai class_group sesi
    $pStmt = $pdo->prepare("
        SELECT id, nickname, avatar_key, class_group
        FROM players
        WHERE class_group = ? AND is_active = 1
        ORDER BY nickname ASC
    ");
    $pStmt->execute([$session['class_group']]);
    $players = $pStmt->fetchAll(PDO::FETCH_ASSOC);

    // Tandai pemain yang sudah join sesi ini
    $joinedStmt = $pdo->prepare("
        SELECT registered_player_id FROM game_players
        WHERE session_id = ? AND registered_player_id IS NOT NULL
    ");
    $joinedStmt->execute([$session['id']]);
    $joinedIds = array_column($joinedStmt->fetchAll(PDO::FETCH_ASSOC), 'registered_player_id');

    foreach ($players as &$p) {
        $p['already_joined'] = in_array((int)$p['id'], array_map('intval', $joinedIds));
    }
    unset($p);

    echo json_encode([
        'success'      => true,
        'session'      => [
            'id'           => $session['id'],
            'code'         => $session['session_code'],
            'class_group'  => $session['class_group'],
            'story_id'     => $session['story_id'],
            'status'       => $session['status'],
            'teacher_name' => $session['teacher_name'],
        ],
        'players'      => $players,
    ]);
    exit;
}

// ── POST: Login pemain ke sesi ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body        = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $sessionCode = strtoupper(trim($body['session_code'] ?? ''));
    $playerId    = (int)($body['player_id'] ?? 0);
    $pin         = trim($body['pin'] ?? '');

    if (!$sessionCode || !$playerId || !$pin) {
        echo json_encode(['success' => false, 'error' => 'Data tidak lengkap']);
        exit;
    }

    // Validasi sesi masih terbuka
    $sStmt = $pdo->prepare("
        SELECT id, class_group, status
        FROM game_sessions
        WHERE session_code = ? AND status IN ('lobby','active')
    ");
    $sStmt->execute([$sessionCode]);
    $session = $sStmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        echo json_encode(['success' => false, 'error' => 'Sesi tidak valid atau sudah berakhir']);
        exit;
    }

    // Validasi pemain & PIN
    $pStmt = $pdo->prepare("
        SELECT id, player_code, nickname, pin_hash, class_group
        FROM players
        WHERE id = ? AND class_group = ? AND is_active = 1
    ");
    $pStmt->execute([$playerId, $session['class_group']]);
    $player = $pStmt->fetch(PDO::FETCH_ASSOC);

    if (!$player) {
        echo json_encode(['success' => false, 'error' => 'Pemain tidak ditemukan']);
        exit;
    }
    if (!password_verify($pin, $player['pin_hash'])) {
        echo json_encode(['success' => false, 'error' => 'PIN salah, coba lagi']);
        exit;
    }

    // Ambil team pertama yang tersedia di sesi (auto-assign)
    $tStmt = $pdo->prepare("
        SELECT id FROM game_teams WHERE session_id = ? ORDER BY order_index ASC LIMIT 1
    ");
    $tStmt->execute([$session['id']]);
    $team = $tStmt->fetch(PDO::FETCH_ASSOC);

    if (!$team) {
        // Buat team default kalau belum ada
        $pdo->prepare("
            INSERT INTO game_teams (session_id, name, color, order_index) VALUES (?,?,?,0)
        ")->execute([$session['id'], 'Tim 1', '#4e73df']);
        $team = ['id' => $pdo->lastInsertId()];
    }

    // Cek apakah sudah join (upsert)
    $existStmt = $pdo->prepare("
        SELECT id, player_token FROM game_players
        WHERE session_id = ? AND registered_player_id = ?
    ");
    $existStmt->execute([$session['id'], $player['id']]);
    $existing = $existStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $playerToken = $existing['player_token'];
    } else {
        $playerToken = generatePlayerToken();
        try {
            $pdo->prepare("
                INSERT INTO game_players
                    (session_id, team_id, registered_player_id, nickname, player_token)
                VALUES (?,?,?,?,?)
            ")->execute([
                $session['id'],
                $team['id'],
                $player['id'],
                $player['nickname'],
                $playerToken,
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Gagal bergabung: ' . $e->getMessage()]);
            exit;
        }
    }

    // Set session PHP
    regenerateSessionIdSafe();
    $_SESSION['player_id']       = $player['id'];
    $_SESSION['player_code']     = $player['player_code'];
    $_SESSION['player_nickname'] = $player['nickname'];
    $_SESSION['player_class']    = $player['class_group'];
    $_SESSION['player_token']    = $playerToken;
    $_SESSION['game_session_id'] = $session['id'];

    echo json_encode([
        'success'      => true,
        'player_token' => $playerToken,
        'nickname'     => $player['nickname'],
        'redirect'     => '../player/dashboard.php',
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);