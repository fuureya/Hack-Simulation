<?php
$host = 'labsec-db';
$dbname = 'neohms_sqli';
$user = 'root';
$pass = 'labsec_root_2026';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
