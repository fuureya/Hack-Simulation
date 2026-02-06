<?php
$host = getenv('DB_HOST') ?: 'neobank-db';
$dbname = getenv('DB_NAME') ?: 'neobank';
$user = getenv('DB_USER') ?: 'neobank_user';
$pass = getenv('DB_PASS') ?: 'neobank_pass';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
