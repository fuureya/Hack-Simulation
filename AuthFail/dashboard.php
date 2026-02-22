<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['sl_user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['sl_user_id'];
$username = $_SESSION['sl_username'];
$role = $_SESSION['sl_role'];
$sessionId = session_id();

$stmt = $pdo->prepare("SELECT * FROM sl_users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// VULN A07: Logout does NOT invalidate session on server
if (isset($_GET['logout'])) {
    // VULN: session_destroy() is purposefully omitted
    setcookie(session_name(), '', time() - 3600, '/');
    setcookie('remember_me', '', time() - 3600, '/');
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Dashboard | SecureLogin Corp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        .sidebar {
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-72 sidebar text-slate-300 flex flex-col p-6 space-y-8">
        <div class="flex items-center gap-3 px-2">
            <div
                class="w-10 h-10 rounded-xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002-2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <span class="text-xl font-bold text-white tracking-tight">SecureLogin</span>
        </div>

        <nav class="flex-1 space-y-2">
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-white/10 text-white font-semibold transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/5 hover:text-white transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil Saya
            </a>
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/5 hover:text-white transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Dokumen Sensus
            </a>
            <a href="#"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-white/5 hover:text-white transition-all font-medium text-rose-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002-2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Keamanan Akun
            </a>
        </nav>

        <div class="pt-6 border-t border-white/10">
            <a href="?logout=1"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-rose-500/10 text-rose-400 font-bold hover:bg-rose-500 hover:text-white transition-all active:scale-[0.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar (SSO)
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10 overflow-y-auto">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Ringkasan Aktivitas</h2>
                <p class="text-slate-500 font-medium">Selamat datang kembali, <?= htmlspecialchars($username) ?>.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right mr-2">
                    <p class="text-sm font-bold text-slate-900 line-clamp-1"><?= htmlspecialchars($username) ?></p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?= strtoupper($role) ?>
                        Account</p>
                </div>
                <div
                    class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg border-2 border-indigo-200">
                    <?= strtoupper(substr($username, 0, 1)) ?>
                </div>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <div class="glass-card p-8 rounded-[2rem] shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest mb-1">Akses Sistem</p>
                <h3 class="text-3xl font-black text-indigo-900">12 Terkoneksi</h3>
            </div>
            <div class="glass-card p-8 rounded-[2rem] shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest mb-1">Status Keamanan</p>
                <h3 class="text-3xl font-black text-emerald-900">Optimal</h3>
            </div>
            <div class="glass-card p-8 rounded-[2rem] shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-widest mb-1">Masa Aktif Token</p>
                <h3 class="text-3xl font-black text-amber-900">22 Jam</h3>
            </div>
        </div>

        <!-- System Info & Logs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-slate-100">
                <div class="flex items-center justify-between mb-8">
                    <h4 class="text-xl font-extrabold text-slate-900 tracking-tight">Informasi Sesi</h4>
                    <span
                        class="px-3 py-1 bg-emerald-100 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest">Active</span>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Global Session
                            ID</span>
                        <span
                            class="text-xs font-mono font-bold text-slate-900"><?= htmlspecialchars($sessionId) ?></span>
                    </div>
                    <div class="flex justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Node ID</span>
                        <span class="text-xs font-bold text-slate-900">ENT-NOD-003</span>
                    </div>
                    <?php if ($user['remember_token']): ?>
                        <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                            <span
                                class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2 text-center">Persistence
                                Token</span>
                            <span
                                class="block text-xs font-mono font-bold text-indigo-900 break-all text-center"><?= htmlspecialchars($user['remember_token']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-slate-100">
                <h4 class="text-xl font-extrabold text-slate-900 tracking-tight mb-8">Log Aktivitas Terbaru</h4>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">Berhasil masuk ke portal SSO</p>
                            <p class="text-xs text-slate-400 font-medium">Baru saja • Dari IP: 192.168.1.45</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-2 h-2 rounded-full bg-slate-300 mt-1.5 shrink-0"></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Perubahan pengaturan keamanan ditunda</p>
                            <p class="text-xs text-slate-400 font-medium">2 jam yang lalu</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-2 h-2 rounded-full bg-slate-300 mt-1.5 shrink-0"></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Akses dokumen teknis disetujui</p>
                            <p class="text-xs text-slate-400 font-medium">Kemarin, 14:20</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>