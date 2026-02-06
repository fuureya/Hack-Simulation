<?php
require_once 'db.php';

try {
    // Buat tabel users
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT,
        balance INTEGER DEFAULT 1000,
        role TEXT
    )");

    // Buat tabel transactions
    $db->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sender_id INTEGER,
        receiver_id INTEGER,
        amount INTEGER,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Cek jika user sudah ada
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        // Seeding data awal
        // Password di sini tidak di-hash untuk mempermudah simulasi, namun disarankan di-hash untuk aplikasi nyata
        // Namun karena ini simulasi hacker, kita biarkan simpel.
        $stmt = $db->prepare("INSERT INTO users (username, password, balance, role) VALUES (?, ?, ?, ?)");
        
        $stmt->execute(['victim', 'victim123', 1000, 'victim']);
        $stmt->execute(['attacker', 'attacker123', 50, 'attacker']);
        
        echo "Database berhasil di-setup dengan user 'victim' dan 'attacker'.<br>";
    } else {
        echo "Database sudah di-setup.<br>";
    }

} catch (PDOException $e) {
    die("Setup gagal: " . $e->getMessage());
}
?>
