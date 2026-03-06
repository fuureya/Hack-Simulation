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

// Find lab config from dashboard data
$labs = [
    'csrf' => ['name' => 'CSRF Attack Lab', 'OWASP' => 'A08', 'port' => 8888, 'color' => 'blue'],
    'xss' => ['name' => 'XSS Attack Lab', 'OWASP' => 'A03', 'port' => 8001, 'color' => 'purple'],
    'sqli' => ['name' => 'SQL Injection Lab', 'OWASP' => 'A03', 'port' => 8002, 'color' => 'emerald'],
    'authfail' => ['name' => 'Auth Failures Lab', 'OWASP' => 'A07', 'port' => 8006, 'color' => 'violet'],
    'bac' => ['name' => 'Broken Access Control', 'OWASP' => 'A01', 'port' => 8009, 'color' => 'rose'],
    'idor' => ['name' => 'IDOR Attack Lab', 'OWASP' => 'A01', 'port' => 8010, 'color' => 'indigo'],
    'cryptofail' => ['name' => 'Crypto Failures Lab', 'OWASP' => 'A02', 'port' => 8003, 'color' => 'yellow'],
    'insecuredesign' => ['name' => 'Insecure Design Lab', 'OWASP' => 'A04', 'port' => 8004, 'color' => 'green'],
    'secmisconfig' => ['name' => 'Security Misconfig Lab', 'OWASP' => 'A05', 'port' => 8005, 'color' => 'gray'],
    'loggingfail' => ['name' => 'Logging Failures Lab', 'OWASP' => 'A09', 'port' => 8007, 'color' => 'teal'],
    'ssrf' => ['name' => 'SSRF Lab', 'OWASP' => 'A10', 'port' => 8008, 'color' => 'pink'],
    'insecurelibrary' => ['name' => 'Insecure Component Lab', 'OWASP' => 'A06', 'port' => 8011, 'color' => 'orange'],
    'integrityfail' => ['name' => 'Integrity Failure Lab', 'OWASP' => 'A08', 'port' => 8012, 'color' => 'cyan'],
];

$lab = $labs[$lab_id] ?? ['name' => 'Unknown Lab', 'OWASP' => 'N/A', 'port' => 0, 'color' => 'gray'];

// Fetch challenges for this lab directly from DB
$host = 'labsec-db';
$db_name   = 'labsec_ctf';
$user = 'root';
$pass = 'labsec_root_2026';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $user, $pass);
    $stmt = $pdo->prepare("SELECT level, title, description, hint FROM challenges WHERE lab_id = ?");
    $stmt->execute([$lab_id]);
    $challenges_db = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
} catch (PDOException $e) {
    $challenges_db = [];
}
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
                
                <?php foreach (['easy', 'medium', 'hard'] as $level): 
                    $c = $challenges_db[$level] ?? ['title' => 'N/A', 'description' => 'Challenge not loaded.', 'hint' => 'No hint.'];
                ?>
                <div class="challenge-card bg-white rounded-[2.5rem] p-8 shadow-xl border border-slate-100 flex items-start gap-8">
                    <div class="w-16 h-16 rounded-2xl bg-<?= $level === 'easy' ? 'emerald' : ($level === 'medium' ? 'orange' : 'rose') ?>-50 flex items-center justify-center shrink-0">
                        <svg class="w-8 h-8 text-<?= $level === 'easy' ? 'emerald' : ($level === 'medium' ? 'orange' : 'rose') ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight"><?= ucfirst($level) ?></h3>
                                <div class="h-1 w-1 bg-slate-300 rounded-full"></div>
                                <span class="text-sm font-bold text-slate-400"><?= htmlspecialchars($c['title']) ?></span>
                            </div>
                            <span id="badge-<?= $level ?>" class="hidden px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest">Completed</span>
                        </div>
                        <p class="text-slate-500 font-medium text-sm leading-relaxed mb-6"><?= htmlspecialchars($c['description']) ?></p>
                        
                        <div class="mb-6 hidden" id="hint-box-<?= $level ?>">
                            <div class="p-4 bg-orange-50 border border-orange-100 rounded-2xl text-xs font-bold text-orange-700 flex items-start gap-3">
                                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Hint: <?= htmlspecialchars($c['hint']) ?></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <input type="text" id="flag-<?= $level ?>" class="flex-1 bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all" placeholder="FLAG{...}">
                            <button onclick="showHint('<?= $level ?>')" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold px-6 py-4 rounded-2xl text-sm transition-all">Hint</button>
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

        function showHint(level) {
            const hintBox = document.getElementById(`hint-box-${level}`);
            hintBox.classList.toggle('hidden');
        }

        async function submitFlag(level) {
            const flagInput = document.getElementById(`flag-${level}`);
            const flag = flagInput.value.trim();
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
                        customClass: { popup: 'rounded-[2rem]' }
                    });
                    document.getElementById(`badge-${level}`).classList.remove('hidden');
                    flagInput.value = '';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Salah!',
                        text: result.message,
                        customClass: { popup: 'rounded-[2rem]' }
                    });
                }
            } catch (error) {
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            }
        }

        checkStatus();
    </script>
</body>
</html>
