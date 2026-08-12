<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$eventType = trim($input['event_type'] ?? '');

if ($eventType === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'INVALID_INPUT']);
    exit;
}

$allowed = [
    'story_started', 'story_completed', 'question_viewed', 'answer_submitted',
    'question_completed', 'session_started', 'session_completed', 'player_joined_session',
    'technical_error'
];
if (!in_array($eventType, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'UNSUPPORTED_EVENT']);
    exit;
}

$metadata = $input['metadata'] ?? [];
if (!is_array($metadata)) {
    $metadata = [];
}

$playerId = isset($input['player_id']) ? (int)$input['player_id'] : null;
$sessionId = isset($input['session_id']) ? (int)$input['session_id'] : null;
$teamId = isset($input['team_id']) ? (int)$input['team_id'] : null;
$storyId = trim($input['story_id'] ?? '') ?: null;
$questionId = trim($input['question_id'] ?? '') ?: null;
$classGroup = $input['class_group'] ?? null;
$gameMode = $input['game_mode'] ?? null;
$questionType = $input['question_type'] ?? null;
$result = $input['result'] ?? null;

try {
    $stmt = $pdo->prepare("INSERT INTO analytics_events
        (event_type, player_id, session_id, team_id, story_id, question_id, class_group, game_mode, question_type, result, metadata_json)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $eventType,
        $playerId ?: null,
        $sessionId ?: null,
        $teamId ?: null,
        $storyId,
        $questionId,
        $classGroup,
        $gameMode,
        $questionType,
        $result,
        !empty($metadata) ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : null,
    ]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    error_log('Analytics event error: ' . $e->getMessage());
    echo json_encode(['success' => true, 'best_effort' => true]);
}
