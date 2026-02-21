<?php
session_start();
if (!isset($_SESSION['sm_user'])) {
    header("Location: index.php");
    exit;
}
$user = $_SESSION['sm_user'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>DevOps Portal — Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f172a;
            color: white;
        }

        .nav {
            background: #1e293b;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #334155;
        }

        .nav h1 {
            font-size: 18px;
            color: #f8fafc;
        }

        .nav a {
            color: #7dd3fc;
            text-decoration: none;
            font-size: 14px;
        }

        .container {
            max-width: 1000px;
            margin: 28px auto;
            padding: 0 20px;
        }

        .vuln-panel {
            background: #422006;
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .vuln-panel h3 {
            color: #fbbf24;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .vuln-panel ul {
            font-size: 12px;
            color: #fdba74;
            padding-left: 16px;
        }

        .vuln-panel li {
            margin-bottom: 4px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .card {
            background: #1e293b;
            border-radius: 12px;
            padding: 18px;
            border: 1px solid #334155;
        }

        .card h3 {
            font-size: 13px;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .card p {
            font-size: 22px;
            font-weight: 700;
            color: #f1f5f9;
        }

        .card.danger {
            border-color: #dc2626;
        }

        .card.danger p {
            color: #f87171;
        }

        .section {
            background: #1e293b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            border: 1px solid #334155;
        }

        .section h2 {
            font-size: 15px;
            color: #e2e8f0;
            margin-bottom: 14px;
            border-bottom: 1px solid #334155;
            padding-bottom: 10px;
        }

        .file-list {
            list-style: none;
        }

        .file-list li {
            padding: 8px 0;
            border-bottom: 1px solid #334155;
            font-size: 13px;
            color: #94a3b8;
        }

        .file-list li:last-child {
            border: none;
        }

        .file-list a {
            color: #7dd3fc;
            text-decoration: none;
        }

        .file-list a:hover {
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-red {
            background: #7f1d1d;
            color: #fca5a5;
        }

        .badge-yellow {
            background: #713f12;
            color: #fde68a;
        }

        .env-display {
            background: #0f172a;
            border-radius: 8px;
            padding: 14px;
            font-family: monospace;
            font-size: 12px;
            color: #4ade80;
            margin-top: 10px;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <nav class="nav">
        <h1>⚙️ DevOps Portal — Internal Dashboard</h1>
        <div>
            <span style="margin-right:14px;color:#94a3b8;">Logged in: <strong style="color:#f8fafc;">
                    <?= htmlspecialchars($user) ?>
                </strong></span>
            <a href="index.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="vuln-panel">
            <h3>⚠️ OWASP A05 — Security Misconfiguration: Leakage dari Lab Ini</h3>
            <ul>
                <li>🔓 <strong>Default credentials</strong>: Login dengan admin/admin berhasil masuk!</li>
                <li>📄 <strong>phpinfo() terbuka</strong>: <a href="info.php" target="_blank"
                        style="color:#fbbf24;">/info.php</a> — versi PHP, path server, env vars bocor</li>
                <li>🗂️ <strong>Directory listing</strong>: Semua file di folder ini terlihat</li>
                <li>📁 <strong>Sensitive files publik</strong>: .env, config.bak, database_backup.sql.bak bisa diakses
                    siapa saja</li>
                <li>📢 <strong>Verbose error</strong>: PHP error menampilkan full stack trace & path server</li>
            </ul>
        </div>

        <div class="grid">
            <div class="card">
                <h3>🖥️ Servers Online</h3>
                <p>12</p>
            </div>
            <div class="card">
                <h3>🐳 Containers Running</h3>
                <p>47</p>
            </div>
            <div class="card danger">
                <h3>🚨 Security Alerts</h3>
                <p>8 ⚠️</p>
            </div>
        </div>

        <div class="section">
            <h2>📂 Sensitive Files yang Bisa Diakses Publik</h2>
            <ul class="file-list">
                <li>
                    <a href="/.env" target="_blank">📄 /.env</a>
                    <span class="badge badge-red">CRITICAL — AWS Keys, DB Password, Payment Gateway</span>
                </li>
                <li>
                    <a href="/config.bak" target="_blank">📄 /config.bak</a>
                    <span class="badge badge-red">CRITICAL — Admin credentials, API keys, Webhooks</span>
                </li>
                <li>
                    <a href="/database_backup.sql.bak" target="_blank">📄 /database_backup.sql.bak</a>
                    <span class="badge badge-red">CRITICAL — DB backup dengan SSH & MySQL credentials</span>
                </li>
                <li>
                    <a href="/info.php" target="_blank">📄 /info.php</a>
                    <span class="badge badge-yellow">HIGH — phpinfo() bocorkan konfigurasi server</span>
                </li>
            </ul>
        </div>

        <div class="section">
            <h2>🔧 Environment Variables (Bocor via phpinfo)</h2>
            <div class="env-display">
                DB_HOST=labsec-db<br>
                DB_PASSWORD=labsec_root_2026<br>
                SECRET_KEY=super_secret_key_12345<br>
                JWT_SECRET=jwt_secret_do_not_share<br>
                AWS_ACCESS_KEY_ID=AKIAIOSFODNN7EXAMPLE<br>
                AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY<br>
                PAYMENT_GATEWAY_KEY=sk_live_51AbCdEfGhIjKlMnOpQ
            </div>
        </div>
    </div>
</body>

</html>