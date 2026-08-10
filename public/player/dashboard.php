<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

requirePlayerAuth();
$player = getPlayer();

// Get player progress
$stmt = $pdo->prepare("
    SELECT COUNT(*) as completed_count
    FROM player_story_progress 
    WHERE player_id = ? AND status = 'completed'
");
$stmt->execute([$player['id']]);
$progress = $stmt->fetch();
$completedCount = $progress['completed_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petualanganku - Bible Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/theme.css">
    <style>
        .player-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: #f5576c;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>
    <div class="player-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="bi bi-joystick me-2"></i>Halo, <?= htmlspecialchars($player['nickname']) ?>!</h1>
                    <p class="mb-0">
                        <span class="badge bg-light text-dark me-2">
                            <?= $player['class_group'] === 'small' ? 'Kelas Kecil' : 
                                ($player['class_group'] === 'medium' ? 'Kelas Madya' : 'Kelas Besar') ?>
                        </span>
                        <span class="badge bg-light text-dark"><?= htmlspecialchars($player['player_code']) ?></span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="logout.php" class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i>Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="progress-circle">
                            <span><?= $completedCount ?></span>
                        </div>
                        <h6>Cerita Selesai</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Perjalanan Alkitabmu</h5>
                        <p>Terus jelajahi cerita-cerita Alkitab dan pelajari lebih banyak tentang perjalanan iman!</p>
                        <div class="d-grid gap-2">
                            <a href="../map.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-map me-2"></i>Lanjutkan Petualangan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-1 text-primary mb-3"></i>
                        <h5 class="card-title">Gabung Game Kelas</h5>
                        <p class="card-text">Masukkan kode dari guru untuk bermain bersama teman</p>
                        <a href="join.php" class="btn btn-outline-primary">
                            <i class="bi bi-key me-2"></i>Masukkan Kode
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history display-1 text-success mb-3"></i>
                        <h5 class="card-title">Progres Saya</h5>
                        <p class="card-text">Lihat cerita yang sudah kamu selesaikan</p>
                        <a href="progress.php" class="btn btn-outline-success">
                            <i class="bi bi-list-check me-2"></i>Lihat Progres
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>