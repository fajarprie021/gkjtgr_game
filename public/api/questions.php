<?php
/**
 * Questions API endpoint - Database-driven
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$storyId    = trim($_GET['storyId']    ?? '');
$classGroup = trim($_GET['classGroup'] ?? 'small');

try {
    if (!$pdo) {
        throw new RuntimeException('Database tidak tersedia.');
    }

    $validClasses = ['small', 'medium', 'large'];
    if (!in_array($classGroup, $validClasses)) {
        $classGroup = 'small';
    }

    if (empty($storyId)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error'   => 'INVALID_INPUT',
            'message' => 'storyId diperlukan.'
        ]);
        exit;
    }

    // Cek apakah story ada
    $stmtStory = $pdo->prepare("SELECT id FROM stories WHERE id = ? AND is_active = TRUE LIMIT 1");
    $stmtStory->execute([$storyId]);
    if (!$stmtStory->fetch()) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error'   => 'STORY_NOT_FOUND',
            'message' => 'Cerita tidak ditemukan.'
        ]);
        exit;
    }

    // Ambil pertanyaan dari database (tanpa jawaban benar)
    $stmt = $pdo->prepare(
        "SELECT id, type, question, options, items, left_items, right_items,
                question_order AS `order`
         FROM questions
         WHERE story_id = ? AND class_group = ?
         ORDER BY question_order ASC"
    );
    $stmt->execute([$storyId, $classGroup]);

    $questions = [];
    while ($row = $stmt->fetch()) {
        $row['order']      = (int) $row['order'];
        $row['options']    = $row['options']    ? json_decode($row['options'],    true) : null;
        $row['items']      = $row['items']      ? json_decode($row['items'],      true) : null;
        $row['left_items'] = $row['left_items'] ? json_decode($row['left_items'], true) : null;
        $row['right_items']= $row['right_items']? json_decode($row['right_items'],true) : null;

        // Hapus field null agar response bersih
        $row = array_filter($row, fn($v) => $v !== null);
        $questions[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data'    => array_values($questions),
        'message' => 'OK'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'SERVER_ERROR',
        'message' => 'Gagal memuat pertanyaan.'
    ]);
}