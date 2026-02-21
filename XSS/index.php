<?php
require_once 'db.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $username = $_POST['username'] ?? 'Anonymous';
    $comment = $_POST['comment'] ?? ''; // Tujuannya rentan XSS, jadi tidak ada sanitasi

    if (!empty($comment)) {
        $stmt = $db->prepare("INSERT INTO reviews (username, comment) VALUES (?, ?)");
        $stmt->execute([$username, $comment]);
        $msg = "Terima kasih atas ulasannya!";
    }
}

// Ambil review untuk ditampilkan (Stored XSS)
$reviews = $db->query("SELECT * FROM reviews ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaX - Pesan Tiket Bioskop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-200 min-h-screen">
    <!-- Navbar -->
    <nav class="border-b border-white/5 bg-slate-900/50 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-tr from-rose-500 to-orange-400 rounded-xl flex items-center justify-center shadow-lg shadow-rose-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path></svg>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">CinemaX</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="index.php" class="text-white font-medium">Beranda</a>
                    <form action="search.php" method="GET" class="relative group">
                        <input type="text" name="q" placeholder="Cari film..." 
                            class="bg-white/5 border border-white/10 rounded-full py-2 px-5 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 transition-all w-64">
                        <svg class="w-4 h-4 text-slate-500 absolute left-4 top-3 group-focus-within:text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </form>
                    <a href="admin_login.php" class="text-sm text-slate-400 hover:text-white transition-colors">Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="relative rounded-[2.5rem] overflow-hidden mb-16 h-[500px] group">
            <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&q=80&w=2000" alt="Cinema Hero" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-[#0f172a]/40 to-transparent"></div>
            <div class="absolute bottom-12 left-12 max-w-2xl">
                <span class="bg-rose-600 text-white text-xs font-bold px-3 py-1 rounded-full mb-4 inline-block">SEDANG TAYANG</span>
                <h1 class="text-6xl font-black text-white mb-4 leading-tight">Misteri Kode yang <br><span class="text-rose-500 italic text-5xl">Terinfeksi.</span></h1>
                <p class="text-slate-300 text-lg mb-8 leading-relaxed">Saksikan thriller teknologi paling mendebarkan tahun ini. Pesan tiket Anda sekarang sebelum kehabisan!</p>
                <div class="flex gap-4">
                    <button class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-4 px-10 rounded-2xl transition-all shadow-xl shadow-rose-600/20">Pesan Tiket</button>
                    <button class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-bold py-4 px-10 rounded-2xl transition-all">Lihat Trailer</button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Form Review (Stored XSS Endpoint) -->
            <div class="lg:col-span-1">
                <div class="bg-slate-900 border border-white/5 rounded-[2rem] p-8 sticky top-32 shadow-2xl">
                    <h3 class="text-2xl font-bold mb-2">Beri Ulasan</h3>
                    <p class="text-slate-500 text-sm mb-6">Ulasan Anda membantu kami menjadi lebih baik.</p>

                    <?php if ($msg): ?>
                        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-6 text-sm">
                            <?= $msg ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="space-y-5">
                        <input type="hidden" name="submit_review" value="1">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Nama Anda</label>
                            <input type="text" name="username" placeholder="Budi Santoso" required
                                class="w-full bg-slate-800 border border-white/5 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-rose-500 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Komentar</label>
                            <textarea name="comment" rows="4" placeholder="Apa pendapatmu tentang bioskop ini?" required
                                class="w-full bg-slate-800 border border-white/5 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-rose-500 transition-all text-sm"></textarea>
                        </div>
                        <button type="submit" 
                            class="w-full bg-slate-100 hover:bg-white text-slate-900 font-bold py-4 rounded-xl transition-all shadow-lg shadow-black/20">
                            Kirim Ulasan
                        </button>
                    </form>
                </div>
            </div>

            <!-- List Review (Stored XSS Display) -->
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-3xl font-bold">Ulasan Penggemar</h3>
                    <span class="text-rose-500 font-semibold cursor-pointer text-sm">Lihat Semua</span>
                </div>
                
                <div class="space-y-6">
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-20 bg-slate-900/50 rounded-3xl border border-dashed border-white/10">
                            <p class="text-slate-500">Belum ada ulasan. Jadilah yang pertama!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reviews as $row): ?>
                            <div class="bg-slate-900/50 border border-white/5 p-8 rounded-3xl hover:bg-slate-800/50 transition-all group">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 font-bold">
                                        <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-lg"><?= htmlspecialchars($row['username']) ?></h4>
                                        <p class="text-xs text-slate-500"><?= $row['created_at'] ?></p>
                                    </div>
                                </div>
                                <div class="text-slate-300 leading-relaxed text-sm">
                                    <!-- VULNERABLE POINT: Comment is displayed without escaping -->
                                    <?= $row['comment'] ?>
                                </div>
                                <div class="mt-6 flex items-center gap-4 text-xs text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="hover:text-rose-500 cursor-pointer flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a2 2 0 00-.8 2.4z"></path></svg> Membantu
                                    </span>
                                    <span class="hover:text-rose-500 cursor-pointer uppercase font-bold tracking-tighter">Laporkan</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="border-t border-white/5 bg-slate-900 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-slate-500 text-sm">&copy; 2026 CinemaX International. Dibangun untuk Simulasi Keamanan Cyber.</p>
        </div>
    </footer>
</body>
</html>
