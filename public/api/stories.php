<?php
/**
 * Bible Stories API
 * Returns structured story data with timeline relationships
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

try {
    // Story metadata (content separated to story-content API)
    $stories = [
        [
            'id' => 'creation',
            'slug' => 'creation',
            'era_id' => 'beginning',
            'title' => 'Penciptaan',
            'reference' => 'Kejadian 1-2',
            'order' => 1,
            'previous_id' => null,
            'next_id' => 'noah',
            'map_x' => 10,
            'map_y' => 50,
            'icon' => 'globe-americas',
            'is_active' => true,
            'has_content' => ['small', 'medium', 'large']
        ],
        [
            'id' => 'noah',
            'slug' => 'noah',
            'era_id' => 'beginning',
            'title' => 'Nuh',
            'reference' => 'Kejadian 6-9',
            'order' => 2,
            'previous_id' => 'creation',
            'next_id' => 'abraham',
            'map_x' => 30,
            'map_y' => 30,
            'icon' => 'tsunami',
            'is_active' => true,
            'has_content' => ['small'] // Only small class content available
        ],
        [
            'id' => 'abraham',
            'slug' => 'abraham',
            'era_id' => 'patriarchs',
            'title' => 'Abraham',
            'reference' => 'Kejadian 12-25',
            'order' => 3,
            'previous_id' => 'noah',
            'next_id' => 'yosef',
            'map_x' => 50,
            'map_y' => 50,
            'icon' => 'tent',
            'is_active' => true,
            'has_content' => [] // No detailed content yet
        ],
        [
            'id' => 'yosef',
            'slug' => 'yosef',
            'era_id' => 'patriarchs',
            'title' => 'Yusuf',
            'reference' => 'Kejadian 37-50',
            'order' => 4,
            'previous_id' => 'abraham',
            'next_id' => 'moses',
            'map_x' => 70,
            'map_y' => 30,
            'icon' => 'person-badge',
            'is_active' => true,
            'has_content' => []
        ],
        [
            'id' => 'moses',
            'slug' => 'moses',
            'era_id' => 'exodus',
            'title' => 'Musa',
            'reference' => 'Keluaran 1-40',
            'order' => 5,
            'previous_id' => 'yosef',
            'next_id' => null,
            'map_x' => 90,
            'map_y' => 50,
            'icon' => 'water',
            'is_active' => true,
            'has_content' => []
        ]
    ];

    // Filter only active stories
    $activeStories = array_filter($stories, function($story) {
        return $story['is_active'] === true;
    });

    echo json_encode([
        'success' => true,
        'data' => array_values($activeStories),
        'message' => 'OK'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'SERVER_ERROR',
        'message' => 'Gagal memuat data cerita.'
    ]);
}