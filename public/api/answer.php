<?php
/**
 * Answer Validation API - Iteration 9
 * Server-side validation for all game mechanics
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
$userAnswer = $input['answer'] ?? null;

if (empty($questionId) || $userAnswer === null) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_INPUT',
        'message' => 'Question ID and answer are required'
    ]);
    exit;
}

try {
    // Question database with correct answers (server-side only)
    // In production, this should be in database
    $questionAnswers = [
        // MULTIPLE CHOICE
        'creation-q1-small' => [
            'type' => 'multiple_choice',
            'correct' => 'Allah',
            'feedbackCorrect' => 'Luar biasa! Allah menciptakan segalanya.',
            'feedbackWrong' => 'Coba baca lagi awal kitab Kejadian.'
        ],
        'creation-q2-small' => [
            'type' => 'multiple_choice',
            'correct' => '6 Hari',
            'feedbackCorrect' => 'Betul! Dan Allah beristirahat di hari ketujuh.',
            'feedbackWrong' => 'Hampir tepat, hitung lagi harinya ya.'
        ],
        'creation-q1-medium' => [
            'type' => 'multiple_choice',
            'correct' => 'Terang',
            'feedbackCorrect' => 'Benar! Terang dipisahkan dari gelap.',
            'feedbackWrong' => 'Belum tepat. Terang adalah yang pertama.'
        ],
        'creation-q1-large' => [
            'type' => 'multiple_choice',
            'correct' => 'Allah',
            'feedbackCorrect' => 'Tepat sekali! Kita diciptakan mulia serupa dengan Allah.',
            'feedbackWrong' => 'Kurang tepat. Ingat Kejadian 1:26.'
        ],
        
        // TRUE/FALSE
        'creation-tf1-small' => [
            'type' => 'true_false',
            'correct' => true,
            'feedbackCorrect' => 'Benar! Allah menciptakan dunia dengan sempurna.',
            'feedbackWrong' => 'Belum tepat. Baca Kejadian 1:31.'
        ],
        
        // SEQUENCE
        'creation-seq1-small' => [
            'type' => 'sequence',
            'correct' => ['0', '1', '2', '3'], // IDs in correct order
            'feedbackCorrect' => 'Hebat! Urutan penciptaan sudah benar.',
            'feedbackWrong' => 'Belum tepat urutannya. Coba ingat lagi hari ke berapa.'
        ],
        
        // MATCHING
        'creation-match1-medium' => [
            'type' => 'matching',
            'correct' => [
                ['0', '0'], // left_id, right_id pairs
                ['1', '1'],
                ['2', '2']
            ],
            'feedbackCorrect' => 'Sempurna! Semua pasangan sudah tepat.',
            'feedbackWrong' => 'Ada yang belum tepat. Coba cocokkan lagi.'
        ],
        
        // TIMELINE
        'creation-timeline1-large' => [
            'type' => 'timeline',
            'correct' => ['creation', 'noah', 'abraham', 'moses'], // story IDs chronologically
            'feedbackCorrect' => 'Luar biasa! Urutan peristiwa sudah benar.',
            'feedbackWrong' => 'Belum tepat kronologinya. Peristiwa mana yang lebih dulu?'
        ],
        
        // VERSE PUZZLE
        'creation-verse1-medium' => [
            'type' => 'verse_puzzle',
            'correct' => ['0', '1', '2', '3', '4'], // word IDs in correct order
            'feedbackCorrect' => 'Sempurna! Ayatnya sudah lengkap dan benar.',
            'feedbackWrong' => 'Belum tepat susunannya. Baca ayatnya lagi dengan teliti.'
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

    // Validate based on question type
    $isCorrect = false;
    $type = $answerData['type'];

    switch ($type) {
        case 'multiple_choice':
            $isCorrect = validateMultipleChoice($userAnswer, $answerData['correct']);
            break;
            
        case 'true_false':
            $isCorrect = validateTrueFalse($userAnswer, $answerData['correct']);
            break;
            
        case 'sequence':
            $isCorrect = validateSequence($userAnswer, $answerData['correct']);
            break;
            
        case 'matching':
            $isCorrect = validateMatching($userAnswer, $answerData['correct']);
            break;
            
        case 'timeline':
            $isCorrect = validateTimeline($userAnswer, $answerData['correct']);
            break;
            
        case 'verse_puzzle':
            $isCorrect = validateVersePuzzle($userAnswer, $answerData['correct']);
            break;
            
        default:
            throw new Exception('Unsupported question type');
    }

    echo json_encode([
        'success' => true,
        'correct' => $isCorrect,
        'feedback' => $isCorrect ? $answerData['feedbackCorrect'] : $answerData['feedbackWrong'],
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

// ===== VALIDATOR FUNCTIONS =====

function validateMultipleChoice($userAnswer, $correctAnswer) {
    return trim($userAnswer) === trim($correctAnswer);
}

function validateTrueFalse($userAnswer, $correctAnswer) {
    // Convert to boolean
    $user = filter_var($userAnswer, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    return $user === $correctAnswer;
}

function validateSequence($userAnswer, $correctAnswer) {
    // userAnswer and correctAnswer are arrays of IDs
    if (!is_array($userAnswer) || count($userAnswer) !== count($correctAnswer)) {
        return false;
    }
    
    // Check if arrays match exactly in order
    for ($i = 0; $i < count($userAnswer); $i++) {
        if (strval($userAnswer[$i]) !== strval($correctAnswer[$i])) {
            return false;
        }
    }
    
    return true;
}

function validateMatching($userAnswer, $correctAnswer) {
    // userAnswer is array of [left_id, right_id] pairs
    // correctAnswer is array of correct pairs
    if (!is_array($userAnswer) || count($userAnswer) !== count($correctAnswer)) {
        return false;
    }
    
    // Sort both arrays for comparison
    $userSorted = $userAnswer;
    $correctSorted = $correctAnswer;
    
    sort($userSorted);
    sort($correctSorted);
    
    // Check if all pairs match
    $correctPairs = 0;
    foreach ($userAnswer as $userPair) {
        foreach ($correctAnswer as $correctPair) {
            if (strval($userPair[0]) === strval($correctPair[0]) && 
                strval($userPair[1]) === strval($correctPair[1])) {
                $correctPairs++;
                break;
            }
        }
    }
    
    return $correctPairs === count($correctAnswer);
}

function validateTimeline($userAnswer, $correctAnswer) {
    // Timeline is like sequence - must be in chronological order
    return validateSequence($userAnswer, $correctAnswer);
}

function validateVersePuzzle($userAnswer, $correctAnswer) {
    // Verse puzzle is like sequence - words in correct order
    return validateSequence($userAnswer, $correctAnswer);
}