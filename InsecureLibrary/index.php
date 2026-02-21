<?php
require_once 'legacy_lib.php';

// Memproses input yang mengandung data serialisasi (VULN A06 & A08)
if (isset($_POST['data'])) {
    // VULN: Deserialisasi input user tanpa validasi!
    // Attacker bisa mengirim objek LegacyMailer yang dimanipulasi
    unserialize($_POST['data']);
}

$articles = [
    ['title' => 'Teknologi Baru 2026', 'content' => 'AI semakin canggih dan membantu manusia.'],
    ['title' => 'Keamanan Siber di Indonesia', 'content' => 'Penting untuk memahami OWASP Top 10.'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Legacy News CMS — A06 Vulnerable Components</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f4f5;
            color: #27272a;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #18181b;
        }

        .article {
            border-bottom: 1px solid #e4e4e7;
            padding: 15px 0;
        }

        .article h2 {
            font-size: 18px;
            color: #2563eb;
        }

        .vuln-badge {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 6px;
            font-size: 12px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }

        .attack-box {
            margin-top: 30px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-size: 13px;
        }

        textarea {
            width: 100%;
            height: 80px;
            margin-bottom: 10px;
            font-family: monospace;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="vuln-badge">
            <strong>⚠️ A06 Vulnerable & Outdated Components Lab</strong><br>
            Aplikasi ini menggunakan library <code>legacy_lib.php</code> versi lama yang mengandung kerentanan
            deserialisasi.
        </div>

        <h1>📰 Legacy News CMS</h1>
        <p>Sistem manajemen konten berita internal perusahaan.</p>

        <?php foreach ($articles as $art): ?>
            <div class="article">
                <h2>
                    <?= $art['title'] ?>
                </h2>
                <p>
                    <?= $art['content'] ?>
                </p>
            </div>
        <?php endforeach; ?>

        <div class="attack-box">
            <h3>🏹 Eksploitasi PHP Object Injection</h3>
            <p>Aplikasi menerima data serialisasi PHP via parameter <code>data</code>.</p>
            <form method="POST">
                <label>Masukkan Data Serialized:</label>
                <textarea name="data"
                    placeholder='O:12:"LegacyMailer":3:{s:9:"recipient";s:5:"admin";s:8:"template";s:19:"<?php phpinfo(); ?>";s:8:"log_file";s:9:"shell.php";}'></textarea>
                <button type="submit">Kirim Payload</button>
            </form>
            <p style="margin-top:10px; font-size:11px; color:#64748b;">
                Payload di atas akan membuat file <code>shell.php</code> di server jika berhasil dieksekusi.
            </p>
        </div>
    </div>
</body>

</html>