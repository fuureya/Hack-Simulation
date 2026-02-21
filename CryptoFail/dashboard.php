<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM sv_users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Ambil transaksi
$stmt2 = $pdo->prepare("SELECT * FROM sv_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt2->execute([$userId]);
$transactions = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// VULN A02: Data sensitif dikirim via GET param saat "cetak struk"
$showSensitive = isset($_GET['show']) && $_GET['show'] === 'true';
// VULN A02: Nomor kartu di URL — ?card=4111111111111111&pin=1234
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>SafeVault Bank — Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
        }

        .navbar {
            background: #1a3a5c;
            color: white;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            font-size: 18px;
        }

        .navbar a {
            color: #93c5fd;
            text-decoration: none;
            font-size: 14px;
        }

        .navbar a:hover {
            color: white;
        }

        .container {
            max-width: 1000px;
            margin: 32px auto;
            padding: 0 20px;
        }

        .vuln-panel {
            background: #fffbeb;
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .vuln-panel h3 {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .vuln-panel ul {
            font-size: 13px;
            color: #78350f;
            padding-left: 18px;
        }

        .vuln-panel li {
            margin-bottom: 4px;
        }

        .vuln-panel code {
            background: #fef3c7;
            padding: 1px 5px;
            border-radius: 3px;
            font-family: monospace;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .card h2 {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px;
        }

        .balance-display {
            font-size: 36px;
            font-weight: 700;
            color: #1a3a5c;
            margin: 8px 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .info-item label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-item p {
            font-size: 14px;
            color: #1e293b;
            margin-top: 2px;
            font-family: monospace;
        }

        .sensitive {
            color: #dc2626 !important;
            background: #fef2f2;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #1a3a5c;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            text-align: left;
            padding: 10px 12px;
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
        }

        td {
            padding: 10px 12px;
            border-top: 1px solid #f1f5f9;
            color: #374151;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-credit {
            background: #dcfce7;
            color: #166534;
        }

        .badge-debit {
            background: #fee2e2;
            color: #991b1b;
        }

        .attack-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 14px;
            margin-top: 12px;
        }

        .attack-box h4 {
            color: #166534;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .attack-box ul {
            font-size: 12px;
            color: #15803d;
            padding-left: 16px;
        }

        .attack-box code {
            background: #dcfce7;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1>🏦 SafeVault Bank</h1>
        <div>
            <span style="margin-right:16px;">Selamat datang, <strong>
                    <?= htmlspecialchars($user['username']) ?>
                </strong></span>
            <a href="index.php?logout=1" onclick="<?php session_destroy(); ?>">Logout</a>
        </div>
    </nav>

    <div class="container">
        <!-- Vulnerability Info Panel -->
        <div class="vuln-panel">
            <h3>⚠️ OWASP A02 — Cryptographic Failures: Vulnerability yang Ada di Lab Ini</h3>
            <ul>
                <li><strong>MD5 tanpa salt</strong>: Password di-hash dengan <code>md5()</code> tanpa salt — mudah
                    di-crack dengan rainbow table</li>
                <li><strong>Cookie sensitif plaintext</strong>: Nomor kartu kredit & PIN tersimpan di cookie tanpa
                    enkripsi</li>
                <li><strong>Data sensitif di URL</strong>: Klik "Lihat Detail Sensitif" untuk lihat data sensitif bocor
                    di GET parameter</li>
                <li><strong>Token predictable</strong>: Remember-me token = <code>md5(username + 'safevault')</code>
                </li>
            </ul>
        </div>

        <!-- Balance Card -->
        <div class="card">
            <h2>💰 Saldo Rekening</h2>
            <div class="balance-display">Rp
                <?= number_format($user['balance'], 0, ',', '.') ?>
            </div>
            <p style="color:#6b7280;font-size:13px;margin-top:4px;">Rekening atas nama:
                <?= htmlspecialchars($user['username']) ?>
            </p>
        </div>

        <!-- Sensitive Data Card — VULNERABILITY DEMO -->
        <div class="card">
            <h2>🔐 Informasi Akun (Data Sensitif)</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Username</label>
                    <p>
                        <?= htmlspecialchars($user['username']) ?>
                    </p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p>
                        <?= htmlspecialchars($user['email']) ?>
                    </p>
                </div>
                <div class="info-item">
                    <label>Password Hash (MD5)</label>
                    <!-- VULN: Hash password ditampilkan di halaman! -->
                    <p class="sensitive">
                        <?= $user['password'] ?>
                    </p>
                </div>
                <div class="info-item">
                    <label>PIN ATM</label>
                    <!-- VULN: PIN disimpan & ditampilkan plaintext -->
                    <p class="sensitive">
                        <?= $user['pin'] ?>
                    </p>
                </div>
                <div class="info-item">
                    <label>Nomor Kartu Kredit</label>
                    <!-- VULN: Nomor kartu tersimpan & ditampilkan plaintext -->
                    <p class="sensitive">
                        <?= $user['credit_card'] ?>
                    </p>
                </div>
                <div class="info-item">
                    <label>Remember Token</label>
                    <!-- VULN: Token predictable — md5(username) -->
                    <p class="sensitive">
                        <?= md5($user['username'] . 'safevault') ?>
                    </p>
                </div>
            </div>

            <!-- VULN: Data sensitif dikirim via GET parameter di URL -->
            <div style="margin-top:16px;">
                <a href="dashboard.php?show=true&card=<?= $user['credit_card'] ?>&pin=<?= $user['pin'] ?>&balance=<?= $user['balance'] ?>"
                    class="btn btn-danger">
                    🔓 Lihat Detail Sensitif (GET Params — VULN!)
                </a>
            </div>

            <?php if ($showSensitive): ?>
                <div style="margin-top:12px;padding:12px;background:#fee2e2;border-radius:8px;font-size:13px;">
                    <strong>📋 Data dari URL GET params:</strong><br>
                    Card: <code><?= htmlspecialchars($_GET['card'] ?? '') ?></code><br>
                    PIN: <code><?= htmlspecialchars($_GET['pin'] ?? '') ?></code><br>
                    Balance: <code><?= htmlspecialchars($_GET['balance'] ?? '') ?></code>
                    <br><small style="color:#991b1b;">⚠️ Data sensitif ini tersimpan di browser history & server
                        logs!</small>
                </div>
            <?php endif; ?>

            <div class="attack-box">
                <h4>🎯 Cara Eksploitasi:</h4>
                <ul>
                    <li>Buka DevTools → Application → Cookies → lihat <code>user_info</code> cookie (berisi CC number &
                        PIN)</li>
                    <li>Copy MD5 hash dari kolom "Password Hash" → paste ke <a href="https://crackstation.net"
                            target="_blank" style="color:#166534;">crackstation.net</a></li>
                    <li>Lihat URL setelah klik "Lihat Detail Sensitif" — data kartu ada di URL!</li>
                    <li>Token: <code>md5("alice" + "safevault")</code> = <code>md5("alicesafevault")</code> = mudah
                        diprediksi</li>
                </ul>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card">
            <h2>📄 Riwayat Transaksi</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td>#
                                <?= $tx['id'] ?>
                            </td>
                            <td><span class="badge badge-<?= $tx['type'] ?>">
                                    <?= strtoupper($tx['type']) ?>
                                </span></td>
                            <td>Rp
                                <?= number_format($tx['amount'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($tx['note']) ?>
                            </td>
                            <td>
                                <?= $tx['created_at'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>