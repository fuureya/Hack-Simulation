<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['sl_user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['sl_user_id'];
$username = $_SESSION['sl_username'];
$role = $_SESSION['sl_role'];
$sessionId = session_id();

$stmt = $pdo->prepare("SELECT * FROM sl_users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// VULN A07: Logout tidak menghapus session di server — hanya hapus cookie
if (isset($_GET['logout'])) {
    // VULN: session_destroy() tidak dipanggil!
    // Hanya unset cookie saja
    setcookie(session_name(), '', time() - 3600, '/');
    setcookie('remember_me', '', time() - 3600, '/');
    // Session di server MASIH ADA dan bisa dipakai!
    header("Location: index.php");
    exit;
}

// Decode remember_me token untuk demo
$decodedToken = '';
if ($user['remember_token']) {
    $decodedToken = base64_decode($user['remember_token']);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>SecureLogin Corp — Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f3ff;
        }

        .nav {
            background: #4f46e5;
            color: white;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav h1 {
            font-size: 18px;
        }

        .nav a {
            color: #c7d2fe;
            text-decoration: none;
            font-size: 14px;
        }

        .container {
            max-width: 960px;
            margin: 28px auto;
            padding: 0 20px;
        }

        .vuln-panel {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .vuln-panel h3 {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .vuln-panel ul {
            font-size: 12px;
            color: #78350f;
            padding-left: 16px;
        }

        .vuln-panel li {
            margin-bottom: 3px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        }

        .card h2 {
            font-size: 15px;
            color: #312e81;
            margin-bottom: 14px;
            border-bottom: 1px solid #ede9fe;
            padding-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f5f3ff;
            font-size: 13px;
        }

        .info-row:last-child {
            border: none;
        }

        .info-row label {
            color: #6b7280;
            font-size: 12px;
        }

        .info-row span {
            color: #1e1b4b;
            font-weight: 600;
            font-family: monospace;
        }

        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-admin {
            background: #ede9fe;
            color: #4f46e5;
        }

        .badge-user {
            background: #e0f2fe;
            color: #0369a1;
        }

        .vuln-value {
            color: #dc2626 !important;
            background: #fef2f2;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .attack-box {
            background: #ede9fe;
            border-radius: 8px;
            padding: 12px;
            margin-top: 12px;
            font-size: 12px;
            color: #3730a3;
        }

        .attack-box strong {
            display: block;
            margin-bottom: 6px;
        }

        .attack-box ul {
            padding-left: 16px;
        }

        code {
            background: #ddd6fe;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 11px;
        }

        .btn-logout {
            background: #dc2626;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-vuln-logout {
            background: #f59e0b;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>

<body>
    <nav class="nav">
        <h1>🔐 SecureLogin Corp — SSO Dashboard</h1>
        <div>
            <span style="margin-right:14px;">Selamat datang, <strong>
                    <?= htmlspecialchars($username) ?>
                </strong></span>
            <a href="?logout=1" class="btn-logout">Logout (VULN)</a>
        </div>
    </nav>

    <div class="container">
        <div class="vuln-panel">
            <h3>⚠️ OWASP A07 — Authentication Failures: Vulnerabilities Lab Ini</h3>
            <ul>
                <li><strong>Session Fixation</strong>: Session ID tidak di-regenerate setelah login — ID sebelum login
                    sama dengan setelah login</li>
                <li><strong>Session tidak expire saat logout</strong>: Tombol Logout hanya hapus cookie, session server
                    tetap hidup</li>
                <li><strong>Brute Force</strong>: Tidak ada rate limiting atau account lockout</li>
                <li><strong>Predictable Remember-Me Token</strong>: Token = base64(user_id:timestamp) — bisa diprediksi
                </li>
                <li><strong>Password lemah diterima</strong>: "pass", "123456", "admin" lolos tanpa kompleksitas</li>
            </ul>
        </div>

        <div class="card">
            <h2>🪪 Informasi Session (Vulnerability Demo)</h2>
            <div class="info-row">
                <label>Username</label>
                <span>
                    <?= htmlspecialchars($username) ?> <span class="badge badge-<?= $role ?>">
                        <?= strtoupper($role) ?>
                    </span>
                </span>
            </div>
            <div class="info-row">
                <label>Session ID (TIDAK berubah setelah login!)</label>
                <span class="vuln-value">
                    <?= htmlspecialchars($sessionId) ?>
                </span>
            </div>
            <div class="info-row">
                <label>Remember-Me Token (PREDICTABLE)</label>
                <span class="vuln-value">
                    <?= htmlspecialchars($user['remember_token'] ?: 'Tidak aktif — centang "Ingat saya" saat login') ?>
                </span>
            </div>
            <?php if ($decodedToken): ?>
                <div class="info-row">
                    <label>Token Decoded (base64)</label>
                    <span class="vuln-value">
                        <?= htmlspecialchars($decodedToken) ?> → format: user_id:timestamp_hari_ini
                    </span>
                </div>
            <?php endif; ?>
            <div class="info-row">
                <label>Password</label>
                <span class="vuln-value">Lemah! Diterima tanpa validasi kompleksitas</span>
            </div>

            <div class="attack-box">
                <strong>🎯 Cara Exploit Session Fixation:</strong>
                <ul>
                    <li>Step 1: Buka <code>/?PHPSESSID=HACKER_CONTROLLED_SESSION</code></li>
                    <li>Step 2: Kirim URL tersebut ke korban (misalnya via phishing email)</li>
                    <li>Step 3: Korban login menggunakan session ID yang sudah kamu tentukan</li>
                    <li>Step 4: Kamu sudah bisa akses akun korban dengan session ID yang sama!</li>
                    <li>Kenapa bisa? Karena <code>session_regenerate_id(true)</code> tidak dipanggil setelah login</li>
                </ul>
            </div>
            <div class="attack-box" style="margin-top:8px;background:#fff7ed;border:none;">
                <strong>🎯 Cara Exploit Session tidak di-invalidasi saat Logout:</strong>
                <ul>
                    <li>Step 1: Catat Session ID saat ini: <code><?= htmlspecialchars($sessionId) ?></code></li>
                    <li>Step 2: Klik Logout → session cookie dihapus dari browser</li>
                    <li>Step 3: Buka browser baru atau gunakan curl:
                        <code>curl -b "PHPSESSID=<?= htmlspecialchars($sessionId) ?>" http://localhost:8006/dashboard.php</code>
                    </li>
                    <li>Step 4: Kamu masih bisa akses dashboard! Session server belum dihapus.</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h2>📊 Menu SSO</h2>
            <p style="color:#6b7280;font-size:13px;">Ini adalah contoh dashboard setelah berhasil login. Di dunia nyata,
                ini bisa berisi data sensitif perusahaan.</p>
            <div style="margin-top:16px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <div style="background:#f0fdf4;border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:24px;">📧</div>
                    <div style="font-size:13px;color:#166534;font-weight:600;margin-top:4px;">Email Corporate</div>
                </div>
                <div style="background:#eff6ff;border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:24px;">📁</div>
                    <div style="font-size:13px;color:#1d4ed8;font-weight:600;margin-top:4px;">File Server</div>
                </div>
                <div style="background:#fef3c7;border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:24px;">💰</div>
                    <div style="font-size:13px;color:#92400e;font-weight:600;margin-top:4px;">Payroll System</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>