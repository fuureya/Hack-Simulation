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
        'color' => 'blue'
    ],
    [
        'id' => 'xss',
        'name' => 'XSS Attack Lab',
        'description' => 'CinemaX - Cross-Site Scripting Simulation',
        'container' => 'cinemax-xss-container',
        'port' => '8001',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"></path>',
        'color' => 'purple'
    ],
    [
        'id' => 'sqli',
        'name' => 'SQL Injection Lab',
        'description' => 'NeoHMS - SQL Injection Simulation',
        'container' => 'neohms-sqli-container',
        'port' => '8002',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>',
        'color' => 'emerald'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabSec Manager - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6 shadow-xl">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight">LabSec Manager</h1>
                    <p class="text-sm text-white/80">Security Lab Control Panel</p>
                </div>
            </div>
            <a href="logout.php" class="bg-white/10 px-6 py-3 rounded-2xl font-bold text-sm hover:bg-white/20 transition-all backdrop-blur-md border border-white/20">
                Logout
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto p-8">
        <div class="mb-8">
            <h2 class="text-3xl font-black text-slate-900 mb-2">Available Labs</h2>
            <p class="text-slate-500">Manage and access your security simulation environments</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($labs as $lab): ?>
                <div class="bg-white rounded-[2rem] p-8 shadow-xl border border-slate-100 hover:shadow-2xl transition-all">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 bg-<?= $lab['color'] ?>-50 rounded-2xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-<?= $lab['color'] ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <?= $lab['icon'] ?>
                            </svg>
                        </div>
                        <div id="status-<?= $lab['id'] ?>" class="flex items-center gap-2 text-xs font-bold">
                            <div class="w-2 h-2 bg-slate-300 rounded-full"></div>
                            <span class="text-slate-400">Checking...</span>
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-slate-900 mb-2"><?= $lab['name'] ?></h3>
                    <p class="text-sm text-slate-500 mb-6"><?= $lab['description'] ?></p>

                    <div class="flex gap-3">
                        <button onclick="startLab('<?= $lab['id'] ?>')" id="start-<?= $lab['id'] ?>" 
                            class="flex-1 bg-<?= $lab['color'] ?>-600 text-white font-bold py-3 rounded-2xl text-sm hover:bg-<?= $lab['color'] ?>-700 transition-all">
                            Start
                        </button>
                        <button onclick="stopLab('<?= $lab['id'] ?>')" id="stop-<?= $lab['id'] ?>" 
                            class="flex-1 bg-slate-200 text-slate-700 font-bold py-3 rounded-2xl text-sm hover:bg-slate-300 transition-all">
                            Stop
                        </button>
                    </div>

                    <a href="http://localhost:<?= $lab['port'] ?>" target="_blank" id="link-<?= $lab['id'] ?>" 
                        class="hidden mt-4 block text-center bg-slate-50 text-<?= $lab['color'] ?>-600 font-bold py-3 rounded-2xl text-sm border-2 border-<?= $lab['color'] ?>-200 hover:bg-<?= $lab['color'] ?>-50 transition-all">
                        Open Lab →
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        const labs = <?= json_encode($labs) ?>;

        async function checkStatus() {
            for (const lab of labs) {
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
            const stopBtn = document.getElementById(`stop-${labId}`);
            const link = document.getElementById(`link-${labId}`);

            if (status === 'running') {
                statusEl.innerHTML = '<div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div><span class="text-emerald-600">Running</span>';
                startBtn.classList.add('opacity-50', 'cursor-not-allowed');
                stopBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                link.classList.remove('hidden');
            } else {
                statusEl.innerHTML = '<div class="w-2 h-2 bg-slate-300 rounded-full"></div><span class="text-slate-400">Stopped</span>';
                startBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                stopBtn.classList.add('opacity-50', 'cursor-not-allowed');
                link.classList.add('hidden');
            }
        }

        async function startLab(labId) {
            const lab = labs.find(l => l.id === labId);
            console.log('Starting lab:', labId, lab);
            try {
                const url = `api.php?action=start&container=${lab.container}`;
                console.log('Fetching:', url);
                const response = await fetch(url);
                const data = await response.json();
                console.log('Start response:', data);
                if (data.success) {
                    alert('Lab started successfully! Waiting for container...');
                    setTimeout(() => checkStatus(), 3000);
                } else {
                    alert('Failed to start lab: ' + (data.message || 'Unknown error'));
                    console.error('Start failed:', data);
                }
            } catch (error) {
                console.error('Error starting lab:', error);
                alert('Failed to start lab: ' + error.message);
            }
        }

        async function stopLab(labId) {
            const lab = labs.find(l => l.id === labId);
            console.log('Stopping lab:', labId, lab);
            try {
                const url = `api.php?action=stop&container=${lab.container}`;
                console.log('Fetching:', url);
                const response = await fetch(url);
                const data = await response.json();
                console.log('Stop response:', data);
                if (data.success) {
                    alert('Lab stopped successfully!');
                    setTimeout(() => checkStatus(), 1000);
                } else {
                    alert('Failed to stop lab: ' + (data.message || 'Unknown error'));
                    console.error('Stop failed:', data);
                }
            } catch (error) {
                console.error('Error stopping lab:', error);
                alert('Failed to stop lab: ' + error.message);
            }
        }

        // Check status on load and every 5 seconds
        checkStatus();
        setInterval(checkStatus, 5000);
    </script>
</body>
</html>
