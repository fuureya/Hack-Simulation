<?php
session_start();

$error = '';
// VULN A05: Default credentials tidak diganti (admin:admin)
$validCredentials = [
    'admin' => 'admin',    // VULN: default cred!
    'devops' => 'devops',   // VULN: default cred!
    'root' => 'root',     // VULN: default cred!
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    if (isset($validCredentials[$u]) && $validCredentials[$u] === $p) {
        $_SESSION['sm_user'] = $u;
        header("Location: portal.php");
        exit;
    } else {
        // VULN A05: Error message verbose — beri tahu username benar/salah!
        if (!isset($validCredentials[$u])) {
            $error = "User <strong>'$u'</strong> tidak ditemukan dalam sistem.";
        } else {
            $error = "Password salah untuk user <strong>'$u'</strong>. Password tersimpan di database versi 8.2.";
        }
    }
}

// VULN A05: Trigger error PHP yang verbose untuk demo
if (isset($_GET['trigger_error'])) {
    $undefined_var; // Notice: undefined
    $arr = ['a' => 1];
    echo $arr['nonexistent']; // Warning
    require_once 'nonexistent_file.php'; // Fatal error — bocorkan path!
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevOps Portal — A05 Security Misconfiguration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
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
            width: 420px;
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
            color: #0f172a;
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
            border-color: #475569;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #1e293b;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background: #0f172a;
        }

        .error {
            background: #fee2e2;
            color: #7f1d1d;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .hint {
            background: #f8fafc;
            border-radius: 8px;
            padding: 14px;
            font-size: 12px;
            color: #475569;
            margin-top: 14px;
        }

        .hint strong {
            display: block;
            margin-bottom: 6px;
            color: #1e293b;
        }

        .hint ul {
            padding-left: 16px;
        }

        .hint li {
            margin-bottom: 3px;
        }

        .hint a {
            color: #3b82f6;
        }

        .attack-panel {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 16px 20px;
            width: 420px;
            color: white;
            font-size: 13px;
        }

        .attack-panel h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #fbbf24;
        }

        .attack-panel ul {
            padding-left: 18px;
        }

        .attack-panel li {
            margin-bottom: 6px;
            line-height: 1.5;
        }

        .attack-panel a {
            color: #7dd3fc;
        }

        code {
            background: rgba(0, 0, 0, 0.4);
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="logo">
            <span>⚙️</span>
            <h1>DevOps Portal</h1>
            <p>Internal Dashboard — A05 Security Misconfiguration Lab</p>
        </div>

        <div class="vuln-badge">
            <strong>⚠️ VULNERABILITY — A05 Security Misconfiguration</strong>
            Default credentials aktif, file sensitif publik, verbose errors, phpinfo() terbuka.
        </div>

        <?php if ($error): ?>
            <div class="error">❌
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Coba: admin, devops, root">
            <label>Password</label>
            <input type="password" name="password" placeholder="Coba password yang sama dengan username">
            <button type="submit" class="btn">🔓 Login ke DevOps Portal</button>
        </form>

        <div class="hint">
            <strong>🎯 Vulnerability Checklist:</strong>
            <ul>
                <li><a href="info.php" target="_blank">🔗 /info.php</a> — phpinfo() terbuka publik</li>
                <li><a href=".env" target="_blank">🔗 /.env</a> — Environment file publik (AWS keys!)</li>
                <li><a href="config.bak" target="_blank">🔗 /config.bak</a> — File backup konfigurasi</li>
                <li><a href="database_backup.sql.bak" target="_blank">🔗 /database_backup.sql.bak</a> — Backup DB publik
                </li>
                <li><a href="?trigger_error=1" target="_blank">🔗 ?trigger_error=1</a> — Verbose PHP error</li>
                <li><a href="/" target="_blank">🔗 / (root)</a> — Directory listing aktif!</li>
            </ul>
            <br>
            <strong>🔑 Default Credentials:</strong>
            <ul>
                <li>admin / admin</li>
                <li>devops / devops</li>
                <li>root / root</li>
            </ul>
        </div>
    </div>

    <div class="attack-panel">
        <h3>🏹 Attack Scenarios — A05 Security Misconfiguration</h3>
        <ul>
            <li><strong>Directory Listing</strong>: Buka <code>/</code> atau subfolder mana saja — semua file tampil
            </li>
            <li><strong>Sensitive Files</strong>: <code>/.env</code> berisi AWS keys, DB password, payment gateway key
            </li>
            <li><strong>phpinfo()</strong>: <code>/info.php</code> bocorkan versi PHP, modul, env vars, dan path server
            </li>
            <li><strong>Verbose Error</strong>: <code>?trigger_error=1</code> → PHP error bocorkan path file server</li>
            <li><strong>Default Creds</strong>: Login dengan admin/admin → berhasil!</li>
            <li><strong>User Enumeration</strong>: Pesan error berbeda untuk "user tidak ada" vs "password salah"</li>
        </ul>
    </div>
</body>

</html>