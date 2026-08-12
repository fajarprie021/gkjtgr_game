-- ============================================================
-- MIGRATION: Stories, Questions & Question Answers
-- Memindahkan data hardcode ke database
-- ============================================================

-- ====================
-- STORIES (Map data)
-- ====================
CREATE TABLE IF NOT EXISTS stories (
    id VARCHAR(50) PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    era_id VARCHAR(50) NOT NULL DEFAULT 'beginning',
    title VARCHAR(100) NOT NULL,
    reference VARCHAR(100) NULL,
    story_order INT UNSIGNED DEFAULT 0,
    previous_id VARCHAR(50) NULL,
    next_id VARCHAR(50) NULL,
    map_x TINYINT UNSIGNED DEFAULT 50,
    map_y TINYINT UNSIGNED DEFAULT 50,
    icon VARCHAR(50) DEFAULT 'book',
    is_active BOOLEAN DEFAULT TRUE,
    has_content JSON NULL COMMENT 'Array of class groups with content e.g. [\"small\",\"medium\"]',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_era (era_id),
    INDEX idx_order (story_order),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- QUESTIONS (Client-safe, no correct answers)
-- ====================
CREATE TABLE IF NOT EXISTS questions (
    id VARCHAR(100) PRIMARY KEY,
    story_id VARCHAR(50) NOT NULL,
    class_group ENUM('small', 'medium', 'large') NOT NULL,
    type ENUM('multiple_choice', 'true_false', 'sequence', 'matching', 'timeline', 'verse_puzzle') NOT NULL,
    question TEXT NOT NULL,
    options JSON NULL COMMENT 'Array of options for multiple_choice',
    items JSON NULL COMMENT 'Array of {id, text} for sequence/timeline/verse_puzzle',
    left_items JSON NULL COMMENT 'Array of {id, text} for matching left side',
    right_items JSON NULL COMMENT 'Array of {id, text} for matching right side',
    question_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_story_class (story_id, class_group),
    INDEX idx_order (question_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- QUESTION ANSWERS (Server-side only, NEVER exposed to client)
-- ====================
CREATE TABLE IF NOT EXISTS question_answers (
    question_id VARCHAR(100) PRIMARY KEY,
    type ENUM('multiple_choice', 'true_false', 'sequence', 'matching', 'timeline', 'verse_puzzle') NOT NULL,
    correct_answer JSON NOT NULL COMMENT 'String for MC/TF, array for sequence/matching',
    feedback_correct TEXT NOT NULL,
    feedback_wrong TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA: STORIES
-- ============================================================
INSERT INTO stories
    (id, slug, era_id, title, reference, story_order, previous_id, next_id, map_x, map_y, icon, is_active, has_content)
VALUES
    ('creation', 'creation', 'beginning', 'Penciptaan',  'Kejadian 1-2',  1, NULL,       'noah',     10, 50, 'globe-americas', TRUE, '["small","medium","large"]'),
    ('noah',     'noah',     'beginning', 'Nuh',         'Kejadian 6-9',  2, 'creation', 'abraham',  30, 30, 'tsunami',        TRUE, '["small"]'),
    ('abraham',  'abraham',  'patriarchs','Abraham',     'Kejadian 12-25',3, 'noah',     'yosef',    50, 50, 'tent',           TRUE, '[]'),
    ('yosef',    'yosef',    'patriarchs','Yusuf',       'Kejadian 37-50',4, 'abraham',  'moses',    70, 30, 'person-badge',   TRUE, '[]'),
    ('moses',    'moses',    'exodus',    'Musa',        'Keluaran 1-40', 5, 'yosef',    NULL,       90, 50, 'water',          TRUE, '[]')
ON DUPLICATE KEY UPDATE
    title        = VALUES(title),
    reference    = VALUES(reference),
    story_order  = VALUES(story_order),
    previous_id  = VALUES(previous_id),
    next_id      = VALUES(next_id),
    map_x        = VALUES(map_x),
    map_y        = VALUES(map_y),
    icon         = VALUES(icon),
    is_active    = VALUES(is_active),
    has_content  = VALUES(has_content),
    updated_at   = CURRENT_TIMESTAMP;

-- ============================================================
-- SEED DATA: QUESTIONS
-- ============================================================
INSERT INTO questions
    (id, story_id, class_group, type, question, options, question_order)
VALUES
    -- CREATION - small
    ('creation-q1-small',  'creation', 'small',  'multiple_choice', 'Siapa yang menciptakan langit dan bumi?',
        '["Allah","Adam","Nuh"]', 1),
    ('creation-q2-small',  'creation', 'small',  'multiple_choice', 'Berapa hari Allah menciptakan alam semesta?',
        '["3 Hari","6 Hari","7 Hari"]', 2),

    -- CREATION - medium
    ('creation-q1-medium', 'creation', 'medium', 'multiple_choice', 'Apa yang Allah ciptakan pada hari pertama?',
        '["Terang","Hewan","Manusia","Tumbuhan"]', 1),

    -- CREATION - large
    ('creation-q1-large',  'creation', 'large',  'multiple_choice', 'Manusia diciptakan menurut gambar dan rupa siapa?',
        '["Malaikat","Debu Tanah","Allah","Dunia"]', 1),

    -- NOAH - small
    ('noah-q1-small',      'noah',     'small',  'multiple_choice', 'Siapa yang hidup benar di hadapan Allah ketika manusia menjadi jahat?',
        '["Adam","Nuh","Abraham"]', 1),
    ('noah-q2-small',      'noah',     'small',  'multiple_choice', 'Apa yang Allah perintahkan kepada Nuh untuk dibuat?',
        '["Istana","Tembok Besar","Bahtera"]', 2),
    ('noah-q3-small',      'noah',     'small',  'multiple_choice', 'Apa tanda perjanjian Allah dengan Nuh setelah air bah surut?',
        '["Pelangi","Bintang","Matahari"]', 3)

ON DUPLICATE KEY UPDATE
    question       = VALUES(question),
    options        = VALUES(options),
    question_order = VALUES(question_order),
    updated_at     = CURRENT_TIMESTAMP;

-- ============================================================
-- SEED DATA: QUESTION ANSWERS (Server-side only)
-- ============================================================
INSERT INTO question_answers
    (question_id, type, correct_answer, feedback_correct, feedback_wrong)
VALUES
    -- CREATION - small
    ('creation-q1-small',  'multiple_choice', '"Allah"',
        'Luar biasa! Allah menciptakan segalanya.',
        'Coba baca lagi awal kitab Kejadian.'),
    ('creation-q2-small',  'multiple_choice', '"6 Hari"',
        'Betul! Dan Allah beristirahat di hari ketujuh.',
        'Hampir tepat, hitung lagi harinya ya.'),

    -- CREATION - medium
    ('creation-q1-medium', 'multiple_choice', '"Terang"',
        'Benar! Terang dipisahkan dari gelap.',
        'Belum tepat. Terang adalah yang pertama.'),

    -- CREATION - large
    ('creation-q1-large',  'multiple_choice', '"Allah"',
        'Tepat sekali! Kita diciptakan mulia serupa dengan Allah.',
        'Kurang tepat. Ingat Kejadian 1:26.'),

    -- NOAH - small
    ('noah-q1-small',      'multiple_choice', '"Nuh"',
        'Tepat! Nuh adalah orang benar di hadapan Allah.',
        'Belum tepat. Baca Kejadian 6:9.'),
    ('noah-q2-small',      'multiple_choice', '"Bahtera"',
        'Benar! Allah menyuruh Nuh membuat bahtera yang besar.',
        'Coba lagi. Allah menyuruh Nuh membuat sesuatu yang besar untuk berlayar.'),
    ('noah-q3-small',      'multiple_choice', '"Pelangi"',
        'Betul! Pelangi adalah tanda perjanjian Allah dengan Nuh.',
        'Belum tepat. Lihatlah langit setelah hujan - apa yang kamu lihat?')

ON DUPLICATE KEY UPDATE
    correct_answer   = VALUES(correct_answer),
    feedback_correct = VALUES(feedback_correct),
    feedback_wrong   = VALUES(feedback_wrong),
    updated_at       = CURRENT_TIMESTAMP;