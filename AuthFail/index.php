<?php
// VULN A07: Session Fixation — kita izinkan session ID dari GET/COOKIE sebelum login
// Jika ?PHPSESSID=hackercontrolled dikirim sebelum login, session itu akan digunakan
if (isset($_GET['PHPSESSID'])) {
    // VULN: Attacker bisa set session ID sebelum korban login
    session_id($_GET['PHPSESSID']);
}
session_start();
require_once 'db.php';

$error = '';
$success = '';

// VULN A07: Tidak ada account lockout setelah banyak percobaan gagal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM sl_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // VULN A07: Session ID TIDAK di-regenerate setelah login!
        // Seharusnya: session_regenerate_id(true);
        // Karena tidak di-regenerate, session fixation berhasil!

        $_SESSION['sl_user_id'] = $user['id'];
        $_SESSION['sl_username'] = $user['username'];
        $_SESSION['sl_role'] = $user['role'];

        // VULN A07: Remember-me token predictable — hanya nomor urut user
        if (isset($_POST['remember'])) {
            // VULN: token = user_id + timestamp yang predictable
            $token = base64_encode($user['id'] . ':' . strtotime('today'));
            setcookie('remember_me', $token, time() + 86400 * 30, '/');
            $pdo->prepare("UPDATE sl_users SET remember_token = ? WHERE id = ?")->execute([$token, $user['id']]);
        }

        header("Location: dashboard.php");
        exit;
    } else {
        // VULN A07: Tidak ada lockout, tidak ada delay — brute force bebas
        $error = "Username atau password salah.";
    }
}

// Handle remember_me token
if (!isset($_SESSION['sl_user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $pdo->prepare("SELECT * FROM sl_users WHERE remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['sl_user_id'] = $user['id'];
        $_SESSION['sl_username'] = $user['username'];
        $_SESSION['sl_role'] = $user['role'];
        header("Location: dashboard.php");
        exit;
    }
}

// VULN A07: Logout tidak invalidasi session server-side
if (isset($_GET['logout_test'])) {
    // VULN: session_destroy() tidak dipanggil di sini sebagai demo
    // Token lama masih valid!
    $success = "Percobaan logout tanpa destroy session — session masih valid di server!";
}

$currentSessId = session_id();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureLogin Corp — A07 Auth Failures</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #312e81 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
        }

        .login-box {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 440px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo span {
            font-size: 48px;
        }

        .logo h1 {
            font-size: 22px;
            color: #1e1b4b;
            font-weight: 700;
            margin-top: 8px;
        }

        .logo p {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .vuln-badge {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 12px;
            color: #92400e;
        }

        .vuln-badge strong {
            display: block;
            margin-bottom: 4px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        input[type=text],
        input[type=password] {
            width: 100%;
            padding: 11px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 14px;
        }

        input:focus {
            outline: none;
            border-color: #4f46e5;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .checkbox-row label {
            margin-bottom: 0;
            font-weight: 400;
            font-size: 13px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background: #4338ca;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .sess-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px;
            margin-top: 14px;
            font-size: 12px;
            color: #475569;
            font-family: monospace;
        }

        .hint {
            background: #ede9fe;
            border-radius: 8px;
            padding: 14px;
            font-size: 12px;
            color: #3730a3;
            margin-top: 14px;
        }

        .hint strong {
            display: block;
            margin-bottom: 6px;
        }

        .hint ul {
            padding-left: 16px;
        }

        .hint li {
            margin-bottom: 3px;
        }

        .attack-panel {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 16px 20px;
            width: 440px;
            color: white;
            font-size: 13px;
        }

        .attack-panel h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #a5b4fc;
        }

        .attack-panel ol {
            padding-left: 18px;
        }

        .attack-panel li {
            margin-bottom: 6px;
            line-height: 1.5;
        }

        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 1px 5px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="logo">
            <span>🔐</span>
            <h1>SecureLogin Corp</h1>
            <p>SSO Portal — A07 Identification & Authentication Failures Lab</p>
        </div>

        <div class="vuln-badge">
            <strong>⚠️ VULNERABILITY — A07 Authentication Failures</strong>
            Session fixation, tidak ada lockout, remember-me predictable, session tidak expire.
        </div>

        <?php if ($error): ?>
            <div class="error">❌
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success">✅
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="alice, bob, atau admin">
            <label>Password</label>
            <input type="password" name="password" placeholder="pass, 123456, atau admin">
            <div class="checkbox-row">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Ingat saya (30 hari)</label>
            </div>
            <button type="submit" class="btn">🔑 Login</button>
        </form>

        <div class="sess-info">
            <strong>Session ID saat ini:</strong><br>
            <?= htmlspecialchars($currentSessId) ?>
            <br><small style="color:#94a3b8;">(setelah login, perhatikan apakah session ID berubah — VULN jika
                tidak!)</small>
        </div>

        <div class="hint">
            <strong>🎯 Test Credentials & Attack Scenarios:</strong>
            <ul>
                <li><strong>alice</strong> / pass &nbsp;|&nbsp; <strong>bob</strong> / 123456 &nbsp;|&nbsp;
                    <strong>admin</strong> / admin</li>
                <li>Session Fixation: <code>?PHPSESSID=hacker123</code> sebelum alice login</li>
                <li>Brute force: tidak ada lockout sama sekali</li>
                <li>Logout test: <a href="?logout_test=1">klik di sini</a> → session tidak invalid</li>
            </ul>
        </div>
    </div>

    <div class="attack-panel">
        <h3>🏹 Attack Scenarios — A07 Auth Failures</h3>
        <ol>
            <li><strong>Session Fixation</strong>: Buka <code>/?PHPSESSID=ATTACKER_ID</code> → kirim link ke korban →
                setelah korban login, attacker pakai session yang sama</li>
            <li><strong>Session Not Invalidated on Logout</strong>: Logout → session ID lama masih valid di server</li>
            <li><strong>Brute Force Password</strong>: Tidak ada lockout, tidak ada CAPTCHA</li>
            <li><strong>Predictable Remember-Me Token</strong>: Token = base64(<code>user_id:timestamp_today</code>) →
                mudah diprediksi</li>
            <li><strong>Weak Password Accepted</strong>: Password "pass" & "123456" diterima tanpa kompleksitas</li>
        </ol>
    </div>
</body>

</html>