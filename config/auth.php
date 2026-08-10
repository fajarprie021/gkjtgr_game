<?php
// Authentication & Session Helper Functions

session_start();

// Staff Authentication
function requireStaffAuth() {
    if (!isset($_SESSION['staff_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Authentication required']);
        exit;
    }
}

function getStaffUser() {
    if (!isset($_SESSION['staff_id'])) {
        return null;
    }
    return [
        'id' => $_SESSION['staff_id'],
        'name' => $_SESSION['staff_name'] ?? '',
        'email' => $_SESSION['staff_email'] ?? '',
        'role' => $_SESSION['staff_role'] ?? 'teacher'
    ];
}

function staffLogin($pdo, $email, $password) {
    $stmt = $pdo->prepare("
        SELECT id, name, email, password_hash, role 
        FROM staff_users 
        WHERE email = ? AND is_active = 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['staff_id'] = $user['id'];
        $_SESSION['staff_name'] = $user['name'];
        $_SESSION['staff_email'] = $user['email'];
        $_SESSION['staff_role'] = $user['role'];
        return true;
    }
    return false;
}

function staffLogout() {
    unset($_SESSION['staff_id']);
    unset($_SESSION['staff_name']);
    unset($_SESSION['staff_email']);
    unset($_SESSION['staff_role']);
}

// Player Authentication
function requirePlayerAuth() {
    if (!isset($_SESSION['player_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Player authentication required']);
        exit;
    }
}

function getPlayer() {
    if (!isset($_SESSION['player_id'])) {
        return null;
    }
    return [
        'id' => $_SESSION['player_id'],
        'player_code' => $_SESSION['player_code'] ?? '',
        'nickname' => $_SESSION['player_nickname'] ?? '',
        'class_group' => $_SESSION['player_class'] ?? 'medium'
    ];
}

function playerLogin($pdo, $playerCode, $pin) {
    $stmt = $pdo->prepare("
        SELECT id, player_code, nickname, pin_hash, class_group 
        FROM players 
        WHERE player_code = ? AND is_active = 1
    ");
    $stmt->execute([$playerCode]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($player && password_verify($pin, $player['pin_hash'])) {
        $_SESSION['player_id'] = $player['id'];
        $_SESSION['player_code'] = $player['player_code'];
        $_SESSION['player_nickname'] = $player['nickname'];
        $_SESSION['player_class'] = $player['class_group'];
        return true;
    }
    return false;
}

function playerLogout() {
    unset($_SESSION['player_id']);
    unset($_SESSION['player_code']);
    unset($_SESSION['player_nickname']);
    unset($_SESSION['player_class']);
}

// Utility Functions
function generatePlayerCode() {
    return 'GKJ-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
}

function generateSessionCode() {
    $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Exclude I, O, 0, 1
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}

function generatePlayerToken() {
    return bin2hex(random_bytes(32));
}