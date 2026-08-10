<?php
/**
 * Questions API endpoint
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$storyId = $_GET['storyId'] ?? '';
$classGroup = $_GET['classGroup'] ?? 'small';

try {
    // Validate inputs
    $validClasses = ['small', 'medium', 'large'];
    if (!in_array($classGroup, $validClasses)) {
        $classGroup = 'small'; // Fallback to default
    }

    // Question data (WITHOUT correct answers - validated server-side)
    $questions = [
        'creation' => [
            'small' => [
                [
                    'id' => 'creation-q1-small',
                    'type' => 'multiple_choice',
                    'question' => 'Siapa yang menciptakan langit dan bumi?',
                    'options' => ['Allah', 'Adam', 'Nuh'],
                    'order' => 1
                ],
                [
                    'id' => 'creation-q2-small',
                    'type' => 'multiple_choice',
                    'question' => 'Berapa hari Allah menciptakan alam semesta?',
                    'options' => ['3 Hari', '6 Hari', '7 Hari'],
                    'order' => 2
                ]
            ],
            'medium' => [
                [
                    'id' => 'creation-q1-medium',
                    'type' => 'multiple_choice',
                    'question' => 'Apa yang Allah ciptakan pada hari pertama?',
                    'options' => ['Terang', 'Hewan', 'Manusia', 'Tumbuhan'],
                    'order' => 1
                ]
            ],
            'large' => [
                [
                    'id' => 'creation-q1-large',
                    'type' => 'multiple_choice',
                    'question' => 'Manusia diciptakan menurut gambar dan rupa siapa?',
                    'options' => ['Malaikat', 'Debu Tanah', 'Allah', 'Dunia'],
                    'order' => 1
                ]
            ]
        ]
    ];

    // Get questions for story and class
    if (!isset($questions[$storyId])) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'STORY_NOT_FOUND',
            'message' => 'Cerita tidak ditemukan.'
        ]);
        exit;
    }

    $result = $questions[$storyId][$classGroup] ?? [];

    echo json_encode([
        'success' => true,
        'data' => $result,
        'message' => 'OK'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'SERVER_ERROR',
        'message' => 'Gagal memuat pertanyaan.'
    ]);
}
