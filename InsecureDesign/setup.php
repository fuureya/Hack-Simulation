<?php
require_once 'db.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS ql_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    credit_limit DECIMAL(15,2) DEFAULT 5000000.00,
    total_loan DECIMAL(15,2) DEFAULT 0.00,
    security_question VARCHAR(255),
    security_answer VARCHAR(255),
    failed_attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS ql_loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) DEFAULT 2.50,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$users = [
    ['budi', password_hash('Budi1234!', PASSWORD_DEFAULT), 'budi@quickloan.id', 5000000, 'Nama hewan peliharaan pertamamu?', 'kucing'],
    ['sari', password_hash('Sari5678!', PASSWORD_DEFAULT), 'sari@quickloan.id', 10000000, 'Kota tempat lahirmu?', 'jakarta'],
    ['admin', password_hash('admin', PASSWORD_DEFAULT), 'admin@quickloan.id', 99999999, 'Warna favoritmu?', 'merah'],
];

foreach ($users as $u) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO ql_users (username, password, email, credit_limit, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute($u);
}

// Seed loans
$pdo->exec("INSERT IGNORE INTO ql_loans (user_id, amount, status) VALUES (1, 2000000, 'approved'), (2, 5000000, 'approved')");

echo "<h2>✅ Setup QuickLoan berhasil!</h2>";
echo "<p>Users: budi (Budi1234!), sari (Sari5678!), admin (admin)</p>";
echo "<a href='index.php'>→ Ke Halaman Login</a>";
