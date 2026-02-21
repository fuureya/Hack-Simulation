<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Ambil semua review untuk dimoderasi
$reviews = $db->query("SELECT * FROM reviews ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CinemaX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 h-screen sticky top-0 border-r border-white/5 p-6 hidden lg:block">
            <div class="flex items-center gap-2 mb-12">
                <div class="w-8 h-8 bg-rose-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path></svg>
                </div>
                <span class="text-xl font-bold">CinemaX Admin</span>
            </div>

            <nav class="space-y-1">
                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-rose-600/10 text-rose-500 rounded-xl font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    Reviews
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-white/5 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Ticket Sales
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-white/5 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Users
                </a>
            </nav>

            <div class="absolute bottom-6 left-6 right-6">
                 <a href="logout_admin.php" class="flex items-center gap-3 px-4 py-3 text-rose-400 hover:bg-rose-500/10 rounded-xl transition-all border border-rose-500/20 text-sm font-bold justify-center">
                    Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-3xl font-black text-white">Review Management</h1>
                    <p class="text-slate-500 text-sm mt-1">Moderasi konten ulasan dari penonton.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-rose-500 uppercase tracking-widest">Admin Role</p>
                        <p class="text-sm font-medium text-white"><?= $_SESSION['admin_user'] ?></p>
                    </div>
                    <div class="w-12 h-12 bg-slate-800 rounded-full border border-white/10 flex items-center justify-center font-bold text-rose-500">
                        A
                    </div>
                </div>
            </header>

            <!-- Table -->
            <div class="bg-slate-900 border border-white/5 rounded-[2rem] overflow-hidden shadow-2xl">
                <table class="w-full text-left">
                    <thead class="bg-white/5 border-b border-white/5">
                        <tr>
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">User</th>
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Komentar</th>
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Waktu</th>
                            <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($reviews as $row): ?>
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center text-xs font-bold text-slate-400">
                                            <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                        </div>
                                        <span class="font-bold text-white"><?= htmlspecialchars($row['username']) ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 max-w-md">
                                    <div class="text-slate-300 text-sm leading-relaxed">
                                        <!-- VULNERABLE POINT: Blind XSS Trigger -->
                                        <?= $row['comment'] ?>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-xs text-slate-500 font-medium">
                                    <?= $row['created_at'] ?>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex gap-2">
                                        <button class="px-3 py-1.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-tighter rounded-lg border border-emerald-500/20 hover:bg-emerald-500/20 transition-all">Approve</button>
                                        <button class="px-3 py-1.5 bg-rose-500/10 text-rose-400 text-[10px] font-bold uppercase tracking-tighter rounded-lg border border-rose-500/20 hover:bg-rose-500/20 transition-all">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-12 p-8 bg-indigo-500/10 border border-indigo-500/20 rounded-3xl">
                <h4 class="text-indigo-400 font-bold flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Info Keamanan (Blind XSS)
                </h4>
                <p class="text-xs text-slate-400 leading-relaxed italic">
                    Halaman admin ini menampilkan ulasan dari database tanpa enkoding. 
                    Jika penyerang memasukkan script lewat halaman depan, script tersebut tidak akan jalan di depan (Stored XSS), 
                    tetapi akan dieksekusi di sini saat admin membukanya. Ini disebut <strong>Blind XSS</strong>.
                </p>
            </div>
        </main>
    </div>
</body>
</html>
