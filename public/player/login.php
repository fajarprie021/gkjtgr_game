<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

// Redirect jika sudah login
if (isset($_SESSION['player_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Ambil kode sesi dari URL (opsional, dari link guru)
$prefillCode = strtoupper(trim($_GET['code'] ?? ''));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Game - Bible Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(145deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            font-family: 'Segoe UI', sans-serif;
            display: flex; align-items: center; justify-content: center;
        }

        /* ── Card ── */
        .login-card {
            width: 100%; max-width: 420px;
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            color: #fff;
            box-shadow: 0 24px 64px rgba(0,0,0,.5);
        }
        .game-logo {
            text-align: center; margin-bottom: 2rem;
        }
        .game-logo .icon-wrap {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            border-radius: 50%; display: inline-flex;
            align-items: center; justify-content: center;
            font-size: 2rem; margin-bottom: .75rem;
            box-shadow: 0 0 32px rgba(245,87,108,.4);
        }
        .game-logo h2 { font-weight: 800; font-size: 1.6rem; margin: 0; }
        .game-logo p  { color: rgba(255,255,255,.6); margin: 0; font-size: .9rem; }

        /* ── Steps ── */
        .step-indicator {
            display: flex; gap: .5rem; justify-content: center;
            margin-bottom: 1.75rem;
        }
        .step-dot {
            width: 32px; height: 6px; border-radius: 3px;
            background: rgba(255,255,255,.2); transition: background .3s;
        }
        .step-dot.active  { background: #f5576c; }
        .step-dot.done    { background: #2ecc71; }

        .step { display: none; }
        .step.active { display: block; }

        /* ── Code Input ── */
        .code-input {
            text-align: center; font-size: 2.2rem; font-weight: 800;
            letter-spacing: .5rem; color: #f5576c;
            background: rgba(255,255,255,.08);
            border: 2px solid rgba(255,255,255,.2);
            border-radius: .75rem; width: 100%; padding: .75rem;
            text-transform: uppercase; outline: none;
            transition: border-color .2s;
        }
        .code-input:focus { border-color: #f5576c; }
        .code-input::placeholder { color: rgba(255,255,255,.25); letter-spacing: .25rem; }

        /* ── Player Grid ── */
        .player-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: .75rem;
            max-height: 320px; overflow-y: auto;
        }
        .player-card {
            background: rgba(255,255,255,.07);
            border: 2px solid rgba(255,255,255,.12);
            border-radius: 1rem; padding: 1rem .75rem;
            text-align: center; cursor: pointer;
            transition: all .2s; position: relative;
        }
        .player-card:hover { background: rgba(245,87,108,.2); border-color: #f5576c; transform: translateY(-2px); }
        .player-card.selected { background: rgba(245,87,108,.3); border-color: #f5576c; }
        .player-card.joined { opacity: .45; cursor: not-allowed; }
        .player-card.joined::after {
            content: 'Sudah masuk'; position: absolute; bottom: 4px;
            left: 50%; transform: translateX(-50%);
            font-size: .65rem; color: #aaa; white-space: nowrap;
        }
        .player-avatar {
            width: 48px; height: 48px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin: 0 auto .5rem;
        }
        .player-name { font-weight: 700; font-size: .95rem; }
        .player-class { font-size: .72rem; color: rgba(255,255,255,.5); margin-top: 2px; }

        /* ── PIN Pad ── */
        .pin-display {
            display: flex; gap: .5rem; justify-content: center; margin-bottom: 1.5rem;
        }
        .pin-dot {
            width: 20px; height: 20px; border-radius: 50%;
            border: 2px solid rgba(255,255,255,.4);
            transition: background .15s;
        }
        .pin-dot.filled { background: #f5576c; border-color: #f5576c; }

        .numpad {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: .5rem;
        }
        .num-btn {
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15);
            border-radius: .75rem; color: #fff; font-size: 1.4rem; font-weight: 700;
            padding: 1rem; cursor: pointer; transition: all .15s;
        }
        .num-btn:hover { background: rgba(255,255,255,.2); transform: scale(1.05); }
        .num-btn:active { transform: scale(.97); }
        .num-btn.del { font-size: 1.1rem; }
        .num-btn.empty { background: transparent; border-color: transparent; cursor: default; }
        .num-btn.empty:hover { transform: none; }

        /* ── Buttons ── */
        .btn-game {
            background: linear-gradient(135deg, #f5576c, #f093fb);
            color: #fff; border: none; border-radius: .75rem;
            padding: .85rem 1.5rem; font-size: 1rem; font-weight: 700;
            width: 100%; cursor: pointer; transition: opacity .2s;
        }
        .btn-game:hover { opacity: .9; }
        .btn-game:disabled { opacity: .5; cursor: not-allowed; }
        .btn-back {
            background: rgba(255,255,255,.08); color: rgba(255,255,255,.7);
            border: 1px solid rgba(255,255,255,.15); border-radius: .75rem;
            padding: .6rem 1.2rem; font-size: .875rem; cursor: pointer;
            transition: background .2s;
        }
        .btn-back:hover { background: rgba(255,255,255,.15); }

        /* ── Info boxes ── */
        .session-info {
            background: rgba(46,204,113,.12); border: 1px solid rgba(46,204,113,.3);
            border-radius: .75rem; padding: .875rem 1rem; margin-bottom: 1.25rem;
            font-size: .85rem;
        }
        .session-info .teacher { font-weight: 600; color: #2ecc71; }
        .alert-game {
            background: rgba(245,87,108,.15); border: 1px solid rgba(245,87,108,.35);
            border-radius: .75rem; padding: .75rem 1rem;
            font-size: .875rem; margin-bottom: 1rem; color: #ff8a95;
        }

        .step-title { font-weight: 700; font-size: 1.05rem; margin-bottom: .25rem; }
        .step-sub   { color: rgba(255,255,255,.55); font-size: .85rem; margin-bottom: 1.25rem; }

        /* ── Login lama ── */
        .alt-login { text-align: center; margin-top: 1.5rem; }
        .alt-login a { color: rgba(255,255,255,.45); font-size: .8rem; text-decoration: none; }
        .alt-login a:hover { color: rgba(255,255,255,.75); }

        @media (max-width: 480px) {
            .login-card { margin: 1rem; padding: 2rem 1.25rem; }
        }
    </style>
</head>
<body>
<div class="login-card">

    <!-- Logo -->
    <div class="game-logo">
        <div class="icon-wrap">✝️</div>
        <h2>Bible Adventure</h2>
        <p>Masuk ke Permainan</p>
    </div>

    <!-- Step indicator -->
    <div class="step-indicator">
        <div class="step-dot" id="dot1"></div>
        <div class="step-dot" id="dot2"></div>
        <div class="step-dot" id="dot3"></div>
    </div>

    <!-- Error global -->
    <div class="alert-game d-none" id="alertBox"></div>

    <!-- ── Step 1: Masukkan Kode Sesi ── -->
    <div class="step" id="step1">
        <div class="step-title"><i class="bi bi-key me-2"></i>Kode Permainan</div>
        <div class="step-sub">Ketik kode yang ditampilkan gurumu</div>

        <input type="text" class="code-input mb-3" id="codeInput"
               placeholder="ABCDEF" maxlength="8"
               value="<?= htmlspecialchars($prefillCode) ?>"
               autocomplete="off" autocorrect="off" spellcheck="false">

        <button class="btn-game" id="btnCheckCode" onclick="checkCode()">
            <i class="bi bi-arrow-right-circle me-2"></i>Lanjut
        </button>

        <div class="alt-login mt-3">
            <a href="../index.php"><i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda</a>
        </div>
    </div>

    <!-- ── Step 2: Pilih Nama ── -->
    <div class="step" id="step2">
        <div class="session-info" id="sessionInfo"></div>
        <div class="step-title"><i class="bi bi-person-check me-2"></i>Siapa kamu?</div>
        <div class="step-sub">Pilih namamu dari daftar</div>

        <div class="player-grid" id="playerGrid"></div>

        <div class="d-flex gap-2 mt-3">
            <button class="btn-back" onclick="goStep(1)">
                <i class="bi bi-arrow-left"></i> Kembali
            </button>
            <button class="btn-game" id="btnSelectPlayer" onclick="confirmPlayer()" disabled>
                <i class="bi bi-check2-circle me-2"></i>Ini Aku!
            </button>
        </div>
    </div>

    <!-- ── Step 3: PIN ── -->
    <div class="step" id="step3">
        <div class="step-title">
            <i class="bi bi-lock me-2"></i>Masukkan PIN
        </div>
        <div class="step-sub" id="pinGreeting">Halo! Ketik PIN-mu</div>

        <!-- PIN dots -->
        <div class="pin-display" id="pinDisplay">
            <div class="pin-dot" id="pd0"></div>
            <div class="pin-dot" id="pd1"></div>
            <div class="pin-dot" id="pd2"></div>
            <div class="pin-dot" id="pd3"></div>
            <div class="pin-dot" id="pd4"></div>
            <div class="pin-dot" id="pd5"></div>
        </div>

        <!-- Numpad -->
        <div class="numpad mb-3">
            <?php foreach([1,2,3,4,5,6,7,8,9] as $n): ?>
            <button class="num-btn" onclick="pinKey(<?= $n ?>)"><?= $n ?></button>
            <?php endforeach; ?>
            <button class="num-btn empty"></button>
            <button class="num-btn" onclick="pinKey(0)">0</button>
            <button class="num-btn del" onclick="pinDel()">⌫</button>
        </div>

        <div class="d-flex gap-2">
            <button class="btn-back" onclick="goStep(2); pinClear()">
                <i class="bi bi-arrow-left"></i>
            </button>
            <button class="btn-game" id="btnSubmitPin" onclick="submitLogin()" disabled>
                <i class="bi bi-play-fill me-2"></i>Mulai Main!
            </button>
        </div>
    </div>

</div>

<script>
// ── State ─────────────────────────────────────────────────────────────────────
let currentStep = 1;
let sessionData = null;   // {id, code, class_group, teacher_name, ...}
let playersData = [];
let selectedPlayerId   = null;
let selectedPlayerName = '';
let pinValue = '';

// ── Navigation ────────────────────────────────────────────────────────────────
function goStep(n) {
    document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
    document.getElementById('step' + n).classList.add('active');
    currentStep = n;
    updateDots();
    hideAlert();
}

function updateDots() {
    for (let i = 1; i <= 3; i++) {
        const d = document.getElementById('dot' + i);
        d.className = 'step-dot';
        if (i < currentStep) d.classList.add('done');
        else if (i === currentStep) d.classList.add('active');
    }
}

function showAlert(msg) {
    const el = document.getElementById('alertBox');
    el.textContent = msg;
    el.classList.remove('d-none');
}
function hideAlert() { document.getElementById('alertBox').classList.add('d-none'); }

// ── Step 1: Cek Kode ──────────────────────────────────────────────────────────
async function checkCode() {
    const code = document.getElementById('codeInput').value.trim().toUpperCase();
    if (code.length < 4) { showAlert('Kode sesi minimal 4 karakter'); return; }

    const btn = document.getElementById('btnCheckCode');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengecek...';
    hideAlert();

    try {
        const res  = await fetch('../api/session-join.php?code=' + encodeURIComponent(code));
        const data = await res.json();

        if (!data.success) { showAlert(data.error || 'Kode tidak valid'); return; }

        sessionData  = data.session;
        playersData  = data.players;
        renderPlayerGrid();

        const classLabel = { small: 'Small (SD 1–2)', medium: 'Medium (SD 3–4)', large: 'Large (SD 5–6)' };
        document.getElementById('sessionInfo').innerHTML =
            `<i class="bi bi-wifi me-1"></i> Sesi <strong>${sessionData.code}</strong> &nbsp;|&nbsp;` +
            `<span class="teacher">Guru: ${sessionData.teacher_name ?? '?'}</span> &nbsp;|&nbsp;` +
            `${classLabel[sessionData.class_group] ?? sessionData.class_group}`;

        goStep(2);
    } catch (e) {
        showAlert('Koneksi gagal, coba lagi.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-right-circle me-2"></i>Lanjut';
    }
}

// ── Step 2: Pilih Pemain ──────────────────────────────────────────────────────
const avatars = ['🦁','🐯','🦊','🐻','🐼','🦋','🐬','🦅','🌟','🎯','⚡','🌈'];

function renderPlayerGrid() {
    const grid = document.getElementById('playerGrid');
    grid.innerHTML = '';
    if (playersData.length === 0) {
        grid.innerHTML = '<div class="text-center py-4" style="color:rgba(255,255,255,.5);grid-column:1/-1">Belum ada pemain terdaftar untuk kelas ini.</div>';
        return;
    }
    playersData.forEach((p, i) => {
        const card = document.createElement('div');
        card.className = 'player-card' + (p.already_joined ? ' joined' : '');
        card.dataset.id   = p.id;
        card.dataset.name = p.nickname;
        card.innerHTML = `
            <div class="player-avatar">${avatars[i % avatars.length]}</div>
            <div class="player-name">${p.nickname}</div>
            <div class="player-class">${p.class_group}</div>
        `;
        if (!p.already_joined) {
            card.onclick = () => selectPlayer(card, p.id, p.nickname);
        }
        grid.appendChild(card);
    });
}

function selectPlayer(card, id, name) {
    document.querySelectorAll('.player-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    selectedPlayerId   = id;
    selectedPlayerName = name;
    document.getElementById('btnSelectPlayer').disabled = false;
}

function confirmPlayer() {
    if (!selectedPlayerId) return;
    document.getElementById('pinGreeting').textContent = `Halo, ${selectedPlayerName}! Ketik PIN-mu 👋`;
    pinClear();
    goStep(3);
}

// ── Step 3: PIN Pad ───────────────────────────────────────────────────────────
function pinKey(n) {
    if (pinValue.length >= 6) return;
    pinValue += n;
    updatePinDots();
    document.getElementById('btnSubmitPin').disabled = (pinValue.length < 4);
}
function pinDel() {
    pinValue = pinValue.slice(0, -1);
    updatePinDots();
    document.getElementById('btnSubmitPin').disabled = (pinValue.length < 4);
}
function pinClear() {
    pinValue = '';
    updatePinDots();
    document.getElementById('btnSubmitPin').disabled = true;
}
function updatePinDots() {
    for (let i = 0; i < 6; i++) {
        const d = document.getElementById('pd' + i);
        if (i < pinValue.length) d.classList.add('filled');
        else d.classList.remove('filled');
    }
}

// ── Submit Login ──────────────────────────────────────────────────────────────
async function submitLogin() {
    if (pinValue.length < 4 || !selectedPlayerId || !sessionData) return;

    const btn = document.getElementById('btnSubmitPin');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Masuk...';
    hideAlert();

    try {
        const res = await fetch('../api/session-join.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                session_code: sessionData.code,
                player_id:    selectedPlayerId,
                pin:          pinValue,
            })
        });
        const data = await res.json();

        if (data.success) {
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Berhasil! Mengalihkan...';
            setTimeout(() => { window.location.href = data.redirect || 'dashboard.php'; }, 600);
        } else {
            showAlert(data.error || 'PIN salah, coba lagi');
            pinClear();
        }
    } catch (e) {
        showAlert('Koneksi gagal, coba lagi.');
    } finally {
        if (!document.querySelector('.bi-check-circle-fill')) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-fill me-2"></i>Mulai Main!';
        }
    }
}

// ── Enter key support ─────────────────────────────────────────────────────────
document.getElementById('codeInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') checkCode();
});

// ── Init ──────────────────────────────────────────────────────────────────────
goStep(1);
<?php if ($prefillCode): ?>
// Kode dari URL — langsung cek otomatis
window.addEventListener('DOMContentLoaded', () => setTimeout(checkCode, 300));
<?php endif; ?>
</script>
</body>
</html>