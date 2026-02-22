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

// Fetch latest transactions
try {
    $stmt = $db->prepare("SELECT * FROM transactions WHERE sender_account = ? OR receiver_account = ? ORDER BY created_at DESC LIMIT 6");
    $stmt->execute([$user['acc_number'], $user['acc_number']]);
    $transactions = $stmt->fetchAll();
} catch (PDOException $e) {
    $transactions = [];
}

// Fetch contacts
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
    <title>Priority Vault | NeoBank</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #020617;
            color: #94a3b8;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .bg-neo {
            background: radial-gradient(circle at 0% 0%, #1e1b4b 0%, #020617 100%);
        }
    </style>
</head>

<body class="bg-neo min-h-screen pb-20">

    <!-- Premium Nav -->
    <nav class="sticky top-0 z-50 glass border-b border-white/5 px-6 py-4">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xl font-black text-white tracking-tighter">NeoBank</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="profile.php"
                    class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-white transition-colors">Security
                    Settings</a>
                <a href="logout.php"
                    class="p-2.5 bg-rose-500/10 text-rose-400 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-xl mx-auto px-6 py-12">
        <!-- Profile Header -->
        <header class="mb-10 flex items-center gap-5">
            <div
                class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-2xl font-black text-white shadow-2xl">
                <?= strtoupper(substr($user['username'], 0, 1)) ?>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1">Priority Member Account
                </p>
                <h2 class="text-2xl font-extrabold text-white tracking-tight leading-none">
                    <?= htmlspecialchars($user['username']) ?></h2>
            </div>
        </header>

        <!-- Balance Card -->
        <div class="glass rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden mb-10 border border-white/10">
            <div class="absolute -right-20 -bottom-20 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] mb-3">Total Estimated Vault
                Value</p>
            <h3 class="text-4xl font-black text-white tracking-widest leading-none mb-6">Rp
                <?= number_format($user['balance'], 0, ',', '.') ?></h3>
            <div class="flex items-center gap-3">
                <span
                    class="px-3 py-1 bg-white/5 rounded-lg text-[10px] font-bold text-slate-400 font-mono tracking-widest"><?= $user['acc_number'] ?></span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Active Node</span>
            </div>
        </div>

        <?php if ($message): ?>
            <div
                class="mb-8 p-5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-[1.5rem] text-sm font-bold flex items-center gap-4">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div
                class="mb-8 p-5 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-[1.5rem] text-sm font-bold flex items-center gap-4">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="grid grid-cols-4 gap-4 mb-10 text-center">
            <button onclick="openModal('transferModal')" class="space-y-3 group">
                <div
                    class="w-full aspect-square glass rounded-2xl flex items-center justify-center text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-lg active:scale-95">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest block">Transfer</span>
            </button>
            <div class="space-y-3 opacity-30 grayscale">
                <div class="w-full aspect-square glass rounded-2xl flex items-center justify-center text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest block">Charts</span>
            </div>
            <div class="space-y-3 opacity-30 grayscale">
                <div class="w-full aspect-square glass rounded-2xl flex items-center justify-center text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-1.17-1.99c1.263-2.257 1.923-4.856 1.923-7.581a1 1 0 00-2 0 8.06 8.06 0 01-2.07 5.081m-1.212-3.946a3 3 0 115.408-2.67c-.12.23-.27.42-.447.604l-4.242 4.242" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest block">Auth</span>
            </div>
            <div class="space-y-3 opacity-30 grayscale">
                <div class="w-full aspect-square glass rounded-2xl flex items-center justify-center text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest block">More</span>
            </div>
        </div>

        <!-- Transactions -->
        <div class="glass rounded-[2.5rem] p-8 border border-white/5">
            <div class="flex justify-between items-center mb-8 px-2">
                <h3 class="text-sm font-black text-white uppercase tracking-widest">Recent Activity</h3>
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Live Logs</span>
            </div>

            <div class="space-y-5">
                <?php foreach ($transactions as $tx): ?>
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl flex items-center justify-center <?= $tx['sender_account'] == $user['acc_number'] ? 'bg-rose-500/10 text-rose-400' : 'bg-emerald-500/10 text-emerald-400' ?>">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <?php if ($tx['sender_account'] == $user['acc_number']): ?>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    <?php else: ?>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                    <?php endif; ?>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white leading-tight">
                                    <?= htmlspecialchars($tx['description']) ?></p>
                                <p class="text-[10px] text-slate-500 font-medium">
                                    <?= date('d M, H:i', strtotime($tx['created_at'])) ?></p>
                            </div>
                        </div>
                        <p
                            class="text-sm font-black <?= $tx['sender_account'] == $user['acc_number'] ? 'text-rose-400' : 'text-emerald-400' ?> tracking-wider">
                            <?= $tx['sender_account'] == $user['acc_number'] ? '−' : '+' ?>
                            Rp<?= number_format($tx['amount'], 0, ',', '.') ?>
                        </p>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($transactions)): ?>
                    <div class="text-center py-10">
                        <p class="text-xs font-bold text-slate-600 uppercase tracking-[0.2em]">No transactions recorded</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal Base UI -->
    <div id="transferModal"
        class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-[100] hidden items-center justify-center p-6">
        <div
            class="glass w-full max-w-md rounded-[2.5rem] p-10 shadow-[0_0_100px_rgba(79,79,229,0.1)] border border-white/10 animate-in zoom-in-95 duration-300">
            <div class="flex justify-between items-center mb-10">
                <h2 class="text-2xl font-black text-white tracking-tight">Vault Transfer</h2>
                <button onclick="closeModal('transferModal')"
                    class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="transfer.php" method="POST" class="space-y-6">
                <!-- CSRF Vulnerable Flow: No anti-CSRF token -->
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 ml-1">Destination
                        Node</label>
                    <select name="to_account" required
                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none text-white appearance-none">
                        <option value="" class="bg-slate-900">Select Authorized Contact</option>
                        <?php foreach ($contacts as $c): ?>
                            <option value="<?= $c['acc_number'] ?>" class="bg-slate-900">
                                <?= htmlspecialchars($c['username']) ?> [<?= $c['acc_number'] ?>]</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 ml-1">Allocation
                        Amount (NeoIDR)</label>
                    <input type="number" name="amount" required min="10000" placeholder="Minimum: 10,000"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none text-white placeholder-slate-700">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 ml-1">Account
                        Secret PIN</label>
                    <input type="password" name="pin" required maxlength="6" placeholder="••••••"
                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none text-white placeholder-slate-700 text-center tracking-[1em]">
                </div>
                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-5 rounded-2xl shadow-xl shadow-emerald-500/10 transition-all active:scale-[0.98] mt-4">
                    Authorize Transaction
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
    </script>
</body>

</html>