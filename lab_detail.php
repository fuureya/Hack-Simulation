<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$lab_id = $_GET['id'] ?? '';
if (!$lab_id) {
    header('Location: dashboard.php');
    exit;
}

// Find lab config from dashboard data (simulated for now)
$labs = [
    'csrf' => ['name' => 'CSRF Attack Lab', 'OWASP' => 'A08', 'port' => 8888, 'color' => 'blue'],
    'xss' => ['name' => 'XSS Attack Lab', 'OWASP' => 'A03', 'port' => 8001, 'color' => 'purple'],
    'sqli' => ['name' => 'SQL Injection Lab', 'OWASP' => 'A03', 'port' => 8002, 'color' => 'emerald'],
    'authfail' => ['name' => 'Auth Failures Lab', 'OWASP' => 'A07', 'port' => 8006, 'color' => 'violet'],
    // ... add more as needed
];

$lab = $labs[$lab_id] ?? ['name' => 'Unknown Lab', 'OWASP' => 'N/A', 'port' => 0, 'color' => 'gray'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lab['name'] ?> - CTF Challenge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .challenge-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .challenge-card:hover { transform: translateY(-4px); }
    </style>
</head>
<body class="min-h-screen">
    <header class="bg-gradient-to-r from-indigo-700 via-purple-700 to-pink-700 text-white p-8 shadow-2xl">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="dashboard.php" class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-xl border border-white/20 hover:bg-white/20 transition-all">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-black tracking-tight"><?= $lab['name'] ?> <span class="text-pink-300">CTF</span></h1>
                    <p class="text-xs font-bold text-white/60 uppercase tracking-widest"><?= $lab['OWASP'] ?> Simulation Environment</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-white/40 mb-1">Status</div>
                <div class="flex items-center gap-2 bg-emerald-500/10 px-4 py-2 rounded-full border border-emerald-500/20 text-emerald-300 font-bold text-xs">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    Target System Active
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Left: Challenges -->
            <div class="lg:col-span-2 space-y-8">
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Available Challenges</h2>
                
                <?php foreach (['easy', 'medium', 'hard'] as $level): ?>
                <div class="challenge-card bg-white rounded-[2.5rem] p-8 shadow-xl border border-slate-100 flex items-start gap-8">
                    <div class="w-16 h-16 rounded-2xl bg-<?= $level === 'easy' ? 'emerald' : ($level === 'medium' ? 'orange' : 'rose') ?>-50 flex items-center justify-center shrink-0">
                        <svg class="w-8 h-8 text-<?= $level === 'easy' ? 'emerald' : ($level === 'medium' ? 'orange' : 'rose') ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight"><?= ucfirst($level) ?> Challenge</h3>
                            <span id="badge-<?= $level ?>" class="hidden px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest">Completed</span>
                        </div>
                        <p id="desc-<?= $level ?>" class="text-slate-500 font-medium text-sm leading-relaxed mb-6">Loading challenge description...</p>
                        
                        <div class="flex items-center gap-4">
                            <input type="text" id="flag-<?= $level ?>" class="flex-1 bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all" placeholder="FLAG{...}">
                            <button onclick="submitFlag('<?= $level ?>')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-4 rounded-2xl text-sm transition-all shadow-lg shadow-indigo-200 active:scale-95">Submit</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Right: Info -->
            <div class="space-y-8">
                <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/20 rounded-full blur-3xl -mr-16 -mt-16"></div>
                    <h2 class="text-xl font-black mb-6 relative z-10">Challenge Guide</h2>
                    <ul class="space-y-4 text-slate-400 text-sm font-medium relative z-10">
                        <li class="flex items-start gap-4">
                            <div class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 text-xs">1</div>
                            <span>Start lab melalui dashboard utama sebelum mencoba mengeksploitasi.</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 text-xs">2</div>
                            <span>Analisis perilaku aplikasi dan temukan celah keamanan sesuai skenario.</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 text-xs">3</div>
                            <span>Ekstrak flag yang biasanya berbentuk format <b>FLAG{...}</b>.</span>
                        </li>
                    </ul>
                    <a href="http://localhost:<?= $lab['port'] ?>" target="_blank" class="block mt-10 text-center bg-white text-slate-900 font-black py-4 rounded-2xl text-sm hover:bg-slate-100 transition-all flex items-center justify-center gap-3">
                        Launch Lab Environment
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        const labId = '<?= $lab_id ?>';
        
        async function loadChallenges() {
            // Simulated fetch for challenge descriptions (can be from API later)
            const challenges = {
                'authfail': {
                    'easy': 'Session Fixation: Gunakan parameter PHPSESSID di URL untuk membajak sesi user lainnya.',
                    'medium': 'Remember Me bypass: Manipulasi cookie "remember_me" yang menggunakan encoding lemah.',
                    'hard': 'Admin Login: Lakukan brute-force atau gunakan kredensial default untuk masuk sebagai admin.'
                },
                'sqli': {
                    'easy': 'Auth Bypass: Masuk ke sistem tanpa password menggunakan payload SQLi klasik.',
                    'medium': 'Union Select: Keluarkan semua data dari tabel sensitif (misal: billing atau users).',
                    'hard': 'Blind SQLi: Tebak versi database atau nama tabel menggunakan teknik time-based.'
                },
                'xss': {
                    'easy': 'Reflected XSS: Jalankan script alert() melalui kolom pencarian atau input non-persistent.',
                    'medium': 'Stored XSS: Masukkan script ke database (misal: review film) yang akan tereksekusi di sisi admin.',
                    'hard': 'Cookie Stealing: Gunakan XSS untuk mengambil session cookie admin dan kirimkan ke server penyerang.'
                }
            };

            const data = challenges[labId] || { 'easy': 'Description not available.', 'medium': 'Description not available.', 'hard': 'Description not available.' };
            
            for (const level of ['easy', 'medium', 'hard']) {
                document.getElementById(`desc-${level}`).innerText = data[level];
            }
            
            checkStatus();
        }

        async function checkStatus() {
            try {
                const response = await fetch(`api_ctf.php?action=status&lab_id=${labId}`);
                const result = await response.json();
                if (result.success) {
                    result.data.forEach(item => {
                        if (item.completed) {
                            document.getElementById(`badge-${item.level}`).classList.remove('hidden');
                        }
                    });
                }
            } catch (error) {
                console.error('Failed to load status:', error);
            }
        }

        async function submitFlag(level) {
            const flag = document.getElementById(`flag-${level}`).value;
            if (!flag) return Swal.fire('Oops!', 'Masukkan flag terlebih dahulu.', 'warning');

            const formData = new FormData();
            formData.append('lab_id', labId);
            formData.append('level', level);
            formData.append('flag', flag);

            try {
                const response = await fetch('api_ctf.php?action=submit', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Correct!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-3xl' }
                    });
                    document.getElementById(`badge-${level}`).classList.remove('hidden');
                    document.getElementById(`flag-${level}`).value = '';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Wrong Flag',
                        text: result.message,
                        customClass: { popup: 'rounded-3xl' }
                    });
                }
            } catch (error) {
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            }
        }

        loadChallenges();
    </script>
</body>
</html>
