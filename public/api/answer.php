<?php
/**
 * Answer Validation API
 * Validates user answers on server side
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'METHOD_NOT_ALLOWED',
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$questionId = $input['question_id'] ?? '';
$userAnswer = $input['answer'] ?? '';

if (empty($questionId) || empty($userAnswer)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_INPUT',
        'message' => 'Question ID and answer are required'
    ]);
    exit;
}

try {
    // Question data with correct answers (NOT sent to frontend)
    $questionAnswers = [
        'creation-q1-small' => [
            'correct' => 'Allah',
            'feedbackCorrect' => 'Luar biasa! Allah menciptakan segalanya.',
            'feedbackWrong' => 'Coba baca lagi awal kitab Kejadian.'
        ],
        'creation-q2-small' => [
            'correct' => '6 Hari',
            'feedbackCorrect' => 'Betul! Dan Allah beristirahat di hari ketujuh.',
            'feedbackWrong' => 'Hampir tepat, hitung lagi harinya ya.'
        ],
        'creation-q1-medium' => [
            'correct' => 'Terang',
            'feedbackCorrect' => 'Benar! Terang dipisahkan dari gelap.',
            'feedbackWrong' => 'Belum tepat. Terang adalah yang pertama.'
        ],
        'creation-q1-large' => [
            'correct' => 'Allah',
            'feedbackCorrect' => 'Tepat sekali! Kita diciptakan mulia serupa dengan Allah.',
            'feedbackWrong' => 'Kurang tepat. Ingat Kejadian 1:26.'
        ]
    ];

    $answerData = $questionAnswers[$questionId] ?? null;

    if (!$answerData) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'QUESTION_NOT_FOUND',
            'message' => 'Question not found'
        ]);
        exit;
    }

    $isCorrect = trim($userAnswer) === trim($answerData['correct']);

    echo json_encode([
        'success' => true,
        'data' => [
            'correct' => $isCorrect,
            'feedback' => $isCorrect ? $answerData['feedbackCorrect'] : $answerData['feedbackWrong']
        ],
        'message' => 'OK'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'SERVER_ERROR',
        'message' => 'Gagal memvalidasi jawaban.'
    ]);
}