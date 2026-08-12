-- COMBINED SCHEMA: ITERATION 7 + 8
-- Teacher Session + Player Identity System

-- ====================
-- STAFF & PLAYER USERS
-- ====================

-- Staff Users (Teachers & Admins)
CREATE TABLE IF NOT EXISTS staff_users (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher') DEFAULT 'teacher',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Players (Registered Children)
CREATE TABLE IF NOT EXISTS players (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    player_code VARCHAR(30) NOT NULL UNIQUE,
    nickname VARCHAR(50) NOT NULL,
    pin_hash VARCHAR(255) NOT NULL,
    class_group ENUM('small', 'medium', 'large') NOT NULL,
    avatar_key VARCHAR(50) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES staff_users(id) ON DELETE SET NULL,
    INDEX idx_player_code (player_code),
    INDEX idx_nickname (nickname),
    INDEX idx_class (class_group),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- PLAYER PROGRESS
-- ====================

-- Player Story Progress (Long-term personal progress)
CREATE TABLE IF NOT EXISTS player_story_progress (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    player_id INT UNSIGNED NOT NULL,
    story_id VARCHAR(50) NOT NULL,
    status ENUM('started', 'completed') DEFAULT 'started',
    attempt_count INT UNSIGNED DEFAULT 0,
    best_score INT NULL,
    completed_at DATETIME NULL,
    completion_source ENUM('solo', 'classroom') DEFAULT 'solo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player_story (player_id, story_id),
    INDEX idx_player (player_id),
    INDEX idx_story (story_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- CLASSROOM SESSIONS
-- ====================

-- Game Sessions (Classroom multiplayer)
CREATE TABLE IF NOT EXISTS game_sessions (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    session_code VARCHAR(10) NOT NULL UNIQUE,
    teacher_id INT UNSIGNED NOT NULL,
    class_group ENUM('small', 'medium', 'large') NOT NULL,
    story_id VARCHAR(50) NOT NULL,
    play_mode ENUM('team') DEFAULT 'team',
    status ENUM('lobby', 'active', 'paused', 'completed') DEFAULT 'lobby',
    current_question_index INT DEFAULT 0,
    total_questions INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (teacher_id) REFERENCES staff_users(id) ON DELETE CASCADE,
    INDEX idx_session_code (session_code),
    INDEX idx_teacher (teacher_id),
    INDEX idx_status (status),
    INDEX idx_story (story_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game Teams
CREATE TABLE IF NOT EXISTS game_teams (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    session_id INT UNSIGNED NOT NULL,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(20) DEFAULT NULL,
    score INT DEFAULT 0,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES game_sessions(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    UNIQUE KEY unique_team_session (session_id, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Game Players (Session participants - can be guest or registered)
CREATE TABLE IF NOT EXISTS game_players (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    session_id INT UNSIGNED NOT NULL,
    team_id INT UNSIGNED NOT NULL,
    registered_player_id INT UNSIGNED NULL,
    nickname VARCHAR(50) NOT NULL,
    player_token VARCHAR(64) NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_seen_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES game_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES game_teams(id) ON DELETE CASCADE,
    FOREIGN KEY (registered_player_id) REFERENCES players(id) ON DELETE SET NULL,
    INDEX idx_session (session_id),
    INDEX idx_team (team_id),
    INDEX idx_token (player_token),
    INDEX idx_registered (registered_player_id),
    UNIQUE KEY unique_nickname_session (session_id, nickname)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Team Answers
CREATE TABLE IF NOT EXISTS team_answers (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    session_id INT UNSIGNED NOT NULL,
    team_id INT UNSIGNED NOT NULL,
    question_id VARCHAR(100) NOT NULL,
    question_index INT NOT NULL,
    answer_value TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    score_awarded INT DEFAULT 0,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES game_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES game_teams(id) ON DELETE CASCADE,
    INDEX idx_session (session_id),
    INDEX idx_team (team_id),
    INDEX idx_question (question_index),
    UNIQUE KEY unique_team_question (session_id, team_id, question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- ANALYTICS EVENTS
-- ====================

CREATE TABLE IF NOT EXISTS analytics_events (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    event_type VARCHAR(50) NOT NULL,
    player_id INT UNSIGNED NULL,
    session_id INT UNSIGNED NULL,
    team_id INT UNSIGNED NULL,
    story_id VARCHAR(50) NULL,
    question_id VARCHAR(100) NULL,
    class_group ENUM('small', 'medium', 'large') NULL,
    game_mode ENUM('solo', 'classroom') NULL,
    question_type VARCHAR(30) NULL,
    result ENUM('correct', 'wrong', 'started', 'completed', 'error') NULL,
    metadata_json JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at),
    INDEX idx_story (story_id),
    INDEX idx_question (question_id),
    INDEX idx_session (session_id),
    INDEX idx_player (player_id),
    INDEX idx_class_group (class_group),
    INDEX idx_game_mode (game_mode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- DEFAULT DATA
-- ====================

-- Default Admin (Password: admin123)
INSERT INTO staff_users (name, email, password_hash, role) VALUES 
('Admin GKJ', 'admin@gkjtangerang.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE name=name;

-- Sample Teacher (Password: teacher123)
INSERT INTO staff_users (name, email, password_hash, role) VALUES 
('Guru Maria', 'maria@gkjtangerang.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher')
ON DUPLICATE KEY UPDATE name=name;

-- Sample Players (PIN: 1234 for all)
INSERT INTO players (player_code, nickname, pin_hash, class_group, created_by) VALUES 
('GKJ-1001', 'Samuel', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'medium', 1),
('GKJ-1002', 'Maria', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'small', 1),
('GKJ-1003', 'Daniel', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'large', 1)
ON DUPLICATE KEY UPDATE nickname=nickname;