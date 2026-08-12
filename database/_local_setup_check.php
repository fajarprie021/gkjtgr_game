<?php
/**
 * Local helper (Windows / PowerShell friendly) to:
 *  - test MySQL connection
 *  - list databases
 *  - import schema_combined_7_8.sql into `gkjtgr_game`
 *  - create a default admin if not exists (password 1234)
 *
 * Run with:  php database/_local_setup_check.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$DB_HOST = '127.0.0.1';
$DB_PORT = 3306;
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'gkjtgr_game';

$schemaFile = __DIR__ . '/schema_combined_7_8.sql';

function println($msg) { echo $msg . PHP_EOL; }

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};charset=utf8mb4",
        $DB_USER, $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    println('[ok] mysql reachable');
} catch (Throwable $e) {
    println('[fatal] cannot connect: ' . $e->getMessage());
    exit(1);
}

println('-- existing databases --');
foreach ($pdo->query('SHOW DATABASES') as $row) {
    println('  - ' . $row[0]);
}

// Create database if missing
$exists = (bool)$pdo->query("SELECT 1 FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$DB_NAME}'")->fetchColumn();
if (!$exists) {
    $pdo->exec("CREATE DATABASE `{$DB_NAME}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    println("[ok] database created: {$DB_NAME}");
} else {
    println("[info] database exists: {$DB_NAME}");
}

$pdo->exec("USE `{$DB_NAME}`");

// Import schema
if (!is_file($schemaFile)) {
    println('[fatal] schema file missing: ' . $schemaFile);
    exit(1);
}
$sql = file_get_contents($schemaFile);

// Split by semicolon outside of strings (simple, but works for this schema)
$statements = array_filter(
    array_map('trim', preg_split('/;\s*\R/u', $sql)),
    fn($s) => $s !== '' && !str_starts_with($s, '--')
);

$ok = 0; $fail = 0;
foreach ($statements as $stmt) {
    if (str_starts_with(ltrim($stmt), '--') || $stmt === '') continue;
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (Throwable $e) {
        $fail++;
        println('[warn] stmt failed: ' . substr($stmt, 0, 60) . '... -> ' . $e->getMessage());
    }
}
println("[ok] schema imported: {$ok} ok / {$fail} failed");

// Seed minimal admin if staff_users table exists and is empty
try {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM staff_users")->fetchColumn();
    if ($count === 0) {
        $hash = password_hash('1234', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO staff_users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, 'admin', 1)");
        $stmt->execute(['Admin Lokal', 'admin@local', $hash]);
        println('[ok] default admin created: email=admin@local password=1234');
    } else {
        println('[info] staff_users not empty (' . $count . ' rows)');
    }
} catch (Throwable $e) {
    println('[warn] admin seed skipped: ' . $e->getMessage());
}

// Show staff_users rows
try {
    foreach ($pdo->query('SELECT id, name, email, role, is_active FROM staff_users') as $r) {
        println('  staff#' . $r['id'] . ' ' . $r['email'] . ' role=' . $r['role'] . ' active=' . $r['is_active']);
    }
} catch (Throwable $e) { /* ignore */ }

println('[done]');
