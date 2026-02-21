<?php
require_once 'db.php';

// Setup database
$pdo->exec("CREATE TABLE IF NOT EXISTS sv_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'VULN: MD5 hash tanpa salt',
    email VARCHAR(100),
    pin VARCHAR(10) COMMENT 'VULN: PIN disimpan plaintext',
    credit_card VARCHAR(20) COMMENT 'VULN: nomor kartu plaintext',
    balance DECIMAL(15,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS sv_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type VARCHAR(20),
    amount DECIMAL(15,2),
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Seed users — password di-hash dengan MD5 (VULNERABLE)
$users = [
    ['alice', md5('password123'), 'alice@safevault.com', '1234', '4111111111111111', 5000000.00],
    ['bob',   md5('qwerty'),      'bob@safevault.com',   '5678', '5500005555555559', 2500000.00],
    ['admin', md5('admin'),       'admin@safevault.com', '0000', '3714496353984312', 99999999.00],
];

foreach ($users as $u) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO sv_users (username, password, email, pin, credit_card, balance) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute($u);
}

// Seed transactions
$pdo->exec("INSERT IGNORE INTO sv_transactions (user_id, type, amount, note) VALUES
    (1, 'credit', 5000000.00, 'Initial deposit'),
    (1, 'debit', 150000.00, 'Pembayaran listrik'),
    (2, 'credit', 2500000.00, 'Initial deposit'),
    (3, 'credit', 99999999.00, 'Admin balance')
");

echo "<h2>✅ Setup SafeVault berhasil!</h2>";
echo "<p>Users yang dibuat: alice (password123), bob (qwerty), admin (admin)</p>";
echo "<p>⚠️ Password disimpan sebagai MD5 hash TANPA salt — ini VULNERABLE!</p>";
echo "<a href='index.php'>→ Ke Halaman Login</a>";
