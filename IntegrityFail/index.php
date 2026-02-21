<?php
require_once 'session.php';

$user = get_session();

if (!$user) {
    $user = new UserProfile('guest', 'visitor');
    set_session($user);
}

$isAdmin = (isset($user->is_admin) && $user->is_admin === true) || (isset($user->role) && $user->role === 'admin');

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>ObjectRelay Service — A08 Integrity Failures</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f0f9ff;
            color: #0c4a6e;
            padding: 40px;
            text-align: center;
        }

        .card {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .vuln-badge {
            background: #fff1f2;
            color: #be123c;
            padding: 12px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 24px;
            border: 1px solid #fecdd3;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .admin {
            background: #dcfce7;
            color: #166534;
        }

        .user {
            background: #f1f5f9;
            color: #475569;
        }

        .cookie-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            font-family: monospace;
            font-size: 11px;
            word-break: break-all;
            margin-top: 20px;
            border: 1px solid #e2e8f0;
        }

        .hint {
            margin-top: 30px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="vuln-badge">
            <strong>🛡️ A08 Software and Data Integrity Failures Lab</strong><br>
            Session data disimpan di cookie sebagai serialized object tanpa perlindungan integrity (HMAC).
        </div>

        <h1>Relay Service</h1>
        <p>Selamat datang, <strong>
                <?= htmlspecialchars($user->username) ?>
            </strong></p>

        <div class="status-badge <?= $isAdmin ? 'admin' : 'user' ?>">
            Role:
            <?= $isAdmin ? 'ADMINISTRATOR' : 'NORMAL USER' ?>
        </div>

        <?php if ($isAdmin): ?>
            <div style="background:#ecfdf5; padding:20px; border-radius:12px; border:1px solid #10b981; color:#064e3b;">
                ✨ <strong>BERHASIL!</strong> Ini adalah area rahasia admin.<br>
                Flag: <code>FLAG{INSECURE_DESERIALIZATION_INTEGRITY_FAIL}</code>
            </div>
        <?php else: ?>
            <p>Anda tidak memiliki akses ke fitur administrator.</p>
        <?php endif; ?>

        <div class="cookie-box">
            <strong>Your Session Cookie:</strong><br>
            <?= $_COOKIE['session'] ?? 'None' ?>
        </div>

        <div class="hint">
            💡 <strong>Hint:</strong> Decode cookie di atas (Base64), ubah nilai <code>role</code> atau
            <code>is_admin</code>, encode kembali, lalu pasang di browser.
        </div>
    </div>
</body>

</html>