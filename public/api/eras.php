<?php
/**
 * Bible Eras API
 * Returns structured timeline eras
 */
header('Content-Type: application/json');

try {
    $eras = [
        [
            'id' => 'beginning',
            'title' => 'Permulaan',
            'order' => 1,
            'description' => 'Dari Penciptaan menuju kehidupan manusia pertama'
        ],
        [
            'id' => 'patriarchs',
            'title' => 'Para Leluhur',
            'order' => 2,
            'description' => 'Perjalanan Abraham, Ishak, Yakub, dan Yusuf'
        ],
        [
            'id' => 'exodus',
            'title' => 'Keluaran',
            'order' => 3,
            'description' => 'Pembebasan Israel dari Mesir menuju Tanah Perjanjian'
        ],
        [
            'id' => 'kingdom',
            'title' => 'Kerajaan',
            'order' => 4,
            'description' => 'Dari Samuel hingga raja-raja Israel'
        ],
        [
            'id' => 'prophets',
            'title' => 'Nabi-nabi',
            'order' => 5,
            'description' => 'Kerajaan terpecah dan pesan para nabi'
        ],
        [
            'id' => 'exile',
            'title' => 'Pembuangan',
            'order' => 6,
            'description' => 'Pembuangan dan kembali dari Babel'
        ],
        [
            'id' => 'jesus',
            'title' => 'Yesus Kristus',
            'order' => 7,
            'description' => 'Kelahiran, pelayanan, salib, dan kebangkitan Yesus'
        ],
        [
            'id' => 'early_church',
            'title' => 'Gereja Mula-mula',
            'order' => 8,
            'description' => 'Pentakosta dan penyebaran Injil'
        ]
    ];

    echo json_encode([
        'success' => true,
        'data' => $eras,
        'message' => 'OK'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'SERVER_ERROR',
        'message' => 'Gagal memuat era.'
    ]);
}