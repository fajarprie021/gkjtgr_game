<?php
/**
 * Answer Validation API - Database-driven
 * Server-side validation for all game mechanics
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error'   => 'METHOD_NOT_ALLOWED',
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Get JSON input
$input      = json_decode(file_get_contents('php://input'), true);
$questionId = $input['question_id'] ?? '';
$userAnswer = $input['answer']      ?? null;

if (empty($questionId) || $userAnswer === null) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'INVALID_INPUT',
        'message' => 'Question ID and answer are required'
    ]);
    exit;
}

try {
    if (!$pdo) {
        throw new RuntimeException('Database tidak tersedia.');
    }

    // Ambil jawaban dari database (server-side only, tidak pernah dikirim ke client)
    $stmt = $pdo->prepare(
        "SELECT question_id, type, correct_answer, feedback_correct, feedback_wrong
         FROM question_answers
         WHERE question_id = ?
         LIMIT 1"
    );
    $stmt->execute([$questionId]);
    $answerData = $stmt->fetch();

    if (!$answerData) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error'   => 'QUESTION_NOT_FOUND',
            'message' => 'Question not found'
        ]);
        exit;
    }

    $type          = $answerData['type'];
    $correctAnswer = json_decode($answerData['correct_answer'], true);
    // json_decode null-safe: jika scalar string maka tetap string
    if ($correctAnswer === null) {
        $correctAnswer = $answerData['correct_answer'];
    }

    // Validate based on question type
    $isCorrect = false;
    switch ($type) {
        case 'multiple_choice':
            $isCorrect = validateMultipleChoice($userAnswer, $correctAnswer);
            break;
        case 'true_false':
            $isCorrect = validateTrueFalse($userAnswer, $correctAnswer);
            break;
        case 'sequence':
        case 'timeline':
        case 'verse_puzzle':
            $isCorrect = validateSequence($userAnswer, $correctAnswer);
            break;
        case 'matching':
            $isCorrect = validateMatching($userAnswer, $correctAnswer);
            break;
        default:
            throw new Exception('Unsupported question type');
    }

    echo json_encode([
        'success'  => true,
        'correct'  => $isCorrect,
        'feedback' => $isCorrect
            ? $answerData['feedback_correct']
            : $answerData['feedback_wrong'],
        'message'  => 'OK'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'SERVER_ERROR',
        'message' => 'Gagal memvalidasi jawaban.'
    ]);
}

// ===== VALIDATOR FUNCTIONS =====

function validateMultipleChoice($userAnswer, $correctAnswer) {
    return trim((string) $userAnswer) === trim((string) $correctAnswer);
}

function validateTrueFalse($userAnswer, $correctAnswer) {
    $user = filter_var($userAnswer, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    return $user === (bool) $correctAnswer;
}

function validateSequence($userAnswer, $correctAnswer) {
    if (!is_array($userAnswer) || !is_array($correctAnswer)) return false;
    if (count($userAnswer) !== count($correctAnswer)) return false;
    for ($i = 0; $i < count($userAnswer); $i++) {
        if (strval($userAnswer[$i]) !== strval($correctAnswer[$i])) return false;
    }
    return true;
}

function validateMatching($userAnswer, $correctAnswer) {
    if (!is_array($userAnswer) || !is_array($correctAnswer)) return false;
    if (count($userAnswer) !== count($correctAnswer)) return false;
    $matched = 0;
    foreach ($userAnswer as $userPair) {
        foreach ($correctAnswer as $correctPair) {
            if (strval($userPair[0]) === strval($correctPair[0]) &&
                strval($userPair[1]) === strval($correctPair[1])) {
                $matched++;
                break;
            }
        }
    }
    return $matched === count($correctAnswer);
}