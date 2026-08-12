<?php
/**
 * Import schema using PHP PDO with multi-statement execution
 * Run with: php database/_import_schema.php
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$DB_HOST = '127.0.0.1';
$DB_PORT = 3306;
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'gkjtgr_game';
$schemaFile = __DIR__ . '/schema_combined_7_8.sql';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};port={$DB_PORT};charset=utf8mb4",
        $DB_USER, $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
        ]
    );
    echo "[ok] connected\n";
} catch (Throwable $e) {
    echo "[fatal] " . $e->getMessage() . "\n";
    exit(1);
}

// Ensure database
$exists = (bool)$pdo->query("SELECT 1 FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$DB_NAME}'")->fetchColumn();
if (!$exists) {
    $pdo->exec("CREATE DATABASE `{$DB_NAME}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[ok] database created\n";
} else {
    $pdo->exec("DROP DATABASE `{$DB_NAME}`");
    $pdo->exec("CREATE DATABASE `{$DB_NAME}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[ok] database reset\n";
}

$pdo->exec("USE `{$DB_NAME}`");

// Clean & split SQL
$sql = file_get_contents($schemaFile);
$sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql); // strip BOM
$sql = preg_replace('/^\s*--.*$/m', '', $sql);    // strip line comments
$lines = explode("\n", $sql);
$clean = [];
foreach ($lines as $ln) {
    $t = rtrim($ln);
    if ($t === '' || str_starts_with($t, '--')) continue;
    $clean[] = $t;
}
$cleanSql = implode("\n", $clean);
$statements = array_filter(array_map('trim', preg_split('/;\s*\n/u', $cleanSql)), fn($s) => $s !== '');

$ok = 0; $fail = 0;
foreach ($statements as $stmt) {
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (Throwable $e) {
        $fail++;
        echo "[warn] failed: " . substr($stmt, 0, 80) . "... -> " . $e->getMessage() . "\n";
    }
}
echo "[ok] imported: $ok statements / $fail failed\n";

// Seed admin + teacher + sample players with fresh bcrypt hashes
$adminHash = password_hash('admin123', PASSWORD_DEFAULT);
$teacherHash = password_hash('teacher123', PASSWORD_DEFAULT);
$pinHash = password_hash('1234', PASSWORD_DEFAULT);

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("DELETE FROM staff_users");
$pdo->exec("DELETE FROM players");
$pdo->exec("ALTER TABLE staff_users AUTO_INCREMENT = 1");
$pdo->exec("ALTER TABLE players AUTO_INCREMENT = 1");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

$ins = $pdo->prepare("INSERT INTO staff_users (name, email, password_hash, role, is_active) VALUES (?,?,?,?,1)");
$ins->execute(['Admin GKJ', 'admin@gkjtangerang.org', $adminHash, 'admin']);
$adminId = (int)$pdo->lastInsertId();
$ins->execute(['Guru Maria', 'maria@gkjtangerang.org', $teacherHash, 'teacher']);
echo "[ok] admin/teacher seeded (admin id={$adminId})\n";

$ins2 = $pdo->prepare("INSERT INTO players (player_code, nickname, pin_hash, class_group, created_by) VALUES (?,?,?,?,?)");
$ins2->execute(['GKJ-1001','Samuel',$pinHash,'medium',$adminId]);
$ins2->execute(['GKJ-1002','Maria', $pinHash,'small', $adminId]);
$ins2->execute(['GKJ-1003','Daniel',$pinHash,'large', $adminId]);
echo "[ok] sample players seeded (PIN 1234)\n";

// Verify
foreach ($pdo->query('SHOW TABLES') as $row) {
    echo "  table: " . $row[0] . "\n";
}
echo "[done]\n";
