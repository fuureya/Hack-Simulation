<?php
require_once 'db.php';

// Create database if not exists
$db->exec("CREATE DATABASE IF NOT EXISTS neobank_csrf");
$db->exec("USE neobank_csrf");

// Drop existing tables
$db->exec("DROP TABLE IF EXISTS transactions");
$db->exec("DROP TABLE IF EXISTS users");

// Tabel Users v2
$db->exec("CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    acc_number VARCHAR(15) UNIQUE,
    pin VARCHAR(6),
    balance DECIMAL(15,2),
    role VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Tabel Transactions v2
$db->exec("CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_account VARCHAR(15),
    receiver_account VARCHAR(15),
    amount DECIMAL(15,2),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Seeding Data
$users = [
    ['victim', 'victim123', '1234567890', '123456', 10000000, 'victim'],
    ['attacker', 'attacker123', '0987654321', '654321', 500000, 'attacker'],
    ['budi', 'budi123', '1122334455', '111111', 2500000, 'user'],
    ['siti', 'siti123', '5544332211', '222222', 1750000, 'user'],
    ['ani', 'ani123', '9988776655', '333333', 3000000, 'user']
];

$stmt = $db->prepare("INSERT INTO users (username, password, acc_number, pin, balance, role) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($users as $u) {
    $stmt->execute($u);
}

// Dummy Transactions
$db->exec("INSERT INTO transactions (sender_account, receiver_account, amount, description) VALUES 
('1234567890', '1122334455', 50000, 'Bayar Bakso'),
('5544332211', '1234567890', 200000, 'Gantian Makan'),
('1122334455', '1234567890', 150000, 'Patungan Kado')");

echo "NeoBank v2 Setup Complete! Nomor Rekening Victim: 1234567890 | PIN: 123456";
?>
