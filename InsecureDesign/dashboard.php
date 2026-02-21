<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['ql_user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['ql_user_id'];
$stmt = $pdo->prepare("SELECT * FROM ql_users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$loanSuccess = '';
$loanError = '';

// VULN A04: Business Logic — amount tidak divalidasi di server terhadap credit_limit
// Attacker bisa manipulasi hidden field interest_rate = 0 atau amount = 9999999999
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = floatval($_POST['amount']);
    // VULN: interest_rate diterima dari POST (hidden field) — bisa dimanipulasi!
    $interestRate = floatval($_POST['interest_rate'] ?? 2.50);

    // Seharusnya: validasi amount <= credit_limit - total_loan
    // VULN: Validasi tidak ada di server! Hanya client-side yang bisa di-bypass
    $stmt = $pdo->prepare("INSERT INTO ql_loans (user_id, amount, interest_rate, status) VALUES (?, ?, ?, 'approved')");
    $stmt->execute([$userId, $amount, $interestRate]);

    $pdo->prepare("UPDATE ql_users SET total_loan = total_loan + ? WHERE id = ?")->execute([$amount, $userId]);

    $stmt = $pdo->prepare("SELECT * FROM ql_users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $loanSuccess = "Pinjaman Rp " . number_format($amount, 0, ',', '.') . " dengan bunga " . $interestRate . "% berhasil diajukan!";
}

$stmt2 = $pdo->prepare("SELECT * FROM ql_loans WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt2->execute([$userId]);
$loans = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>QuickLoan — Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0fdf4;
        }

        .nav {
            background: #064e3b;
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
            color: #6ee7b7;
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

        .vuln-panel ul li {
            margin-bottom: 3px;
        }

        .vuln-panel code {
            background: #fef3c7;
            border: 1px solid #fbbf24;
            padding: 0 4px;
            border-radius: 3px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        }

        .card h2 {
            font-size: 16px;
            color: #064e3b;
            margin-bottom: 16px;
            border-bottom: 1px solid #f0fdf4;
            padding-bottom: 10px;
        }

        .grid2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat {
            background: #f0fdf4;
            border-radius: 8px;
            padding: 14px;
        }

        .stat label {
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat p {
            font-size: 20px;
            font-weight: 700;
            color: #064e3b;
            margin-top: 4px;
        }

        .stat p.danger {
            color: #dc2626;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        input[type=number],
        input[type=text] {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 12px;
        }

        input:focus {
            outline: none;
            border-color: #16a34a;
        }

        .btn {
            padding: 11px 20px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background: #15803d;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            text-align: left;
            padding: 9px 12px;
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
        }

        td {
            padding: 9px 12px;
            border-top: 1px solid #f1f5f9;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-approved {
            background: #dcfce7;
            color: #166534;
        }

        .attack-box {
            background: #fefce8;
            border: 1px solid #fde047;
            border-radius: 8px;
            padding: 12px;
            margin-top: 12px;
            font-size: 12px;
            color: #713f12;
        }

        .attack-box strong {
            display: block;
            margin-bottom: 6px;
        }

        .attack-box ul {
            padding-left: 16px;
        }
    </style>
</head>

<body>
    <nav class="nav">
        <h1>💸 QuickLoan App</h1>
        <div>
            <span style="margin-right:14px;">Hi, <strong>
                    <?= htmlspecialchars($user['username']) ?>
                </strong></span>
            <a href="index.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="vuln-panel">
            <h3>⚠️ OWASP A04 — Insecure Design: Vulnerabilities di Lab Ini</h3>
            <ul>
                <li><strong>Business Logic Bypass</strong>: Tidak ada validasi server untuk batas pinjaman — bisa pinjam
                    melebihi credit limit</li>
                <li><strong>Interest Rate Manipulation</strong>: Field <code>interest_rate</code> di-accept dari POST —
                    ubah dari 2.5% jadi 0%</li>
                <li><strong>Brute Force tanpa lockout</strong>: Login tidak terkunci meski ribuan kali gagal</li>
                <li><strong>Security Question lemah</strong>: Bisa di-bypass dengan social engineering / OSINT</li>
            </ul>
        </div>

        <div class="grid2">
            <div class="stat">
                <label>💰 Credit Limit</label>
                <p>Rp
                    <?= number_format($user['credit_limit'], 0, ',', '.') ?>
                </p>
            </div>
            <div class="stat">
                <label>📊 Total Pinjaman</label>
                <p class="<?= ($user['total_loan'] > $user['credit_limit']) ? 'danger' : '' ?>">
                    Rp
                    <?= number_format($user['total_loan'], 0, ',', '.') ?>
                    <?php if ($user['total_loan'] > $user['credit_limit']): ?>
                        ⚠️ OVER LIMIT!
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="card">
            <h2>📋 Ajukan Pinjaman</h2>
            <?php if ($loanSuccess): ?>
                <div class="success">✅
                    <?= htmlspecialchars($loanSuccess) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label>Jumlah Pinjaman (Rp)</label>
                <!-- VULN A04: max hanya client-side — bisa di-bypass dengan DevTools -->
                <input type="number" name="amount" placeholder="Misal: 1000000" min="100000"
                    max="<?= $user['credit_limit'] ?>" required>

                <label>Tenor (bulan)</label>
                <input type="number" name="tenor" value="12" min="1" max="60">

                <!-- VULN A04: Interest rate dikirim dari client — bisa dimanipulasi! -->
                <input type="hidden" name="interest_rate" value="2.50">
                <!-- Ubah value di atas jadi 0 via DevTools → pinjam tanpa bunga! -->

                <button type="submit" class="btn">🚀 Ajukan Sekarang</button>
            </form>

            <div class="attack-box">
                <strong>🎯 Cara Exploit Business Logic:</strong>
                <ul>
                    <li>Buka DevTools → klik kanan angka <code>max="<?= $user['credit_limit'] ?>"</code> → Edit → hapus
                        max → isi amount 9999999999</li>
                    <li>Cari <code>input[name="interest_rate"]</code> → ubah value dari <code>2.50</code> jadi
                        <code>0</code> → pinjam tanpa bunga!</li>
                    <li>Atau gunakan Burp Suite → intercept POST request → ubah <code>amount</code> &
                        <code>interest_rate</code></li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h2>📄 Riwayat Pinjaman</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jumlah</th>
                        <th>Bunga (%)</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td>#
                                <?= $loan['id'] ?>
                            </td>
                            <td>Rp
                                <?= number_format($loan['amount'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <?= $loan['interest_rate'] ?>%
                                <?php if ($loan['interest_rate'] == 0): ?><span style="color:#dc2626;font-weight:600;">←
                                        Dimanipulasi!</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-approved">
                                    <?= strtoupper($loan['status']) ?>
                                </span></td>
                            <td>
                                <?= $loan['created_at'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>