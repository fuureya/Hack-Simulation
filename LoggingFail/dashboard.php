<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['at_user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['at_user_id'];
$username = $_SESSION['at_username'];
$role = $_SESSION['at_role'];

// VULN A09: Tampilkan log file langsung dari dashboard (readable oleh user biasa)
$logContent = file_get_contents(__DIR__ . '/audit.log');

// Delete log — VULN: user biasa bisa hapus log audit!
if (isset($_GET['clear_log']) && $role === 'staff') {
    // VULN A09: Staff bisa hapus semua log audit!
    file_put_contents(__DIR__ . '/audit.log', "# Log dibersihkan oleh $username pada " . date('Y-m-d H:i:s') . "\n");
    header("Location: dashboard.php?cleared=1");
    exit;
}

// Ambil activities dari DB
$stmt = $pdo->prepare("SELECT * FROM at_activities ORDER BY created_at DESC LIMIT 20");
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Privilege escalation — tidak di-log!
if (isset($_GET['escalate'])) {
    // VULN: user biasa ganti role ke admin, tidak ada log!
    $pdo->prepare("UPDATE at_users SET role = 'admin' WHERE id = ?")->execute([$userId]);
    $_SESSION['at_role'] = 'admin';
    $role = 'admin';
    // Seharusnya: log privilege escalation attempt!
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>AuditTrail System — Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0fdfa;
        }

        .nav {
            background: #0f766e;
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
            color: #99f6e4;
            text-decoration: none;
            font-size: 14px;
        }

        .container {
            max-width: 1000px;
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
            color: #042f2e;
            margin-bottom: 14px;
            border-bottom: 1px solid #ccfbf1;
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-sm {
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .log-display {
            background: #0f172a;
            border-radius: 8px;
            padding: 14px;
            font-family: monospace;
            font-size: 12px;
            color: #4ade80;
            max-height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            text-align: left;
            padding: 9px 12px;
            background: #f0fdfa;
            color: #0f766e;
            font-weight: 600;
        }

        td {
            padding: 9px 12px;
            border-top: 1px solid #f0fdfa;
            color: #374151;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-failed {
            background: #fee2e2;
            color: #991b1b;
        }

        .role-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: #ccfbf1;
            color: #0f766e;
        }

        .alert-box {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 13px;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <nav class="nav">
        <h1>📋 AuditTrail System</h1>
        <div>
            <span style="margin-right:14px;">
                <strong>
                    <?= htmlspecialchars($username) ?>
                </strong>
                <span class="role-badge">
                    <?= strtoupper($role) ?>
                </span>
            </span>
            <a href="?logout=1">Logout</a>
        </div>
    </nav>
    <div class="container">
        <div class="vuln-panel">
            <h3>⚠️ OWASP A09 — Logging & Monitoring Failures: Vulnerabilities Lab Ini</h3>
            <ul>
                <li><strong>Login gagal tidak di-log</strong>: Brute force bisa dilakukan tanpa jejak apapun di log</li>
                <li><strong>Log file publik</strong>: <a href="/audit.log" target="_blank"
                        style="color:#78350f;">/audit.log</a> bisa dibaca siapapun tanpa auth</li>
                <li><strong>Log injection</strong>: Karakter newline di username bisa inject baris log palsu</li>
                <li><strong>Staff bisa hapus log</strong>: Tombol "Clear Log" di bawah bisa diakses oleh role staff
                    (bukan hanya admin)</li>
                <li><strong>Privilege escalation tidak di-log</strong>: Klik "Escalate ke Admin" — tidak ada log sama
                    sekali!</li>
            </ul>
        </div>

        <?php if (isset($_GET['cleared'])): ?>
            <div class="alert-box">⚠️ Log audit berhasil dihapus oleh staff! Ini vulnerability!</div>
        <?php endif; ?>

        <div class="card">
            <h2>
                📄 Isi Audit Log (audit.log)
                <div>
                    <a href="/audit.log" target="_blank" class="btn-sm btn-warning">🔗 Akses Publik</a>
                    &nbsp;
                    <!-- VULN: Staff bisa hapus semua log! -->
                    <a href="?clear_log=1" class="btn-sm btn-danger" onclick="return confirm('Hapus semua log?')">🗑️
                        Clear Log (VULN!)</a>
                </div>
            </h2>
            <div class="log-display">
                <?= htmlspecialchars($logContent) ?>
            </div>
            <p style="font-size:12px;color:#6b7280;margin-top:8px;">⚠️ File ini bisa diakses langsung di
                <code>http://localhost:8007/audit.log</code></p>
        </div>

        <div class="card">
            <h2>🔺 Privilege Escalation Demo (Tidak Di-log!)</h2>
            <p style="font-size:13px;color:#6b7280;margin-bottom:12px;">Role saat ini: <strong>
                    <?= strtoupper($role) ?>
                </strong></p>
            <?php if ($role !== 'admin'): ?>
                <a href="?escalate=1" class="btn-sm btn-danger">⬆️ Escalate ke Admin (VULN — tidak di-log!)</a>
            <?php else: ?>
                <span style="color:#166534;font-size:13px;font-weight:600;">✅ Kamu sudah admin! Perhatikan: tidak ada log
                    privilege escalation di atas.</span>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>📊 Activity Log dari Database</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Detail</th>
                        <th>IP</th>
                        <th>Status</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $act): ?>
                        <tr>
                            <td>#
                                <?= $act['id'] ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($act['username']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($act['action']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($act['detail']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($act['ip']) ?>
                            </td>
                            <td><span class="badge badge-<?= $act['status'] ?>">
                                    <?= strtoupper($act['status']) ?>
                                </span></td>
                            <td>
                                <?= $act['created_at'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="font-size:12px;color:#6b7280;margin-top:10px;">⚠️ Perhatikan: tidak ada log untuk brute force
                attempts, privilege escalation, atau akses file sensitif.</p>
        </div>
    </div>
</body>

</html>