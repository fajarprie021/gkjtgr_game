<?php
require_once '../../config/security.php';
sendSecurityHeaders();
require_once '../../config/database.php';
require_once '../../config/auth.php';

// Redirect if already logged in as admin
if (isset($_SESSION['staff_id']) && ($_SESSION['staff_role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi.';
    } elseif (!$pdo) {
        $error = 'Database tidak terhubung. Hubungi administrator.';
    } else {
        if (staffLogin($pdo, $email, $password)) {
            // Only allow admin role
            if (($_SESSION['staff_role'] ?? '') === 'admin') {
                header('Location: dashboard.php');
                exit;
            } else {
                staffLogout();
                $error = 'Akun Anda tidak memiliki akses admin.';
            }
        } else {
            $error = 'Email atau password tidak sesuai.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Bible Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3a5f 0%, #2d6a4f 100%);
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 12px 48px rgba(0,0,0,0.25);
            padding: 2.5rem 2rem;
        }
        .brand-icon {
            font-size: 3.5rem;
            color: #1e3a5f;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="text-center mb-4">
        <i class="bi bi-shield-lock-fill brand-icon"></i>
        <h2 class="fw-bold mt-2 mb-0">Bible Adventure</h2>
        <p class="text-muted">Portal Admin</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if (!$pdo): ?>
    <div class="alert alert-warning">
        <i class="bi bi-database-exclamation me-2"></i>
        Database tidak terhubung.
    </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="mb-3">
            <label class="form-label fw-semibold">Email Admin</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="admin@gkjtangerang.org"
                       required autofocus <?= !$pdo ? 'disabled' : '' ?>>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control"
                       placeholder="Password" required <?= !$pdo ? 'disabled' : '' ?>>
            </div>
        </div>

        <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold" <?= !$pdo ? 'disabled' : '' ?>>
            <i class="bi bi-shield-check me-2"></i>Masuk sebagai Admin
        </button>
    </form>

    <hr class="my-3">
    <div class="text-center small text-muted mb-2">
        Demo: <code>admin@gkjtangerang.org</code> / <code>admin123</code>
    </div>
    <div class="text-center">
        <a href="../" class="btn btn-link btn-sm text-muted">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Home
        </a>
        <span class="text-muted mx-2">|</span>
        <a href="../teacher/login.php" class="btn btn-link btn-sm text-muted">Login Guru</a>
    </div>
</div>
</body>
</html>