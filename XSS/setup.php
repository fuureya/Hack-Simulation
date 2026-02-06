<?php
require_once 'db.php';

try {
    // Tabel untuk simpan pemesanan tiket
    $db->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        movie_title TEXT,
        customer_name TEXT,
        showtime TEXT
    )");

    // Tabel untuk simpan review/feedback (Vulnerable Stored XSS & Blind XSS)
    $db->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT,
        comment TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Tabel untuk admin
    $db->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT
    )");

    // Seed admin data
    $stmt = $db->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        $db->exec("INSERT INTO admins (username, password) VALUES ('admin', 'admin123')");
        
        // Seed some reviews
        $db->exec("INSERT INTO reviews (username, comment) VALUES ('Andi', 'Filmnya keren banget!')");
        $db->exec("INSERT INTO reviews (username, comment) VALUES ('Budi', 'Bioskopnya bersih dan nyaman.')");
        
        echo "Database XSS Cinema berhasil di-setup.<br>";
    } else {
        echo "Database sudah ada.<br>";
    }

} catch (PDOException $e) {
    die("Setup gagal: " . $e->getMessage());
}
?>
