<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

// Redirect if already logged in
if (isset($_SESSION['player_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playerCode = strtoupper(trim($_POST['player_code'] ?? ''));
    $pin = $_POST['pin'] ?? '';
    
    if (empty($playerCode) || empty($pin)) {
        $error = 'Kode pemain dan PIN harus diisi.';
    } else {
        if (playerLogin($pdo, $playerCode, $pin)) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Kode pemain atau PIN tidak sesuai.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pemain - Bible Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 2rem;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header i {
            font-size: 3rem;
            color: #f5576c;
            margin-bottom: 1rem;
        }
        .pin-input {
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-joystick"></i>
                <h2>Bible Adventure</h2>
                <p class="text-muted">Petualanganku</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="player_code" class="form-label">Kode Pemain</label>
                    <input type="text" class="form-control text-uppercase" id="player_code" name="player_code" 
                           placeholder="GKJ-1001"
                           value="<?= htmlspecialchars($_POST['player_code'] ?? '') ?>" 
                           required autofocus>
                    <small class="form-text text-muted">Contoh: GKJ-1001</small>
                </div>

                <div class="mb-3">
                    <label for="pin" class="form-label">PIN</label>
                    <input type="password" class="form-control pin-input" id="pin" name="pin" 
                           maxlength="4" pattern="[0-9]{4}" 
                           placeholder="••••" required>
                    <small class="form-text text-muted">4 digit angka</small>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>

                <div class="text-center text-muted small">
                    <p class="mb-2">Akun Demo:</p>
                    <p class="mb-0">GKJ-1001 / PIN: 1234</p>
                    <p class="mb-0">GKJ-1002 / PIN: 1234</p>
                </div>
            </form>

            <hr class="my-3">

            <div class="text-center">
                <a href="../join.php" class="btn btn-outline-primary mb-2 w-100">
                    <i class="bi bi-people me-2"></i>Main Sebagai Tamu
                </a>
                <a href="../" class="btn btn-link">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>