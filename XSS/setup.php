<?php
require_once 'db.php';

try {
    // Create database if not exists
    $db->exec("CREATE DATABASE IF NOT EXISTS cinemax_xss");
    $db->exec("USE cinemax_xss");

    // Drop existing tables
    $db->exec("DROP TABLE IF EXISTS reviews");
    $db->exec("DROP TABLE IF EXISTS bookings");
    $db->exec("DROP TABLE IF EXISTS admins");

    // Tabel untuk simpan pemesanan tiket
    $db->exec("CREATE TABLE bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        movie_title VARCHAR(255),
        customer_name VARCHAR(255),
        showtime VARCHAR(100)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Tabel untuk simpan review/feedback (Vulnerable Stored XSS & Blind XSS)
    $db->exec("CREATE TABLE reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Tabel untuk admin
    $db->exec("CREATE TABLE admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE,
        password VARCHAR(255)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Seed admin data
    $db->exec("INSERT INTO admins (username, password) VALUES ('admin', 'admin123')");
    
    // Seed some reviews
    $db->exec("INSERT INTO reviews (username, comment) VALUES ('Andi', 'Filmnya keren banget!')");
    $db->exec("INSERT INTO reviews (username, comment) VALUES ('Budi', 'Bioskopnya bersih dan nyaman.')");
    
    echo "Database XSS Cinema berhasil di-setup dengan MariaDB.<br>";

} catch (PDOException $e) {
    die("Setup gagal: " . $e->getMessage());
}
?>
