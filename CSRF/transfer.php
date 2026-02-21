<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_id = $_SESSION['user_id'];
    $to_account = $_POST['to_account'] ?? '';
    $amount = (float)($_POST['amount'] ?? 0);
    $pin = $_POST['pin'] ?? '';
    $bypass_pin = $_POST['bypass_pin'] ?? '0';

    // Ambil data pengirim
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$from_id]);
    $sender = $stmt->fetch();

    // VULNERABILITY: Logical flaw allowing PIN bypass if bypass_pin=1 is sent
    // Ini mensimulasikan kegagalan pengecekan otentikasi tambahan pada level endpoint
    if ($bypass_pin !== '1' && $sender['pin'] !== $pin) {
        $_SESSION['error'] = "PIN yang Anda masukkan salah!";
        header('Location: dashboard.php');
        exit;
    }

    if ($amount <= 0 || $amount > $sender['balance']) {
        $_SESSION['error'] = "Saldo tidak mencukupi atau nominal tidak valid.";
        header('Location: dashboard.php');
        exit;
    }

    // Ambil data penerima
    $stmt = $db->prepare("SELECT * FROM users WHERE acc_number = ?");
    $stmt->execute([$to_account]);
    $receiver = $stmt->fetch();

    if (!$receiver) {
        $_SESSION['error'] = "Rekening tujuan tidak ditemukan.";
        header('Location: dashboard.php');
        exit;
    }

    try {
        $db->beginTransaction();

        // Kurangi saldo pengirim
        $stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $from_id]);

        // Tambah saldo penerima
        $stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE acc_number = ?");
        $stmt->execute([$amount, $to_account]);

        // Log transaksi
        $stmt = $db->prepare("INSERT INTO transactions (sender_account, receiver_account, amount, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sender['acc_number'], $receiver['acc_number'], $amount, "Transfer ke " . $receiver['username']]);

        $db->commit();
        $_SESSION['message'] = "Transfer Berhasil ke " . $receiver['username'] . " sejumlah Rp " . number_format($amount, 0, ',', '.');
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
    }

    header('Location: dashboard.php');
    exit;
}
?>
