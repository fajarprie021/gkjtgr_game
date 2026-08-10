<?php
/**
 * Database Configuration Sample
 * Copy this file to database.php and update with your credentials
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'gkjtgr_game');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create PDO connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Legacy function for compatibility
function get_db_connection() {
    global $pdo;
    return $pdo;
}