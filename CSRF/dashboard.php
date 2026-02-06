<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = '';
$error = '';

// Proses Transfer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer'])) {
    $to_username = $_POST['to_username'] ?? '';
    $amount = intval($_POST['amount'] ?? 0);

    if ($amount > 0) {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$to_username]);
        $receiver = $stmt->fetch();

        if ($receiver) {
            // Cek saldo pengirim
            $stmt = $db->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $sender_balance = $stmt->fetchColumn();

            if ($sender_balance >= $amount) {
                // Proses Transfer (Tanpa CSRF Token!)
                $db->beginTransaction();
                try {
                    $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?")->execute([$amount, $user_id]);
                    $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?")->execute([$amount, $receiver['id']]);
                    $db->prepare("INSERT INTO transactions (sender_id, receiver_id, amount) VALUES (?, ?, ?)")->execute([$user_id, $receiver['id'], $amount]);
                    $db->commit();
                    $msg = "Transfer ke " . htmlspecialchars($to_username) . " sebesar $" . $amount . " berhasil!";
                } catch (Exception $e) {
                    $db->rollBack();
                    $error = "Terjadi kesalahan.";
                }
            } else {
                $error = "Saldo tidak cukup!";
            }
        } else {
            $error = "Penerima tidak ditemukan!";
        }
    } else {
        $error = "Jumlah harus lebih dari 0!";
    }
}

// Ambil data user terbaru
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Ambil riwayat transaksi
$stmt = $db->prepare("
    SELECT t.*, u_s.username as sender, u_r.username as receiver 
    FROM transactions t
    JOIN users u_s ON t.sender_id = u_s.id
    JOIN users u_r ON t.receiver_id = u_r.id
    WHERE t.sender_id = ? OR t.receiver_id = ?
    ORDER BY t.timestamp DESC LIMIT 5
");
$stmt->execute([$user_id, $user_id]);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoBank - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen">
    <!-- Navbar -->
    <nav class="border-b border-white/10 bg-white/5 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">NeoBank</span>
                </div>
                <div class="flex items-center gap-6">
                    <span class="text-sm text-slate-400">Halo, <span class="text-white font-semibold"><?= htmlspecialchars($user['username']) ?></span></span>
                    <a href="profile.php" class="text-sm hover:text-indigo-400 transition-colors">Profil</a>
                    <a href="logout.php" class="bg-red-500/10 text-red-400 hover:bg-red-500/20 px-4 py-2 rounded-lg text-sm font-medium transition-all border border-red-500/30">Keluar</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Balance Card -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-8 rounded-3xl shadow-xl shadow-indigo-500/20 relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-indigo-100/70 text-sm font-medium mb-1">Total Saldo Anda</p>
                        <h2 class="text-4xl font-bold text-white tracking-tight">$<?= number_format($user['balance'], 0, ',', '.') ?></h2>
                        <div class="mt-8 flex items-center gap-2 text-xs text-indigo-100/60">
                            <span class="px-2 py-1 bg-white/10 rounded-full border border-white/20">Akun <?= strtoupper($user['role']) ?></span>
                        </div>
                    </div>
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                </div>

                <!-- Transaction History -->
                <div class="bg-white/5 border border-white/10 rounded-3xl p-6 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Transaksi Terakhir
                    </h3>
                    <div class="space-y-4">
                        <?php if (empty($transactions)): ?>
                            <p class="text-slate-500 text-sm italic">Belum ada transaksi.</p>
                        <?php else: ?>
                            <?php foreach ($transactions as $tx): ?>
                                <div class="flex justify-between items-center p-3 rounded-2xl bg-white/5 border border-white/5">
                                    <div class="flex items-center gap-3">
                                        <div class="<?= $tx['sender_id'] == $user_id ? 'bg-red-500/20 text-red-400' : 'bg-green-500/20 text-green-400' ?> p-2 rounded-xl">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $tx['sender_id'] == $user_id ? 'M19 14l-7 7m0 0l-7-7m7 7V3' : 'M5 10l7-7m0 0l7 7m-7-7v18' ?>"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium"><?= htmlspecialchars($tx['sender_id'] == $user_id ? 'Ke: ' . $tx['receiver'] : 'Dari: ' . $tx['sender']) ?></p>
                                            <p class="text-[10px] text-slate-500"><?= $tx['timestamp'] ?></p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-bold <?= $tx['sender_id'] == $user_id ? 'text-red-400' : 'text-green-400' ?>">
                                        <?= $tx['sender_id'] == $user_id ? '-' : '+' ?>$<?= $tx['amount'] ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Transfer Form -->
            <div class="lg:col-span-2">
                <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm h-full">
                    <h3 class="text-2xl font-bold mb-6">Transfer Dana</h3>
                    
                    <?php if ($msg): ?>
                        <div class="bg-green-500/20 border border-green-500/50 text-green-200 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <?= $msg ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="bg-red-500/20 border border-red-500/50 text-red-200 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2">Username Penerima</label>
                                <input type="text" name="to_username" required
                                    class="w-full bg-slate-900 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all placeholder-slate-600"
                                    placeholder="Contoh: attacker">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2">Jumlah Transfer ($)</label>
                                <input type="number" name="amount" required min="1"
                                    class="w-full bg-slate-900 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all placeholder-slate-600"
                                    placeholder="0">
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" name="transfer"
                                class="group relative w-full md:w-auto px-10 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl transition-all shadow-lg shadow-indigo-500/20 overflow-hidden">
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    Kirim Sekarang
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </button>
                        </div>
                    </form>

                    <div class="mt-12 p-6 bg-yellow-500/10 border border-yellow-500/30 rounded-2xl">
                        <h4 class="text-yellow-400 font-bold flex items-center gap-2 mb-2 text-sm italic">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Security Note (CSRF Vulnerability)
                        </h4>
                        <p class="text-xs text-slate-400 leading-relaxed italic">
                            Fitur transfer ini sengaja dibuat rentan terhadap serangan <strong>CSRF</strong>. Form di atas tidak menyertakan 
                            token keamanan unik sehingga penyerang dapat memicu aksi transfer secara paksa jika korban mengunjungi link berbahaya.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
