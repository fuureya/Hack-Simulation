<?php
require_once 'db.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS sl_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    remember_token VARCHAR(50),
    session_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$users = [
    [1, 'alice', password_hash('pass', PASSWORD_DEFAULT), 'alice@securecorp.id', 'user'],
    [2, 'bob', password_hash('123456', PASSWORD_DEFAULT), 'bob@securecorp.id', 'user'],
    [3, 'admin', password_hash('admin', PASSWORD_DEFAULT), 'admin@securecorp.id', 'admin'],
];

foreach ($users as $u) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO sl_users (id, username, password, email, role) VALUES (?,?,?,?,?)");
    $stmt->execute($u);
}

echo "<h2>✅ Setup SecureLogin berhasil!</h2>";
echo "<p>Users: alice (pass), bob (123456), admin (admin)</p>";
echo "<a href='index.php'>→ Ke Halaman Login</a>";
