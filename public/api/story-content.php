<?php
/**
 * Story Content API
 * Returns class-specific learning content for stories
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$storyId = $_GET['id'] ?? $_GET['storyId'] ?? '';
$classGroup = $_GET['class'] ?? $_GET['classGroup'] ?? 'small';

// Validate inputs
$validClasses = ['small', 'medium', 'large'];
if (!in_array($classGroup, $validClasses)) {
    $classGroup = 'small';
}

if (empty($storyId)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_INPUT',
        'message' => 'Story ID is required'
    ]);
    exit;
}

try {
    // Story content data separated by class
    // Structure: story_id => class_group => content
    $storyContents = [
        'creation' => [
            'small' => [
                'summary' => 'Pada mulanya Allah menciptakan langit dan bumi. Allah menciptakan terang, air, tanaman, matahari, bulan, hewan, dan manusia. Semuanya sangat baik!',
                'main_message' => 'Allah menciptakan segalanya dengan sempurna.',
                'about_god' => 'Allah adalah Pencipta yang Mahakuasa. Dia menciptakan dunia hanya dengan firman-Nya.',
                'character_value' => 'Bersyukur',
                'application' => 'Kita harus bersyukur untuk semua ciptaan Allah dan merawatnya dengan baik.',
                'memory_verse_reference' => 'Kejadian 1:1',
                'memory_verse_text' => 'Pada mulanya Allah menciptakan langit dan bumi.',
                'learning_objective' => [
                    'Mengetahui bahwa Allah menciptakan segalanya',
                    'Memahami urutan penciptaan',
                    'Belajar bersyukur atas ciptaan Allah'
                ],
                'content_status' => 'verified'
            ],
            'medium' => [
                'summary' => 'Pada mulanya Allah menciptakan langit dan bumi dalam enam hari. Hari pertama: terang dan gelap. Hari kedua: langit. Hari ketiga: daratan dan tumbuhan. Hari keempat: matahari, bulan, dan bintang. Hari kelima: ikan dan burung. Hari keenam: hewan darat dan manusia. Allah beristirahat di hari ketujuh.',
                'main_message' => 'Allah menciptakan alam semesta dengan rencana yang teratur dan sempurna.',
                'about_god' => 'Allah adalah Pencipta yang berkuasa penuh. Dia menciptakan segala sesuatu dengan firman-Nya dan melihat bahwa semuanya baik.',
                'character_value' => 'Keteraturan dan Syukur',
                'application' => 'Kita harus menghargai ciptaan Allah dengan merawat lingkungan dan tidak merusaknya. Kita juga perlu mengatur hidup kita dengan baik seperti Allah mengatur penciptaan.',
                'memory_verse_reference' => 'Kejadian 1:1',
                'memory_verse_text' => 'Pada mulanya Allah menciptakan langit dan bumi.',
                'learning_objective' => [
                    'Memahami urutan penciptaan enam hari',
                    'Mengenal sifat Allah sebagai Pencipta',
                    'Menerapkan sikap bersyukur dan tanggung jawab'
                ],
                'content_status' => 'verified'
            ],
            'large' => [
                'summary' => 'Kitab Kejadian membuka dengan pernyataan luar biasa: "Pada mulanya Allah menciptakan langit dan bumi." Allah menciptakan alam semesta dalam enam hari dengan pola yang teratur. Penciptaan dimulai dari yang tidak beraturan (tohu wabohu) menjadi teratur dan penuh kehidupan. Manusia diciptakan menurut gambar dan rupa Allah (imago Dei), berbeda dari ciptaan lain. Allah memberikan mandat kepada manusia untuk berkuasa atas ciptaan dan memeliharanya. Hari ketujuh menjadi hari perhentian (Sabat) yang kudus.',
                'main_message' => 'Allah adalah Pencipta yang berdaulat penuh yang menciptakan alam semesta dengan tujuan dan keteraturan. Manusia memiliki posisi khusus sebagai gambar Allah dan pemelihara ciptaan.',
                'about_god' => 'Allah adalah Pencipta ex nihilo (dari yang tidak ada). Dia transenden namun dekat dengan ciptaan-Nya. Trinitas Allah terlibat dalam penciptaan (Roh Allah, Firman Allah).',
                'character_value' => 'Tanggung Jawab dan Penatalayanan (Stewardship)',
                'application' => 'Sebagai gambar Allah, kita memiliki tanggung jawab khusus untuk merawat ciptaan. Kita dipanggil menjadi penatalayan yang baik, bukan eksploitator. Sabat mengajarkan kita pentingnya istirahat dan refleksi spiritual.',
                'memory_verse_reference' => 'Kejadian 1:27',
                'memory_verse_text' => 'Maka Allah menciptakan manusia itu menurut gambar-Nya, menurut gambar Allah diciptakan-Nya dia.',
                'learning_objective' => [
                    'Memahami teologi penciptaan dalam Kejadian 1-2',
                    'Mengenal konsep imago Dei',
                    'Memahami mandat budaya dan tanggung jawab ekologis',
                    'Menghargai keteraturan dan maksud Allah dalam penciptaan'
                ],
                'content_status' => 'verified'
            ]
        ],
        'noah' => [
            'small' => [
                'summary' => 'Manusia menjadi jahat. Allah memutuskan membawa air bah. Nuh hidup benar. Allah menyuruh Nuh membuat bahtera besar. Nuh taat. Air bah datang. Nuh, keluarganya, dan binatang-binatang selamat. Allah membuat pelangi sebagai tanda perjanjian.',
                'main_message' => 'Allah menghukum kejahatan tetapi menyelamatkan orang yang taat.',
                'about_god' => 'Allah adil dan setia pada janji-Nya.',
                'character_value' => 'Ketaatan',
                'application' => 'Kita harus taat pada Allah seperti Nuh taat.',
                'memory_verse_reference' => 'Kejadian 6:22',
                'memory_verse_text' => NULL,
                'learning_objective' => [
                    'Mengetahui cerita Nuh dan air bah',
                    'Memahami pentingnya ketaatan',
                    'Mengenal tanda pelangi'
                ],
                'content_status' => 'draft'
            ],
            'medium' => [
                'summary' => 'TODO: Medium level content for Noah - needs verification',
                'main_message' => NULL,
                'about_god' => NULL,
                'character_value' => NULL,
                'application' => NULL,
                'memory_verse_reference' => NULL,
                'memory_verse_text' => NULL,
                'learning_objective' => [],
                'content_status' => 'needs_review'
            ],
            'large' => [
                'summary' => 'TODO: Large level content for Noah - needs verification',
                'main_message' => NULL,
                'about_god' => NULL,
                'character_value' => NULL,
                'application' => NULL,
                'memory_verse_reference' => NULL,
                'memory_verse_text' => NULL,
                'learning_objective' => [],
                'content_status' => 'needs_review'
            ]
        ]
    ];

    // Get content for story and class
    if (!isset($storyContents[$storyId])) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'STORY_NOT_FOUND',
            'message' => 'Cerita tidak ditemukan.'
        ]);
        exit;
    }

    if (!isset($storyContents[$storyId][$classGroup])) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'CONTENT_NOT_AVAILABLE',
            'message' => 'Konten untuk kelas ini belum tersedia.'
        ]);
        exit;
    }

    $content = $storyContents[$storyId][$classGroup];

    echo json_encode([
        'success' => true,
        'data' => [
            'story_id' => $storyId,
            'class_group' => $classGroup,
            'content' => $content
        ],
        'message' => 'OK'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'SERVER_ERROR',
        'message' => 'Gagal memuat konten cerita.'
    ]);
}