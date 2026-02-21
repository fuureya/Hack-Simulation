<?php
// VULN A10: SSRF — URL yang dimasukkan user langsung di-fetch tanpa validasi
$result = '';
$requestedUrl = '';
$error = '';
$responseHeaders = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');
    $requestedUrl = $url;

    if (empty($url)) {
        $error = "URL tidak boleh kosong.";
    } else {
        // VULN A10: Tidak ada validasi URL sama sekali!
        // Seharusnya: whitelist domain, blokir internal IPs, blokir localhost, dll
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // VULN: ikuti redirect (bypass filter)
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // VULN: SSL tidak diverifikasi
        // VULN: User-agent bisa dimanipulasi untuk bypass WAF
        curl_setopt($ch, CURLOPT_USERAGENT, 'WebFetcher/1.0 (Internal Service)');

        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $error = "Curl error: " . $curlError;
        } else {
            $responseHeaders = substr($response, 0, $headerSize);
            $result = substr($response, $headerSize);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebFetcher Pro — A10 SSRF Lab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #7c3aed 0%, #3b0764 100%);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 30px 20px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .main-card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            width: 520px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .logo {
            margin-bottom: 24px;
        }

        .logo span {
            font-size: 40px;
        }

        .logo h1 {
            font-size: 22px;
            color: #3b0764;
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
            color: #374151;
            margin-bottom: 6px;
        }

        input[type=text] {
            width: 100%;
            padding: 11px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 14px;
        }

        input:focus {
            outline: none;
            border-color: #7c3aed;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover {
            background: #6d28d9;
        }

        .result-box {
            margin-top: 16px;
        }

        .result-box h3 {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .headers {
            background: #0f172a;
            color: #94a3b8;
            font-size: 11px;
            font-family: monospace;
            padding: 10px;
            border-radius: 6px;
            max-height: 100px;
            overflow-y: auto;
            margin-bottom: 8px;
            white-space: pre-wrap;
        }

        .response {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            font-size: 12px;
            max-height: 280px;
            overflow-y: auto;
            white-space: pre-wrap;
            font-family: monospace;
            color: #1e293b;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 12px;
            font-size: 13px;
        }

        .attack-panel {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 20px;
            width: 360px;
            color: white;
            font-size: 13px;
        }

        .attack-panel h3 {
            font-size: 14px;
            margin-bottom: 14px;
            color: #c4b5fd;
        }

        .scenario {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .scenario h4 {
            font-size: 12px;
            color: #a78bfa;
            margin-bottom: 6px;
        }

        .scenario ul {
            padding-left: 16px;
            font-size: 12px;
            color: #e2e8f0;
        }

        .scenario ul li {
            margin-bottom: 4px;
        }

        .scenario code {
            background: rgba(0, 0, 0, 0.4);
            padding: 1px 5px;
            border-radius: 3px;
        }

        .quick-fill {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }

        .qf-btn {
            background: #ede9fe;
            color: #4c1d95;
            border: none;
            border-radius: 4px;
            padding: 4px 10px;
            font-size: 11px;
            cursor: pointer;
            font-weight: 600;
        }

        .qf-btn:hover {
            background: #ddd6fe;
        }
    </style>
    <script>
        function fillUrl(url) {
            document.getElementById('urlInput').value = url;
        }
    </script>
</head>

<body>
    <div class="main-card">
        <div class="logo">
            <span>🌐</span>
            <h1>WebFetcher Pro</h1>
            <p>URL Preview & Screenshot Service — A10 Server-Side Request Forgery (SSRF) Lab</p>
        </div>

        <div class="vuln-badge">
            <strong>⚠️ VULNERABILITY — A10 SSRF (Server-Side Request Forgery)</strong>
            URL diakses langsung oleh server tanpa validasi — bisa akses internal services!
        </div>

        <form method="POST">
            <label>URL yang ingin di-fetch</label>
            <div class="quick-fill">
                <button type="button" class="qf-btn" onclick="fillUrl('http://localhost/')">localhost</button>
                <button type="button" class="qf-btn" onclick="fillUrl('http://127.0.0.1/')">127.0.0.1</button>
                <button type="button" class="qf-btn" onclick="fillUrl('http://labsec-db:3306/')">Internal DB</button>
                <button type="button" class="qf-btn" onclick="fillUrl('http://169.254.169.254/latest/meta-data/')">AWS
                    Metadata</button>
                <button type="button" class="qf-btn" onclick="fillUrl('file:///etc/passwd')">File Read</button>
                <button type="button" class="qf-btn" onclick="fillUrl('http://labsec-db/')">labsec-db</button>
            </div>
            <input type="text" id="urlInput" name="url" placeholder="https://example.com atau http://localhost/admin"
                value="<?= htmlspecialchars($requestedUrl) ?>">
            <button type="submit" class="btn">🚀 Fetch URL</button>
        </form>

        <?php if ($error): ?>
            <div class="error">❌
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($result || $responseHeaders): ?>
            <div class="result-box">
                <h3>📡 Response Headers dari:
                    <?= htmlspecialchars($requestedUrl) ?>
                </h3>
                <div class="headers">
                    <?= htmlspecialchars($responseHeaders) ?>
                </div>
                <h3>📄 Response Body</h3>
                <div class="response">
                    <?= htmlspecialchars(mb_substr($result, 0, 3000)) ?>
                    <?= (strlen($result) > 3000) ? "\n\n[...truncated " . strlen($result) . " bytes total...]" : '' ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="attack-panel">
        <h3>🏹 SSRF Attack Scenarios — A10</h3>

        <div class="scenario">
            <h4>1. 🏠 Akses Internal Services</h4>
            <ul>
                <li><code>http://localhost/</code> — Apache loopback</li>
                <li><code>http://127.0.0.1:80/</code> — Port scan</li>
                <li><code>http://labsec-db:3306/</code> — Internal DB</li>
                <li><code>http://labsec-dashboard/</code> — Dashboard internal</li>
            </ul>
        </div>

        <div class="scenario">
            <h4>2. ☁️ Cloud Metadata Exfiltration</h4>
            <ul>
                <li><code>http://169.254.169.254/latest/meta-data/</code></li>
                <li><code>http://169.254.169.254/latest/meta-data/iam/security-credentials/</code></li>
                <li><code>http://metadata.google.internal/computeMetadata/v1/</code></li>
            </ul>
        </div>

        <div class="scenario">
            <h4>3. 📁 Local File Read (file://)</h4>
            <ul>
                <li><code>file:///etc/passwd</code></li>
                <li><code>file:///etc/hosts</code></li>
                <li><code>file:///var/www/html/index.php</code> — Source code!</li>
            </ul>
        </div>

        <div class="scenario">
            <h4>4. 🔄 Bypass Filter via Redirect</h4>
            <ul>
                <li>Buat server redirect dari URL publik ke <code>http://internal/</code></li>
                <li><code>http://attacker.com/redirect?to=http://localhost/admin</code></li>
            </ul>
        </div>

        <div class="scenario">
            <h4>5. 🔢 IP Encoding Bypass</h4>
            <ul>
                <li><code>http://0x7f000001/</code> — Hex: 127.0.0.1</li>
                <li><code>http://2130706433/</code> — Decimal: 127.0.0.1</li>
                <li><code>http://0177.0.0.1/</code> — Octal: 127.0.0.1</li>
            </ul>
        </div>
    </div>
</body>

</html>