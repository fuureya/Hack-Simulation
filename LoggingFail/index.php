<?php
session_start();
require_once 'db.php';

$error = '';

// VULN A09: Login failures TIDAK di-log sama sekali!
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM at_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['at_user_id'] = $user['id'];
        $_SESSION['at_username'] = $user['username'];
        $_SESSION['at_role'] = $user['role'];

        // VULN A09: Sukses login DI-LOG, tapi sangat minim info
        // Tidak ada IP, tidak ada timestamp detail, tidak ada user agent
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        // Log injection: username dari user bisa inject newline ke log!
        // VULN: Tidak ada sanitasi username sebelum ditulis ke log
        $logLine = date('Y-m-d H:i:s') . " | SUCCESS | Login: $username from $ip\n";
        // VULN: Log file bisa diakses publik langsung!
        file_put_contents(__DIR__ . '/audit.log', $logLine, FILE_APPEND);

        header("Location: dashboard.php");
        exit;
    } else {
        // VULN A09: Login GAGAL tidak di-log sama sekali!
        // Attacker bisa brute force tanpa jejak di log
        $error = "Username atau password salah.";
        // Tidak ada: file_put_contents(__DIR__ . '/audit.log', "FAILED login: $username\n", FILE_APPEND);
    }
}

// Handle log injection demo
if (isset($_GET['inject'])) {
    // VULN A09: Log injection — masukkan newline di username
    $injected = $_GET['inject'];
    $logLine = date('Y-m-d H:i:s') . " | SUCCESS | Login: $injected from 127.0.0.1\n";
    file_put_contents(__DIR__ . '/audit.log', $logLine, FILE_APPEND);
    header("Location: index.php?injected=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuditTrail System — A09 Logging Failures</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f766e 0%, #042f2e 100%);
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
            color: #042f2e;
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
            border-color: #0f766e;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #0f766e;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background: #0d6460;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .success-note {
            background: #ccfbf1;
            color: #115e59;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .hint {
            background: #f0fdfa;
            border-radius: 8px;
            padding: 14px;
            font-size: 12px;
            color: #115e59;
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

        .hint a {
            color: #0f766e;
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
            color: #5eead4;
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
            <span>📋</span>
            <h1>AuditTrail System</h1>
            <p>Activity Monitor — A09 Logging & Monitoring Failures Lab</p>
        </div>

        <div class="vuln-badge">
            <strong>⚠️ VULNERABILITY — A09 Security Logging Failures</strong>
            Login gagal tidak di-log, log file publik, log injection dimungkinkan.
        </div>

        <?php if (isset($_GET['injected'])): ?>
            <div class="success-note">✅ Log injection berhasil! <a href="audit.log" target="_blank">Lihat audit.log</a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error">❌
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="alice, bob, atau admin">
            <label>Password</label>
            <input type="password" name="password" placeholder="alice123, bob456, atau admin">
            <button type="submit" class="btn">📋 Masuk ke AuditTrail</button>
        </form>

        <div class="hint">
            <strong>🎯 Vulnerability Checklist:</strong>
            <ul>
                <li><a href="audit.log" target="_blank">📄 /audit.log</a> — Log file bisa diakses publik! Berisi
                    aktivitas internal</li>
                <li>Coba login dengan password salah berkali-kali → TIDAK ada di log!</li>
                <li>Log injection: <a
                        href="?inject=hacker%0A2026-01-01+00:00:00+%7C+SUCCESS+%7C+Login:+admin+from+1.1.1.1"
                        target="_blank">klik untuk inject log palsu</a></li>
            </ul>
            <br>
            <strong>💡 Credentials:</strong>
            alice/alice123 &nbsp;|&nbsp; bob/bob456 &nbsp;|&nbsp; admin/admin
        </div>
    </div>

    <div class="attack-panel">
        <h3>🏹 Attack Scenarios — A09 Logging & Monitoring Failures</h3>
        <ol>
            <li><strong>Brute Force Tanpa Jejak</strong>: Coba salah password 100x → TIDAK ada di log → attacker tidak
                terdeteksi</li>
            <li><strong>Log File Publik</strong>: <code>/audit.log</code> terbuka di browser → bocorkan aktivitas
                internal</li>
            <li><strong>Log Injection</strong>: Masukkan newline <code>%0A</code> di parameter → tambahkan baris log
                palsu</li>
            <li><strong>Tidak Ada Alerting</strong>: Tidak ada monitoring real-time → tidak ada notifikasi saat serangan
                terjadi</li>
            <li><strong>Log Insufficiency</strong>: Log login sukses hanya ada username — tidak ada IP asal, user-agent,
                atau detail lainnya</li>
        </ol>
    </div>
</body>

</html>