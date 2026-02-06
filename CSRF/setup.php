<?php
require_once 'db.php';

// Drop existing tables
$db->exec("DROP TABLE IF EXISTS transactions");
$db->exec("DROP TABLE IF EXISTS users");

// Tabel Users v2
$db->exec("CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    username TEXT UNIQUE,
    password TEXT,
    acc_number TEXT UNIQUE,
    pin TEXT,
    balance DECIMAL(15,2),
    role TEXT
)");

// Tabel Transactions v2
$db->exec("CREATE TABLE transactions (
    id INTEGER PRIMARY KEY,
    sender_account TEXT,
    receiver_account TEXT,
    amount DECIMAL(15,2),
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

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
