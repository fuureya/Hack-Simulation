<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // VULN A02: Password di-hash MD5 tanpa salt sebelum dibandingkan
    $hashed = md5($password);

    $stmt = $pdo->prepare("SELECT * FROM sv_users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $hashed]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // VULN A02: Data sensitif disimpan di cookie PLAINTEXT (tidak dienkripsi)
        setcookie('user_info', json_encode([
            'id' => $user['id'],
            'username' => $user['username'],
            'credit_card' => $user['credit_card'],  // VULN: nomor kartu di cookie!
            'pin' => $user['pin'],            // VULN: PIN di cookie!
            'balance' => $user['balance'],
        ]), time() + 3600, '/');

        // VULN A02: Token "remember me" adalah MD5 dari username — mudah diprediksi
        setcookie('remember_token', md5($user['username'] . 'safevault'), time() + 86400, '/');

        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafeVault Bank — Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a3a5c 0%, #0d2137 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            width: 400px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 8px;
        }

        .logo h1 {
            font-size: 24px;
            color: #1a3a5c;
            font-weight: 700;
        }

        .logo p {
            color: #6b7280;
            font-size: 13px;
            margin-top: 4px;
        }

        .vuln-badge {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 20px;
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
            color: #374151;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
            transition: border-color 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #1a3a5c;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: #1a3a5c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #0d2137;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .hint {
            margin-top: 20px;
            padding: 12px;
            background: #f0f9ff;
            border-radius: 8px;
            font-size: 12px;
            color: #0369a1;
        }

        .hint strong {
            display: block;
            margin-bottom: 6px;
        }

        .hint ul {
            padding-left: 16px;
        }

        .hint li {
            margin-bottom: 2px;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="logo">
            <span class="logo-icon">🏦</span>
            <h1>SafeVault Bank</h1>
            <p>Internet Banking — A02 Cryptographic Failures Lab</p>
        </div>

        <div class="vuln-badge">
            <strong>⚠️ VULNERABILITY DEMO — A02 Cryptographic Failures</strong>
            Password disimpan sebagai MD5 hash tanpa salt. Cookie berisi data sensitif plaintext.
        </div>

        <?php if ($error): ?>
            <div class="error">❌
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            <button type="submit" class="btn">🔐 Masuk ke Akun</button>
        </form>

        <div class="hint">
            <strong>🎯 Test Credentials:</strong>
            <ul>
                <li><strong>alice</strong> / password123</li>
                <li><strong>bob</strong> / qwerty</li>
                <li><strong>admin</strong> / admin</li>
            </ul>
            <br>
            <strong>💡 Attack Hints:</strong>
            <ul>
                <li>Inspect cookie setelah login — data sensitif ada di sana!</li>
                <li>Password hash MD5 bisa di-crack di <a href="https://crackstation.net"
                        target="_blank">crackstation.net</a></li>
                <li>Cek URL saat transaksi — data sensitif mungkin di GET params</li>
            </ul>
        </div>
    </div>
</body>

</html>