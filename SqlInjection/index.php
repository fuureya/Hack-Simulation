<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoHMS - Hospital Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-slate-800">NeoHMS</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="index.php" class="text-sm font-semibold text-blue-600">Terbaru</a>
                    <a href="schedule.php" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Jadwal</a>
                    <a href="login.php" class="bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-md hover:bg-slate-800 transition-all">Login Staff</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <!-- Hero -->
        <div class="bg-white rounded-3xl p-8 lg:p-12 shadow-xl border border-slate-100 flex flex-col lg:flex-row items-center gap-12 mb-12 overflow-hidden relative">
            <div class="flex-1 z-10">
                <h1 class="text-5xl font-extrabold text-slate-900 leading-tight mb-6">Sistem Informasi <br><span class="text-blue-600 underline decoration-blue-200 underline-offset-8">Rumah Sakit Terintegrasi.</span></h1>
                <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-lg">Selamat datang di sistem manajemen NeoHMS. Kelola data pasien, rekam medis, dan administrasi dengan efisien dalam satu platform.</p>
                <div class="flex flex-wrap gap-4">
                     <form action="search.php" method="GET" class="relative group">
                        <input type="text" name="name" placeholder="Cari nama pasien..." 
                            class="bg-slate-100 border-none rounded-2xl py-4 px-6 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all w-80 shadow-inner">
                        <button type="submit" class="absolute right-4 top-3.5 text-slate-400 group-focus-within:text-blue-600">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            <div class="flex-1 relative">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-100 rounded-full blur-3xl opacity-50"></div>
                <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=1000" alt="Hospital" class="rounded-[2.5rem] shadow-2xl relative z-10 border-4 border-white">
            </div>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-lg hover:shadow-xl transition-all group">
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Detail Pasien</h3>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Akses informasi lengkap mengenai data pribadi dan riwayat pasien terdaftar.</p>
                <a href="patient.php?id=1" class="text-blue-600 font-bold text-sm flex items-center gap-2 hover:gap-3 transition-all">
                    Lihat Pasien <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-lg hover:shadow-xl transition-all group">
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Jadwal Operasi</h3>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Informasi terkini mengenai jadwal dokter bedah dan ruang operasi rumah sakit.</p>
                <a href="schedule.php" class="text-emerald-600 font-bold text-sm flex items-center gap-2 hover:gap-3 transition-all">
                    Cek Jadwal <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-lg hover:shadow-xl transition-all group">
                <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-600 mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3">Sistem Billing</h3>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">Kelola faktur pengobatan dan status pembayaran pasien secara real-time.</p>
                <a href="billing.php?invoice_id=1" class="text-rose-600 font-bold text-sm flex items-center gap-2 hover:gap-3 transition-all">
                    Lihat Tagihan <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-slate-900 py-12 mt-20 text-center">
        <p class="text-slate-500 text-sm">&copy; 2026 NeoHMS Cybersecurity Lab. Dibuat hanya untuk Tujuan Edukasi.</p>
    </footer>
</body>
</html>
