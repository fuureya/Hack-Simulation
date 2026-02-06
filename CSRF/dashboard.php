<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Ambil transaksi terakhir
try {
    $stmt = $db->prepare("SELECT * FROM transactions WHERE sender_account = ? OR receiver_account = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user['acc_number'], $user['acc_number']]);
    $transactions = $stmt->fetchAll();
} catch (PDOException $e) {
    // Jika kolom tidak ditemukan, kemungkinan database belum di-setup ulang
    $_SESSION['error'] = "Database out of sync! Silakan akses <a href='setup.php' class='underline'>setup.php</a> untuk memperbarui skema perbankan v2.";
    $transactions = [];
}

// Ambil daftar kontak (user lain)
$stmt = $db->prepare("SELECT username, acc_number FROM users WHERE id != ?");
$stmt->execute([$user_id]);
$contacts = $stmt->fetchAll();

$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoBank - My Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Public Sans', sans-serif; background-color: #f0f2f5; }
        .bca-blue { background-color: #00529C; }
        .bca-gradient { background: linear-gradient(135deg, #00529C 0%, #003A70 100%); }
    </style>
</head>
<body class="pb-10">
    <!-- Header ala myBCA -->
    <header class="bca-blue text-white p-6 pb-20 rounded-b-[2.5rem] shadow-lg sticky top-0 z-50">
        <div class="max-w-md mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center font-bold text-xl backdrop-blur-md">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
                <div>
                    <p class="text-[10px] font-bold opacity-70 uppercase tracking-widest">Selamat Datang,</p>
                    <h1 class="text-lg font-bold"><?= htmlspecialchars($user['username']) ?></h1>
                </div>
            </div>
            <a href="logout.php" class="bg-white/10 p-3 rounded-2xl backdrop-blur-md border border-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </header>

    <main class="max-w-md mx-auto px-4 -mt-12 space-y-6">
        <!-- Balance Card -->
        <div class="bg-white rounded-[2rem] p-8 shadow-xl border border-white/10 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bca-blue opacity-5 rounded-full -mr-16 -mt-16"></div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Saldo (NeoIDR)</p>
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Rp <?= number_format($user['balance'], 0, ',', '.') ?></h2>
            <div class="flex items-center gap-2 mt-4 text-[10px] text-slate-400 font-bold">
                <span class="bg-slate-100 px-2 py-1 rounded-md tracking-wider"><?= $user['acc_number'] ?></span>
                <span class="opacity-50">NeoBank Virtual</span>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-500 text-white p-4 rounded-2xl text-sm font-bold text-center animate-bounce">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-rose-500 text-white p-4 rounded-2xl text-sm font-bold text-center shadow-lg">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="grid grid-cols-4 gap-4">
            <button onclick="openModal('transferModal')" class="flex flex-col items-center gap-2 group">
                <div class="w-14 h-14 bca-blue rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20 group-active:scale-90 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-500">Transfer</span>
            </button>
            <div class="flex flex-col items-center gap-2 opacity-40">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-400 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-500">Tagihan</span>
            </div>
            <div class="flex flex-col items-center gap-2 opacity-40">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-400 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-500">Top Up</span>
            </div>
            <div class="flex flex-col items-center gap-2 opacity-40">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-slate-400 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-slate-500">Lainnya</span>
            </div>
        </div>

        <!-- Transactions -->
        <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-slate-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-slate-800">Transaksi Terakhir</h3>
                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Semua</span>
            </div>
            <div class="space-y-4">
                <?php foreach ($transactions as $tx): ?>
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-2xl transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center <?= $tx['sender_account'] == $user['acc_number'] ? 'bg-rose-50 text-rose-500' : 'bg-emerald-50 text-emerald-500' ?>">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <?php if ($tx['sender_account'] == $user['acc_number']): ?>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                     <?php else: ?>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                     <?php endif; ?>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800"><?= htmlspecialchars($tx['description']) ?></p>
                                <p class="text-[10px] text-slate-400"><?= $tx['created_at'] ?></p>
                            </div>
                        </div>
                        <p class="text-sm font-black <?= $tx['sender_account'] == $user['acc_number'] ? 'text-rose-600' : 'text-emerald-600' ?>">
                            <?= $tx['sender_account'] == $user['acc_number'] ? '-' : '+' ?> Rp <?= number_format($tx['amount'], 0, ',', '.') ?>
                        </p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($transactions)): ?>
                    <p class="text-center text-slate-400 text-xs py-4">Belum ada transaksi.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Transfer Modal -->
    <div id="transferModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-end sm:items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-t-[2.5rem] sm:rounded-[2.5rem] p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-xl font-bold text-slate-900">Transfer Dana</h2>
                <button onclick="closeModal('transferModal')" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>

            <form action="transfer.php" method="POST" class="space-y-6">
                <!-- VULNERABLE: No CSRF Token Here -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Rekening Tujuan</label>
                    <select name="to_account" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <option value="">Pilih Kontak</option>
                        <?php foreach ($contacts as $c): ?>
                            <option value="<?= $c['acc_number'] ?>"><?= htmlspecialchars($c['username']) ?> - <?= $c['acc_number'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Jumlah Transfer (Rp)</label>
                    <input type="number" name="amount" required min="10000" placeholder="Min. 10.000" 
                        class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">PIN Transaksi</label>
                    <div class="relative">
                        <input type="password" id="pinInput" name="pin" required maxlength="6" placeholder="Masukkan 6 digit PIN" 
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 pr-12 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none tracking-[1em] text-center transition-all">
                        <button type="button" onclick="togglePin()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="w-full bca-gradient text-white font-bold py-5 rounded-3xl shadow-xl shadow-blue-500/30 active:scale-95 transition-all text-sm">
                    Konfirmasi Transfer
                </button>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        function togglePin() {
            const pinInput = document.getElementById('pinInput');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pinInput.type === 'password') {
                pinInput.type = 'text';
                pinInput.classList.remove('tracking-[1em]');
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                pinInput.type = 'password';
                pinInput.classList.add('tracking-[1em]');
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</body>
</html>
