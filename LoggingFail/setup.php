<?php
require_once 'db.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS at_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS at_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    action VARCHAR(100),
    detail TEXT,
    ip VARCHAR(50),
    status VARCHAR(20) DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$users = [
    ['alice', password_hash('alice123', PASSWORD_DEFAULT), 'alice@audit.id', 'staff'],
    ['bob', password_hash('bob456', PASSWORD_DEFAULT), 'bob@audit.id', 'staff'],
    ['admin', password_hash('admin', PASSWORD_DEFAULT), 'admin@audit.id', 'admin'],
];
foreach ($users as $u) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO at_users (username, password, email, role) VALUES (?,?,?,?)");
    $stmt->execute($u);
}

$pdo->exec("INSERT IGNORE INTO at_activities (user_id, username, action, detail, ip, status) VALUES
    (3, 'admin', 'login', 'Admin login berhasil', '192.168.1.10', 'success'),
    (1, 'alice', 'view_salary', 'Alice melihat data gaji karyawan', '192.168.1.20', 'success'),
    (3, 'admin', 'delete_user', 'Admin menghapus user id=5', '192.168.1.10', 'success'),
    (2, 'bob', 'change_role', 'Bob mencoba escalate privilege ke admin', '10.0.0.5', 'failed')
");

echo "<h2>✅ Setup AuditTrail berhasil!</h2>";
echo "<a href='index.php'>→ Ke Halaman Login</a>";
