<?php
session_start();
require_once 'db.php';

$error = '';
$step = $_GET['step'] ?? 'login'; // login | reset

// VULN A04: Tidak ada rate limiting — brute force bebas!
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM ql_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // VULN A04: failed_attempts tidak di-lockout, hanya counter saja
    if ($user && password_verify($password, $user['password'])) {
        $pdo->prepare("UPDATE ql_users SET failed_attempts = 0 WHERE id = ?")->execute([$user['id']]);
        $_SESSION['ql_user_id'] = $user['id'];
        $_SESSION['ql_username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        if ($user) {
            $pdo->prepare("UPDATE ql_users SET failed_attempts = failed_attempts + 1 WHERE id = ?")->execute([$user['id']]);
        }
        // VULN A04: Tidak ada lockout! Boleh terus coba!
        $error = "Username atau password salah. Silakan coba lagi.";
    }
}

// VULN A04: Reset password hanya dengan security question + jawaban tebakan
$resetSuccess = false;
$resetError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'reset') {
    $username = trim($_POST['username'] ?? '');
    $answer = strtolower(trim($_POST['answer'] ?? ''));

    $stmt = $pdo->prepare("SELECT * FROM ql_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && strtolower($user['security_answer']) === $answer) {
        // VULN A04: Password langsung di-reset tanpa verifikasi email/OTP!
        $newPass = password_hash('NewPass123!', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE ql_users SET password = ? WHERE id = ?")->execute([$newPass, $user['id']]);
        $resetSuccess = true;
    } else {
        $resetError = 'Jawaban security question salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickLoan — A04 Insecure Design Lab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #16a34a 0%, #064e3b 100%);
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
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
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
            color: #064e3b;
            font-weight: 700;
            margin-top: 8px;
        }

        .logo p {
            font-size: 12px;
            color: #6b7280;
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

        input[type=text],
        input[type=password] {
            width: 100%;
            padding: 11px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 14px;
            transition: border-color 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #16a34a;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background: #15803d;
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

        .link {
            text-align: center;
            margin-top: 14px;
            font-size: 13px;
        }

        .link a {
            color: #16a34a;
            text-decoration: none;
            font-weight: 600;
        }

        .hint {
            background: #f0fdf4;
            border-radius: 8px;
            padding: 14px;
            font-size: 12px;
            color: #166534;
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
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 16px 20px;
            width: 420px;
            color: white;
            font-size: 13px;
        }

        .attack-panel h3 {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .attack-panel ol {
            padding-left: 18px;
        }

        .attack-panel li {
            margin-bottom: 6px;
            line-height: 1.5;
        }

        .attack-panel code {
            background: rgba(0, 0, 0, 0.3);
            padding: 1px 5px;
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="logo">
            <span>💸</span>
            <h1>QuickLoan App</h1>
            <p>Pinjaman Online Instan — A04 Insecure Design Lab</p>
        </div>

        <?php if ($step === 'login'): ?>
            <div class="vuln-badge">
                <strong>⚠️ VULNERABILITY — A04 Insecure Design</strong>
                Tidak ada rate limiting! Coba brute force password tanpa batas.
            </div>

            <?php if ($error): ?>
                <div class="error">❌
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label>Username</label>
                <input type="text" name="username" placeholder="Username">
                <label>Password</label>
                <input type="password" name="password" placeholder="Password">
                <button type="submit" class="btn">🚀 Ajukan Pinjaman</button>
            </form>

            <div class="link">
                Lupa password? <a href="?step=reset">Reset via Security Question</a>
            </div>

            <div class="hint">
                <strong>🎯 Test Credentials:</strong>
                <ul>
                    <li>budi / Budi1234!</li>
                    <li>sari / Sari5678!</li>
                    <li>admin / admin</li>
                </ul>
                <br>
                <strong>💡 Attack Hints:</strong>
                <ul>
                    <li>Brute force: tidak ada lockout & tidak ada CAPTCHA</li>
                    <li>Reset: security question bisa ditebak (nama hewan, kota)</li>
                    <li>Business logic: manipulasi amount saat ajukan pinjaman</li>
                </ul>
            </div>

        <?php elseif ($step === 'reset'): ?>
            <h2 style="font-size:18px;color:#064e3b;margin-bottom:16px;">🔑 Reset Password</h2>

            <?php if ($resetSuccess): ?>
                <div class="success">✅ Password berhasil direset ke: <strong>NewPass123!</strong></div>
                <div class="link"><a href="index.php">← Kembali ke Login</a></div>
            <?php else: ?>
                <?php if ($resetError): ?>
                    <div class="error">❌
                        <?= htmlspecialchars($resetError) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username">
                    <label>Security Question</label>
                    <p style="font-size:12px;color:#6b7280;margin-bottom:8px;">Nama hewan peliharaan pertama / kota tempat lahir
                        / warna favorit</p>
                    <input type="text" name="answer" placeholder="Jawaban security question">
                    <button type="submit" class="btn">🔄 Reset Password</button>
                </form>
                <div class="link"><a href="index.php">← Kembali ke Login</a></div>

                <div class="hint">
                    <strong>🎯 Hint Reset Attack:</strong>
                    <ul>
                        <li>Jawaban: <strong>budi</strong> → "kucing", <strong>sari</strong> → "jakarta"</li>
                        <li>Security question mudah ditebak dari info sosial media</li>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="attack-panel">
        <h3>🏹 Attack Scenarios — A04 Insecure Design</h3>
        <ol>
            <li><strong>Brute Force Login</strong>: Tidak ada rate limiting atau lockout setelah gagal berulang kali
            </li>
            <li><strong>Security Question Bypass</strong>: Jawaban bisa ditebak dari OSINT (social media)</li>
            <li><strong>Business Logic Bypass</strong>: Di dashboard, manipulasi field <code>amount</code> di form
                pinjaman melebihi credit limit</li>
            <li><strong>Interest Rate Manipulation</strong>: Ubah hidden field <code>interest_rate</code> menjadi
                <code>0</code></li>
        </ol>
    </div>
</body>

</html>