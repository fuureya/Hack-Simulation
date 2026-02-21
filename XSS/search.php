<?php
$query = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian - CinemaX</title>
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
                <a href="index.php" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-tr from-rose-500 to-orange-400 rounded-xl flex items-center justify-center shadow-lg shadow-rose-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 00-1 1z"></path></svg>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">CinemaX</span>
                </a>
                <div class="flex items-center gap-4">
                     <form action="search.php" method="GET" class="relative group">
                        <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Cari film..." 
                            class="bg-white/5 border border-white/10 rounded-full py-2 px-5 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 transition-all w-64 text-white">
                        <svg class="w-4 h-4 text-slate-500 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h2 class="text-4xl font-bold mb-4">Hasil Pencarian</h2>
            <p class="text-slate-400">
                Menampilkan hasil untuk: 
                <span class="text-rose-500 font-bold italic">
                    <!-- VULNERABLE POINT: Reflected XSS -->
                    <?= $query ?>
                </span>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Mock Result -->
            <div class="text-center py-20 bg-slate-900 border border-white/5 rounded-[2rem] col-span-full">
                <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-2 text-slate-400">Maaf, film tidak ditemukan.</h3>
                <p class="text-slate-600 text-sm">Coba kata kunci lain atau periksa ejaan Anda.</p>
            </div>
        </div>

        <div class="mt-20 p-8 bg-rose-500/10 border border-rose-500/20 rounded-3xl">
            <h4 class="text-rose-400 font-bold flex items-center gap-2 mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Info Keamanan (Reflected XSS)
            </h4>
            <p class="text-xs text-slate-400 leading-relaxed italic">
                Parameter `q` pada halaman ini langsung dicetak ke layar tanpa proses enkoding atau sanitasi. 
                Seorang penyerang bisa mengirimkan link khusus kepada korban, misalnya: 
                <code class="text-rose-300">search.php?q=&lt;script&gt;alert(1)&lt;/script&gt;</code>
            </p>
        </div>
    </main>
</body>
</html>
