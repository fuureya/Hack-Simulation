<?php
session_start();

$users = [
    'user' => 'password123',
    'guest' => 'guest123'
];

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($users[$username]) && $users[$username] === $password) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = 'user';
        header('Location: index.php');
        exit;
    } else {
        $error = 'Kredensial yang Anda masukkan tidak valid.';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartRetail — Member Service Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f0f4f8;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
        }

        .card-hover:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
    </style>
</head>

<body class="min-h-screen">

    <?php if (!isset($_SESSION['user'])): ?>
        <!-- Login View -->
        <div class="min-h-screen flex items-center justify-center p-6">
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200 overflow-hidden max-w-[900px] w-full flex">
                <div class="hidden md:block w-1/2 hero-gradient p-12 text-white flex flex-col justify-between">
                    <div>
                        <div
                            class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mb-6 backdrop-blur-md">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h2 class="text-4xl font-extrabold tracking-tight mb-4">Kemudahan Belanja, <span
                                class="text-emerald-400">Keistimewaan Member.</span></h2>
                        <p class="text-slate-400 font-medium">Masuk untuk mengelola poin belanja, riwayat pesanan, dan promo
                            eksklusif SmartRetail.</p>
                    </div>
                    <div class="text-[10px] uppercase font-bold tracking-[0.3em] text-slate-500">Official SmartRetail Member
                        v2.0</div>
                </div>

                <div class="w-full md:w-1/2 p-12 lg:p-16">
                    <h1 class="text-3xl font-black text-slate-900 mb-2">Selamat Datang</h1>
                    <p class="text-slate-400 mb-8 font-medium">Silakan login dengan akun member Anda.</p>

                    <?php if ($error): ?>
                        <div
                            class="bg-rose-50 border border-rose-100 text-rose-600 px-4 py-3 rounded-2xl mb-6 text-xs font-bold flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-5">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Username
                                Member</label>
                            <input type="text" name="username"
                                class="w-full border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold focus:outline-none focus:border-emerald-500 transition-all bg-slate-50"
                                placeholder="Username member" required>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Kata
                                Sandi</label>
                            <input type="password" name="password"
                                class="w-full border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-semibold focus:outline-none focus:border-emerald-500 transition-all bg-slate-50"
                                placeholder="••••••••" required>
                        </div>
                        <button type="submit" name="login"
                            class="w-full bg-slate-900 hover:bg-black text-white font-bold py-5 rounded-2xl shadow-xl shadow-slate-200 transition-all active:scale-[0.98]">
                            Masuk Member
                        </button>
                    </form>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Member Dashboard View -->
        <nav class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-slate-900 tracking-tight">SmartRetail</span>
                </div>
                <div class="flex items-center gap-6">
                    <span class="text-sm font-semibold text-slate-600 hidden sm:block">Hai, <span
                            class="text-slate-900"><?= htmlspecialchars($_SESSION['user']) ?></span></span>
                    <a href="?logout=1"
                        class="text-xs font-black uppercase tracking-widest text-rose-500 hover:text-rose-600">Logout</a>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-6 py-12 lg:px-8">
            <header class="mb-12">
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Dashboard Member</h1>
                <p class="text-slate-500 font-medium">Kelola pengalaman belanja Anda di satu tempat.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Profile Card -->
                <a href="profile.php" class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 card-hover">
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-500 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 tracking-tight">Profil Member</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">Perbarui informasi data diri dan
                        preferensi belanja Anda secara berkala.</p>
                </a>

                <!-- Orders Card -->
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 cursor-not-allowed opacity-60">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 tracking-tight">Riwayat Pesanan</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">Lihat detail belanja dan status pengiriman
                        barang idaman Anda.</p>
                </div>

                <!-- Points Card -->
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 cursor-not-allowed opacity-60">
                    <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2 tracking-tight">Katalog Hadiah</h3>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed">Tukarkan poin SmartRetail Anda dengan
                        berbagai merchandise menarik.</p>
                </div>
            </div>

            <section class="mt-16 text-center">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4">Butuh Bantuan Lain?</p>
                <div class="flex justify-center gap-4">
                    <button
                        class="px-6 py-2 bg-slate-200 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-widest">Hubungi
                        Support</button>
                    <button
                        class="px-6 py-2 bg-slate-200 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-widest">FAQ</button>
                </div>
            </section>
        </main>

        <footer class="mt-20 py-10 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">&copy; 2026 SmartRetail
                    International. All Access Managed by DevTeam.</p>
            </div>
        </footer>
    <?php endif; ?>

</body>

</html>