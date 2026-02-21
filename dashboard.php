<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Lab configuration
$labs = [
    [
        'id' => 'csrf',
        'name' => 'CSRF Attack Lab',
        'description' => 'NeoBank - Cross-Site Request Forgery Simulation',
        'container' => 'neobank-csrf-container',
        'port' => '8888',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        'color' => 'blue',
        'owasp' => 'A08'
    ],
    [
        'id' => 'xss',
        'name' => 'XSS Attack Lab',
        'description' => 'CinemaX - Cross-Site Scripting Simulation',
        'container' => 'cinemax-xss-container',
        'port' => '8001',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>',
        'color' => 'purple',
        'owasp' => 'A03'
    ],
    [
        'id' => 'sqli',
        'name' => 'SQL Injection Lab',
        'description' => 'NeoHMS - SQL Injection Simulation',
        'container' => 'neohms-sqli-container',
        'port' => '8002',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>',
        'color' => 'emerald',
        'owasp' => 'A03'
    ],
    [
        'id' => 'bac',
        'name' => 'Broken Access Control',
        'description' => 'ShopAdmin - Access Control Simulation',
        'container' => 'shop-bac-container',
        'port' => '8009',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>',
        'color' => 'rose',
        'owasp' => 'A01'
    ],
    [
        'id' => 'idor',
        'name' => 'IDOR Attack Lab',
        'description' => 'SupportTicket - Insecure Direct Object Reference',
        'container' => 'ticket-idor-container',
        'port' => '8010',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>',
        'color' => 'indigo',
        'owasp' => 'A01'
    ],
    // === NEW OWASP Labs ===
    [
        'id' => 'cryptofail',
        'name' => 'Crypto Failures Lab',
        'description' => 'SafeVault Bank — MD5 tanpa salt, cookie sensitif plaintext, data di URL',
        'container' => 'safevault-cryptofail-container',
        'port' => '8003',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>',
        'color' => 'yellow',
        'owasp' => 'A02'
    ],
    [
        'id' => 'insecuredesign',
        'name' => 'Insecure Design Lab',
        'description' => 'QuickLoan App — Brute force, business logic bypass, security question lemah',
        'container' => 'quickloan-insecuredesign-container',
        'port' => '8004',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
        'color' => 'green',
        'owasp' => 'A04'
    ],
    [
        'id' => 'secmisconfig',
        'name' => 'Security Misconfig Lab',
        'description' => 'DevOps Portal — Default creds, directory listing, .env publik, phpinfo()',
        'container' => 'devops-secmisconfig-container',
        'port' => '8005',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>',
        'color' => 'gray',
        'owasp' => 'A05'
    ],
    [
        'id' => 'authfail',
        'name' => 'Auth Failures Lab',
        'description' => 'SecureLogin Corp — Session fixation, no lockout, predictable remember-me token',
        'container' => 'securelogin-authfail-container',
        'port' => '8006',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>',
        'color' => 'violet',
        'owasp' => 'A07'
    ],
    [
        'id' => 'loggingfail',
        'name' => 'Logging Failures Lab',
        'description' => 'AuditTrail System — Login gagal tidak di-log, log publik, log injection',
        'container' => 'audittrail-loggingfail-container',
        'port' => '8007',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>',
        'color' => 'teal',
        'owasp' => 'A09'
    ],
    [
        'id' => 'ssrf',
        'name' => 'SSRF Lab',
        'description' => 'WebFetcher Pro — Akses internal service, cloud metadata, file:// read',
        'container' => 'webfetcher-ssrf-container',
        'port' => '8008',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 019-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>',
        'color' => 'pink',
        'owasp' => 'A10'
    ],
    [
        'id' => 'insecurelibrary',
        'name' => 'Insecure Component Lab',
        'description' => 'Legacy News CMS — Menggunakan library lama dengan kerentanan PHP Object Injection',
        'container' => 'insecurelibrary-legacycms-container',
        'port' => '8011',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>',
        'color' => 'orange',
        'owasp' => 'A06'
    ],
    [
        'id' => 'integrityfail',
        'name' => 'Integrity Failure Lab',
        'description' => 'ObjectRelay Service — Insecure Deserialization pada session cookie tanpa signature',
        'container' => 'integrityfail-objectrelay-container',
        'port' => '8012',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>',
        'color' => 'cyan',
        'owasp' => 'A08'
    ],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabSec Manager - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .loader {
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .glass-btn {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">
    <!-- Header -->
    <header
        class="bg-gradient-to-r from-indigo-700 via-purple-700 to-pink-700 text-white p-6 shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:20px_20px]"></div>
        <div class="max-w-7xl mx-auto flex justify-between items-center relative z-10">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-xl border border-white/20 shadow-inner">
                    <svg class="w-8 h-8 text-white drop-shadow-md" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight drop-shadow-sm">LabSec <span
                            class="text-pink-300">Manager</span></h1>
                    <p class="text-xs font-bold text-white/60 uppercase tracking-widest px-1">Infrastructure Control
                        Dashboard</p>
                </div>
            </div>
            <a href="logout.php"
                class="glass-btn px-8 py-3 rounded-2xl font-black text-sm hover:bg-white/20 transition-all shadow-lg flex items-center gap-2">
                <span>Keluar</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto p-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-900 mb-3 tracking-tight">Security Simulation Labs</h2>
                <p class="text-slate-500 font-medium max-w-2xl text-lg">Deploy and manage vulnerable environments for
                    educational purposes. <span class="text-indigo-600 font-bold">Use responsibly.</span></p>
            </div>
            <div class="bg-white px-6 py-4 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-black tracking-widest text-slate-400">Environment
                        Status</label>
                    <p class="text-slate-900 font-black">All Networks Stable</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($labs as $lab): ?>
                <div
                    class="bg-white rounded-[2.5rem] p-1 shadow-xl border border-slate-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
                    <div class="p-8">
                        <div class="flex items-start justify-between mb-8">
                            <div
                                class="w-16 h-16 bg-<?= $lab['color'] ?>-50 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-8 h-8 text-<?= $lab['color'] ?>-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <?= $lab['icon'] ?>
                                </svg>
                            </div>
                            <div id="status-<?= $lab['id'] ?>"
                                class="bg-slate-50 px-4 py-2 rounded-2xl flex items-center gap-3 border border-slate-100 min-w-[120px]">
                                <div class="w-2 h-2 bg-slate-300 rounded-full"></div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400">Loading...</span>
                            </div>
                        </div>

                        <h3 class="text-2xl font-black text-slate-900 mb-3 tracking-tight"><?= $lab['name'] ?></h3>
                        <p class="text-sm font-medium text-slate-500 mb-8 leading-relaxed h-12 overflow-hidden">
                            <?= $lab['description'] ?></p>

                        <div class="flex gap-4">
                            <button onclick="startLab('<?= $lab['id'] ?>')" id="start-<?= $lab['id'] ?>"
                                class="flex-1 bg-<?= $lab['color'] ?>-600 text-white font-black py-4 rounded-2xl text-sm hover:bg-<?= $lab['color'] ?>-700 transition-all shadow-lg shadow-<?= $lab['color'] ?>-200 flex items-center justify-center gap-2">
                                <div id="start-inner-<?= $lab['id'] ?>" class="flex items-center gap-2">
                                    <span>Start Lab</span>
                                </div>
                            </button>
                            <button onclick="stopLab('<?= $lab['id'] ?>')" id="stop-<?= $lab['id'] ?>"
                                class="flex-1 bg-slate-100 text-slate-600 font-black py-4 rounded-2xl text-sm hover:bg-slate-200 transition-all flex items-center justify-center gap-2">
                                <span id="stop-inner-<?= $lab['id'] ?>">Stop</span>
                            </button>
                        </div>

                        <a href="http://localhost:<?= $lab['port'] ?>" target="_blank" id="link-<?= $lab['id'] ?>"
                            class="hidden mt-4 text-center bg-indigo-50 text-indigo-700 font-black py-4 rounded-2xl text-sm border-2 border-indigo-100 hover:bg-indigo-100 hover:border-indigo-200 transition-all flex items-center justify-center gap-3">
                            <span>Buka Simulation</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        const labs = <?= json_encode($labs) ?>;
        const processing = new Set();

        async function checkStatus() {
            for (const lab of labs) {
                if (processing.has(lab.id)) continue;

                try {
                    const response = await fetch(`api.php?action=status&container=${lab.container}`);
                    const data = await response.json();
                    updateUI(lab.id, data.status);
                } catch (error) {
                    console.error(`Error checking ${lab.id}:`, error);
                }
            }
        }

        function updateUI(labId, status) {
            const statusEl = document.getElementById(`status-${labId}`);
            const startBtn = document.getElementById(`start-${labId}`);
            const startInner = document.getElementById(`start-inner-${labId}`);
            const stopBtn = document.getElementById(`stop-${labId}`);
            const link = document.getElementById(`link-${labId}`);

            if (status === 'running') {
                statusEl.innerHTML = '<div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div><span class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Running</span>';
                startBtn.classList.add('opacity-50', 'pointer-events-none');
                startInner.innerHTML = '<span>Active</span>';
                stopBtn.classList.remove('opacity-50', 'pointer-events-none');
                link.classList.remove('hidden');
            } else if (status === 'processing') {
                statusEl.innerHTML = '<div class="loader"></div><span class="text-[10px] font-black uppercase tracking-widest text-blue-600">Building...</span>';
                startBtn.classList.add('opacity-50', 'pointer-events-none');
                stopBtn.classList.add('opacity-50', 'pointer-events-none');
                link.classList.add('hidden');
            } else if (status === 'stopping') {
                statusEl.innerHTML = '<div class="loader"></div><span class="text-[10px] font-black uppercase tracking-widest text-rose-600">Stopping...</span>';
                startBtn.classList.add('opacity-50', 'pointer-events-none');
                stopBtn.classList.add('opacity-50', 'pointer-events-none');
                link.classList.add('hidden');
            } else {
                statusEl.innerHTML = '<div class="w-2 h-2 bg-slate-300 rounded-full"></div><span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Stopped</span>';
                startBtn.classList.remove('opacity-50', 'pointer-events-none');
                startInner.innerHTML = '<span>Start Lab</span>';
                stopBtn.classList.add('opacity-50', 'pointer-events-none');
                link.classList.add('hidden');
            }
        }

        async function startLab(labId) {
            const lab = labs.find(l => l.id === labId);
            processing.add(labId);
            updateUI(labId, 'processing');

            Swal.fire({
                title: 'Deploying Environment',
                html: `Building and starting <b>${lab.name}</b>...<br><span class="text-xs text-slate-500">Menyiapkan kontainer dan jaringan.</span>`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(`api.php?action=start&container=${lab.container}`);
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Lab Deployed!',
                        text: 'Security environment successfully initialized.',
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#fff',
                        customClass: {
                            popup: 'rounded-3xl border-0 shadow-2xl'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Deployment Failed',
                        text: data.message || 'Build or run error occurred.',
                        background: '#fff',
                        customClass: {
                            popup: 'rounded-3xl border-0 shadow-2xl'
                        }
                    });
                }
            } catch (error) {
                Swal.fire('Error', 'Sistem gagal menghubungi API.', 'error');
            } finally {
                processing.delete(labId);
                checkStatus();
            }
        }

        async function stopLab(labId) {
            const lab = labs.find(l => l.id === labId);
            processing.add(labId);
            updateUI(labId, 'stopping');

            Swal.fire({
                title: 'Terminating Lab',
                html: `Stopping <b>${lab.name}</b>...<br><span class="text-xs text-slate-500">Membersihkan resource kontainer.</span>`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(`api.php?action=stop&container=${lab.container}`);
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Lab Offline',
                        text: 'Security simulation has been stopped.',
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#fff',
                        customClass: {
                            popup: 'rounded-3xl border-0 shadow-2xl'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Termination Failed',
                        text: 'Gagal menghentikan lab.',
                        background: '#fff',
                        customClass: {
                            popup: 'rounded-3xl border-0 shadow-2xl'
                        }
                    });
                }
            } catch (error) {
                Swal.fire('Error', 'Koneksi API bermasalah.', 'error');
            } finally {
                processing.delete(labId);
                checkStatus();
            }
        }

        checkStatus();
        setInterval(checkStatus, 5000);
    </script>
</body>

</html>
</body>

</html>