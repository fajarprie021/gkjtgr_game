<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$response = [
    'success' => true,
    'status' => 'ok',
    'app' => 'Bible Adventure Sekolah Minggu GKJ Tangerang',
    'timestamp' => date('c'),
];

$dbStatus = [
    'configured' => false,
    'connected' => false,
];

try {
    $dbFile = __DIR__ . '/../config/database.php';
    if (is_file($dbFile)) {
        $dbStatus['configured'] = true;
        require_once $dbFile;
        if (isset($pdo) && $pdo instanceof PDO) {
            try {
                $stmt = $pdo->query('SELECT 1');
                $dbStatus['connected'] = (bool)$stmt;
            } catch (Throwable $e) {
                $dbStatus['connected'] = false;
            }
        }
    }
} catch (Throwable $e) {
    $dbStatus['connected'] = false;
}

$response['database'] = $dbStatus;
$response['services'] = [
    'api' => 'ok',
    'database' => $dbStatus['connected'] ? 'ok' : 'degraded',
];

http_response_code($dbStatus['connected'] ? 200 : 200);
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
