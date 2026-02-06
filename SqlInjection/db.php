<?php
$host = 'hms-db';
$user = 'hms_user';
$pass = 'hms_pass';
$db   = 'hospital_db';

// Koneksi sengaja menggunakan mysqli procedural untuk kemudahan simulasi error
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
