<?php
$host = getenv('DB_HOST') ?: 'labsec-db';
$dbname = getenv('DB_NAME') ?: 'labsec_dashboard';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'labsec_root_2026';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
