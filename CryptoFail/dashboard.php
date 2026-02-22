<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM sv_users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch transaction history
$stmt2 = $pdo->prepare("SELECT * FROM sv_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt2->execute([$userId]);
$transactions = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Report Generation Sync (Simulates GET Leakage)
$showSensitive = isset($_GET['show']) && $_GET['show'] === 'true';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuantumGuard | Enterprise Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: #020617;
        }

        .glass {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .cyber-card {
            border-left: 2px solid #6366f1;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.05) 0%, transparent 100%);
        }

        .status-pulse {
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }
    </style>
</head>

<body class="bg-[#020617] text-slate-400 min-h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-black/40 border-r border-white/5 p-8 flex flex-col shrink-0">
        <div class="flex items-center gap-3 mb-12">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002-2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <span class="text-xl font-bold text-white tracking-widest uppercase">Vault</span>
        </div>

        <nav class="flex-1 space-y-4">
            <a href="#"
                class="flex items-center gap-4 text-xs font-bold text-indigo-400 uppercase tracking-widest bg-indigo-500/10 p-4 rounded-xl border border-indigo-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Overview
            </a>
            <a href="#"
                class="flex items-center gap-4 text-xs font-bold text-slate-600 hover:text-white uppercase tracking-widest p-4 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Security
            </a>
            <a href="#"
                class="flex items-center gap-4 text-xs font-bold text-slate-600 hover:text-white uppercase tracking-widest p-4 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Assets
            </a>
        </nav>

        <a href="index.php"
            class="p-4 bg-rose-500/10 text-rose-400 rounded-xl text-xs font-bold uppercase tracking-widest text-center hover:bg-rose-500 hover:text-white transition-all">
            Logout
        </a>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto p-12">
        <header class="flex justify-between items-end mb-12">
            <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] mb-2">Authenticated Secure
                    Node</p>
                <h1 class="text-3xl font-bold text-white tracking-widest uppercase">Welcome,
                    <?= htmlspecialchars($user['username']) ?></h1>
            </div>
            <div class="text-right">
                <p
                    class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-1 flex items-center justify-end gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 status-pulse"></span>
                    Quantum link active
                </p>
                <p class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">Node-ID:
                    0x<?= dechex($userId * 1234) ?></p>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Balance Card -->
            <div class="glass rounded-[2rem] p-10 border border-white/5 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl"></div>
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-4">Total Assets in Vault
                </p>
                <h2 class="text-5xl font-black text-white tracking-widest leading-none mb-8">
                    Rp<?= number_format($user['balance'], 0, ',', '.') ?></h2>
                <div class="flex items-center gap-4">
                    <span
                        class="px-3 py-1.5 bg-white/5 rounded-lg text-[10px] font-bold text-slate-500 tracking-widest border border-white/5">QG-CORE-v4</span>
                    <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest group cursor-help">
                        Sync frequency: <span class="text-white">Real-time</span>
                    </span>
                </div>
            </div>

            <!-- Vault Details (Sensitive Data Simulation) -->
            <div class="glass rounded-[2rem] p-10 border border-white/5">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.3em] mb-8">Metadata Identifiers</h3>
                <div class="grid grid-cols-2 gap-y-8 gap-x-12">
                    <div class="space-y-1">
                        <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest">Public Alias</p>
                        <p class="text-sm font-bold text-slate-300"><?= htmlspecialchars($user['username']) ?></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest">Secure PIN (Cached)
                        </p>
                        <p class="text-sm font-mono font-bold text-rose-400/80"><?= $user['pin'] ?></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest">Legacy Token Hash
                            (MD5)</p>
                        <p class="text-[10px] font-mono text-indigo-400 truncate"><?= $user['password'] ?></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest">Credit Relay Node</p>
                        <p class="text-sm font-mono font-bold text-amber-500/80"><?= $user['credit_card'] ?></p>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-white/5 flex gap-4">
                    <a href="dashboard.php?show=true&card=<?= $user['credit_card'] ?>&pin=<?= $user['pin'] ?>&token=<?= md5($user['username'] . 'safevault') ?>"
                        class="flex-1 bg-white/5 hover:bg-white/10 p-4 rounded-xl text-[10px] font-black text-white uppercase tracking-widest text-center transition-all border border-white/5">
                        Generate Node Metadata Report
                    </a>
                </div>
            </div>
        </div>

        <?php if ($showSensitive): ?>
            <div
                class="mb-12 glass rounded-[2rem] p-8 border border-rose-500/20 bg-rose-500/5 animate-in slide-in-from-top-4 duration-500">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 flex items-center justify-center text-rose-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h4 class="text-xs font-black text-white uppercase tracking-widest">Metadata Leakage Warning</h4>
                </div>
                <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest mb-6 leading-relaxed">
                    Report generated for the following node identifiers. WARNING: This data is currently exposed in the
                    active transmission stream (GET).
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-4 bg-black/40 rounded-xl border border-white/5">
                        <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest mb-1">Stream: Card</p>
                        <code class="text-xs text-rose-400"><?= htmlspecialchars($_GET['card'] ?? '0x00') ?></code>
                    </div>
                    <div class="p-4 bg-black/40 rounded-xl border border-white/5">
                        <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest mb-1">Stream: PIN</p>
                        <code class="text-xs text-rose-400"><?= htmlspecialchars($_GET['pin'] ?? '0000') ?></code>
                    </div>
                    <div class="p-4 bg-black/40 rounded-xl border border-white/5">
                        <p class="text-[8px] font-black text-slate-600 uppercase tracking-widest mb-1">Stream: Session Token
                        </p>
                        <code
                            class="text-xs text-rose-400 truncate block"><?= htmlspecialchars($_GET['token'] ?? 'null') ?></code>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Transaction Logs -->
        <div class="glass rounded-[2rem] p-10 border border-white/5">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.3em]">Access & Transaction Logs</h3>
                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest italic">Live Feed (Node
                    v4.2)</span>
            </div>

            <div class="space-y-4">
                <?php foreach ($transactions as $tx): ?>
                    <div
                        class="flex items-center justify-between p-5 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/[0.08] transition-all group">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-10 h-10 rounded-xl bg-black/40 flex items-center justify-center text-slate-500 group-hover:text-white group-hover:bg-indigo-600/20 transition-all border border-white/5">
                                <span class="text-[10px] font-black uppercase"><?= substr($tx['type'], 0, 1) ?></span>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-white uppercase tracking-widest mb-1">
                                    <?= htmlspecialchars($tx['note']) ?></p>
                                <p class="text-[8px] font-bold text-slate-600 uppercase tracking-[0.2em]">
                                    <?= date('H:i:s • d.M.Y', strtotime($tx['created_at'])) ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p
                                class="text-sm font-black <?= $tx['type'] == 'credit' ? 'text-emerald-500' : 'text-rose-500' ?> tracking-widest mb-1 uppercase">
                                <?= $tx['type'] == 'credit' ? 'SYNC' : 'AUTH' ?>
                            </p>
                            <p class="text-sm font-bold text-white tracking-widest">
                                Rp<?= number_format($tx['amount'], 0, ',', '.') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

</body>

</html>