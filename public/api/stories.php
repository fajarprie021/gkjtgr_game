<?php
/**
 * Bible Stories API - Database-driven
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

try {
    if (!$pdo) {
        throw new RuntimeException('Database tidak tersedia.');
    }

    $stmt = $pdo->query(
        "SELECT id, slug, era_id, title, reference,
                story_order AS `order`, previous_id, next_id,
                map_x, map_y, icon, is_active,
                has_content
         FROM stories
         WHERE is_active = TRUE
         ORDER BY story_order ASC"
    );

    $stories = [];
    while ($row = $stmt->fetch()) {
        $row['has_content'] = json_decode($row['has_content'], true) ?? [];
        $row['map_x']       = (int) $row['map_x'];
        $row['map_y']       = (int) $row['map_y'];
        $row['order']       = (int) $row['order'];
        $row['is_active']   = (bool) $row['is_active'];
        $stories[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data'    => $stories,
        'message' => 'OK'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'SERVER_ERROR',
        'message' => 'Gagal memuat data cerita.'
    ]);
}