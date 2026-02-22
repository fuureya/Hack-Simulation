<?php
require_once 'legacy_lib.php';

$message = '';
// Memproses input yang mengandung data serialisasi (VULN A06 & A08)
if (isset($_POST['data'])) {
    // VULN: Deserialisasi input user tanpa validasi!
    try {
        @unserialize($_POST['data']);
        $message = "Pipeline configuration updated successfully.";
    } catch (Exception $e) {
        $message = "Error: Invalid configuration object.";
    }
}

$logs = [
    ['service' => 'identity-srv-01', 'status' => 'Healthy', 'last_sync' => '2s ago', 'region' => 'us-east-1'],
    ['service' => 'payment-gateway', 'status' => 'Monitoring', 'last_sync' => '15s ago', 'region' => 'eu-west-1'],
    ['service' => 'legacy-auth-v2', 'status' => 'Healthy', 'last_sync' => '1m ago', 'region' => 'ap-southeast-1'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | CloudLog Dynamics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #020617;
        }

        .glass {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(51, 65, 85, 0.5);
        }

        .glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>

<body class="text-slate-300 min-h-screen flex flex-col">

    <!-- Top Navigation -->
    <nav class="border-b border-slate-800 bg-slate-950/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-black text-xl">
                    L</div>
                <span class="text-lg font-bold text-white tracking-tight leading-tight">CloudLog<span
                        class="text-blue-500">Dynamics</span></span>
            </div>
            <div class="flex items-center gap-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                <a href="#" class="hover:text-white transition-colors">Monitoring</a>
                <a href="#" class="text-white">Pipelines</a>
                <a href="#" class="hover:text-white transition-colors">Alerts</a>
                <div class="w-px h-4 bg-slate-800"></div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-white">Node: Global-Relay</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto w-full px-6 py-12 flex-1">
        <header class="mb-12">
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Ingestion Pipelines</h1>
            <p class="text-slate-500 font-medium">Manage and monitor your enterprise data flow across global regions.
            </p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Active Services -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass rounded-3xl p-8 glow">
                    <h2 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6">Active Infrastructure
                        Nodes</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <?php foreach ($logs as $log): ?>
                            <div
                                class="bg-slate-900/50 p-5 rounded-2xl border border-slate-800 hover:border-blue-500/30 transition-all group">
                                <div class="flex justify-between items-start mb-4">
                                    <div
                                        class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-[9px] font-black text-emerald-500 uppercase tracking-widest bg-emerald-500/5 px-2 py-1 rounded">Online</span>
                                </div>
                                <p class="text-sm font-bold text-white mb-1"><?= htmlspecialchars($log['service']) ?></p>
                                <div
                                    class="flex justify-between items-center text-[10px] font-bold text-slate-600 uppercase">
                                    <span><?= $log['region'] ?></span>
                                    <span><?= $log['last_sync'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-slate-900/30 rounded-3xl border border-slate-800 p-8">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4">Pipeline Health Status
                    </h3>
                    <div class="h-40 flex items-end gap-2 group">
                        <?php for ($i = 0; $i < 24; $i++): ?>
                            <div class="flex-1 bg-blue-500/20 rounded-t-sm hover:bg-blue-500 transition-colors"
                                style="height: <?= rand(40, 100) ?>%"></div>
                        <?php endfor; ?>
                    </div>
                    <div
                        class="mt-4 flex justify-between text-[9px] font-black text-slate-700 uppercase tracking-widest">
                        <span>Yesterday</span>
                        <span>00:00 UTC</span>
                        <span>Live Now</span>
                    </div>
                </div>
            </div>

            <!-- Configuration Side -->
            <div class="lg:col-span-1">
                <div class="glass rounded-3xl p-8 glow sticky top-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-white tracking-tight leading-tight">Sync Configuration</h2>
                    </div>

                    <p class="text-xs text-slate-500 font-medium mb-8 leading-relaxed">Import serialized configuration
                        objects to synchronize legacy data pipelines with the dynamical node cluster.</p>

                    <?php if ($message): ?>
                        <div
                            class="mb-6 p-4 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <?= $message ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Object
                                Content (PHP Serialized)</label>
                            <textarea name="data" required
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-xs font-mono text-blue-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none h-40"
                                placeholder='Enter configuration object...'></textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-500 py-4 rounded-xl text-white font-black text-[10px] uppercase tracking-[0.2em] shadow-2xl shadow-blue-500/20 transition-all active:scale-[0.98]">
                            Apply Pipeline Sync
                        </button>
                    </form>

                    <div class="mt-8 pt-8 border-t border-slate-800/50">
                        <div
                            class="flex items-center justify-between text-[10px] font-bold text-slate-600 uppercase tracking-widest">
                            <span>Legacy Engine</span>
                            <span class="text-slate-400">v1.2.4-STABLE</span>
                        </div>
                        <p class="mt-4 text-[9px] text-slate-700 leading-relaxed italic">Notice: Serialized objects must
                            adhere to the LegacyMailer schema for successful log relaying.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="border-t border-slate-800 py-10 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
            <p
                class="text-[10px] font-bold text-slate-700 uppercase tracking-widest text-center md:text-left leading-relaxed">
                © 2026 CloudLog Dynamics Global Services.<br>
                Protected by Enterprise Data Integrity Protocols.
            </p>
            <div class="flex items-center gap-8 text-[10px] font-black uppercase tracking-widest text-slate-700">
                <a href="#" class="hover:text-slate-500">Architecture</a>
                <a href="#" class="hover:text-slate-500">Compliance</a>
                <a href="#" class="hover:text-slate-500">Support</a>
            </div>
        </div>
    </footer>

</body>

</html>