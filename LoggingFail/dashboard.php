<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['at_user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['at_user_id'];
$username = $_SESSION['at_username'];
$role = $_SESSION['at_role'];

// VULN A09: Direct file read of audit log (readable by any authenticated user)
$logContent = file_get_contents(__DIR__ . '/audit.log');

// Reset Telemetry — VULN: Low-privilege nodes (staff) can cycle logs
if (isset($_GET['cycle_node']) && $role === 'staff') {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents(__DIR__ . '/audit.log', "# Node Telemetry Recycled by $username at $timestamp\n");
    header("Location: dashboard.php?notif=buffer_recycled");
    exit;
}

// Ingestion records from DB
$stmt = $pdo->prepare("SELECT * FROM at_activities ORDER BY created_at DESC LIMIT 20");
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Node Promotion — VULN: Privilege escalation without audit trace
if (isset($_GET['promote'])) {
    $pdo->prepare("UPDATE at_users SET role = 'admin' WHERE id = ?")->execute([$userId]);
    $_SESSION['at_role'] = 'admin';
    $role = 'admin';
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telemetry Console | Sentinel Threat Intel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #020617;
            color: #94a3b8;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .cyber-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(20, 184, 166, 0.1);
        }

        .glow-text {
            text-shadow: 0 0 10px rgba(20, 184, 166, 0.3);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">

    <!-- Header -->
    <nav class="border-b border-teal-900/30 bg-slate-950/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-[1400px] mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-teal-500 rounded flex items-center justify-center text-slate-950 font-black">S
                </div>
                <span class="text-sm font-bold text-white tracking-widest uppercase">Sentinel<span
                        class="text-teal-500 italic">Core</span></span>
            </div>

            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4 text-right">
                    <div>
                        <p class="text-[10px] font-black text-white uppercase tracking-widest">
                            <?= htmlspecialchars($username) ?></p>
                        <p class="text-[9px] font-bold text-teal-500 uppercase tracking-tighter italic"><?= $role ?>
                            node</p>
                    </div>
                    <div
                        class="w-8 h-8 rounded-full bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-500 text-[10px] font-bold">
                        <?= strtoupper(substr($username, 0, 2)) ?>
                    </div>
                </div>
                <div class="h-4 w-px bg-slate-800"></div>
                <a href="?logout=1"
                    class="text-[10px] font-black text-slate-500 hover:text-rose-500 uppercase tracking-widest transition-colors">Terminate</a>
            </div>
        </div>
    </nav>

    <main class="max-w-[1400px] mx-auto w-full px-6 py-10 flex-1 grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- Sidebar Stats -->
        <div class="lg:col-span-1 space-y-6">
            <div class="cyber-panel rounded-2xl p-6">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6">System Integrity</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-bold uppercase">Heuristic Load</span>
                        <span class="text-xs font-black text-white mono">84.2%</span>
                    </div>
                    <div class="h-1.5 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-teal-500 w-[84%]"></div>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-[10px] font-bold uppercase">Node Stability</span>
                        <span class="text-xs font-black text-white mono">99.9%</span>
                    </div>
                    <div class="h-1.5 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 w-[99%]"></div>
                    </div>
                </div>
            </div>

            <div class="cyber-panel rounded-2xl p-6 border-amber-500/20">
                <h3 class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-4">Node Operations</h3>
                <p class="text-[11px] text-slate-500 leading-relaxed mb-6">Elevate your operational priority to access
                    restricted spectrum telemetry.</p>
                <?php if ($role !== 'admin'): ?>
                    <a href="?promote=1"
                        class="block w-full text-center py-3 bg-teal-600/10 border border-teal-500/20 rounded-xl text-teal-500 text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 hover:text-slate-950 transition-all">
                        Upgrade Node Priority
                    </a>
                <?php else: ?>
                    <div class="py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-center">
                        <span class="text-emerald-500 text-[10px] font-black uppercase tracking-widest">Full Spectrum
                            Access</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mono text-[9px] text-slate-600 space-y-1 p-2">
                <p>> relay_sync: ACTIVE</p>
                <p>> ingestion_rate: 4.2k eps</p>
                <p>> buffer_status: NOMINAL</p>
            </div>
        </div>

        <!-- Main Real-time Feed -->
        <div class="lg:col-span-3 space-y-6">

            <!-- Raw Log Stream -->
            <div class="cyber-panel rounded-2xl overflow-hidden border-teal-500/20">
                <div class="px-6 py-4 border-b border-teal-900/30 flex items-center justify-between bg-teal-500/5">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></div>
                        <h2 class="text-xs font-black text-white uppercase tracking-widest">Raw Ingestion Stream
                            (audit.log)</h2>
                    </div>
                    <div class="flex gap-4">
                        <a href="audit.log" target="_blank"
                            class="text-[9px] font-black text-teal-500 uppercase hover:underline">Open Raw</a>
                        <?php if ($role === 'staff' || $role === 'admin'): ?>
                            <a href="?cycle_node=1" onclick="return confirm('Recycle node telemetry buffer?')"
                                class="text-[9px] font-black text-rose-500 uppercase hover:underline">Cycle Buffer</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-6 bg-black/40">
                    <div
                        class="mono text-[11px] text-teal-400/80 h-48 overflow-y-auto leading-relaxed custom-scrollbar">
                        <?= nl2br(htmlspecialchars($logContent)) ?>
                        <div class="animate-pulse">_</div>
                    </div>
                </div>
            </div>

            <!-- Event Log Table -->
            <div class="cyber-panel rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-800">
                    <h2 class="text-xs font-black text-white uppercase tracking-widest">Global Handshake Activity</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900/50">
                                <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest">
                                    Node ID</th>
                                <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest">
                                    Protocol</th>
                                <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest">
                                    Origin</th>
                                <th class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-right">
                                    Coordinate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            <?php foreach ($activities as $act): ?>
                                <tr class="hover:bg-teal-500/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-[10px] font-bold text-white mono">#<?= str_pad($act['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[11px] font-bold text-slate-300"><?= htmlspecialchars($act['action']) ?></span>
                                            <span
                                                class="text-[9px] text-slate-600 truncate max-w-[150px]"><?= htmlspecialchars($act['detail']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-[10px] font-bold text-slate-400 mono"><?= htmlspecialchars($act['ip']) ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border <?= ($act['status'] === 'success') ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border-rose-500/20' ?>">
                                            <?= $act['status'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            class="text-[10px] font-medium text-slate-600 whitespace-nowrap"><?= $act['created_at'] ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #134e4a;
            border-radius: 10px;
        }
    </style>

</body>

</html>