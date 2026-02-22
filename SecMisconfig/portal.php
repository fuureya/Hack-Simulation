<?php
session_start();
if (!isset($_SESSION['sm_user'])) {
    header("Location: index.php");
    exit;
}
$user = $_SESSION['sm_user'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infrastructure Hub | Titanium Cloud</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background-color: #020617;
            color: #94a3b8;
        }

        .industrial-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.4) 0%, rgba(15, 23, 42, 0.4) 100%);
            border: 1px solid rgba(59, 130, 246, 0.05);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">

    <!-- Header -->
    <nav class="border-b border-slate-800 bg-slate-950/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-[1400px] mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-black">T</div>
                <span class="text-sm font-black text-white tracking-[0.3em] uppercase italic">Titanium<span
                        class="text-blue-500">Node</span></span>
            </div>

            <div class="flex items-center gap-8">
                <div class="flex items-center gap-4 text-right">
                    <div>
                        <p class="text-[10px] font-black text-white uppercase tracking-widest">
                            <?= htmlspecialchars($user) ?></p>
                        <p class="text-[9px] font-bold text-blue-500 uppercase tracking-tighter italic">Authorized
                            Personnel</p>
                    </div>
                    <div
                        class="w-8 h-8 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 text-[10px] font-black">
                        <?= strtoupper(substr($user, 0, 2)) ?>
                    </div>
                </div>
                <div class="h-4 w-px bg-slate-800"></div>
                <a href="index.php"
                    class="text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-widest transition-colors">Terminate</a>
            </div>
        </div>
    </nav>

    <main class="max-w-[1400px] mx-auto w-full px-6 py-10 flex-1 space-y-8">

        <!-- Dashboard Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="stat-card rounded-3xl p-8 relative overflow-hidden group hover:border-blue-500/20 transition-all">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-6">Active Clusters</h3>
                <p class="text-5xl font-black text-white italic tracking-tighter">12</p>
                <div
                    class="absolute -bottom-4 -right-4 text-8xl font-black text-slate-800/10 group-hover:text-blue-500/5 transition-colors select-none">
                    NODE</div>
            </div>
            <div
                class="stat-card rounded-3xl p-8 relative overflow-hidden group hover:border-blue-500/20 transition-all">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-6">Orchestrated Nodes
                </h3>
                <p class="text-5xl font-black text-white italic tracking-tighter">47</p>
                <div
                    class="absolute -bottom-4 -right-4 text-8xl font-black text-slate-800/10 group-hover:text-blue-500/5 transition-colors select-none">
                    SYNC</div>
            </div>
            <div
                class="stat-card rounded-3xl p-8 border-amber-500/10 relative overflow-hidden group hover:border-amber-500/20 transition-all bg-amber-500/5">
                <h3 class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-6">System Advisories</h3>
                <p class="text-5xl font-black text-amber-500 italic tracking-tighter">08</p>
                <div
                    class="absolute -bottom-4 -right-4 text-8xl font-black text-amber-500/5 group-hover:text-amber-500/10 transition-colors select-none italic tracking-tighter">
                    ATTN</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Manifest Archive -->
            <div class="industrial-panel rounded-[2rem] p-8">
                <h2
                    class="text-xs font-black text-slate-200 uppercase tracking-widest mb-8 border-b border-slate-800 pb-6">
                    Node Manifests & Performance Archives</h2>
                <div class="space-y-4">
                    <a href="/.env" target="_blank"
                        class="flex items-center justify-between p-5 bg-slate-900/40 rounded-2xl border border-slate-800 hover:border-blue-500/30 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 font-bold text-xs group-hover:bg-blue-600 group-hover:text-white transition-all">
                                ENV</div>
                            <div>
                                <p class="text-xs font-bold text-slate-300">Environment Manifest</p>
                                <p class="text-[10px] text-slate-600 uppercase font-black tracking-tighter">Production
                                    Variables</p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 bg-rose-500/10 text-rose-500 rounded text-[9px] font-black uppercase tracking-widest border border-rose-500/20">Critical</span>
                    </a>
                    <a href="/config.bak" target="_blank"
                        class="flex items-center justify-between p-5 bg-slate-900/40 rounded-2xl border border-slate-800 hover:border-blue-500/30 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 font-bold text-xs group-hover:bg-blue-600 group-hover:text-white transition-all">
                                BAK</div>
                            <div>
                                <p class="text-xs font-bold text-slate-300">Configuration Backup</p>
                                <p class="text-[10px] text-slate-600 uppercase font-black tracking-tighter">Registry
                                    Snapshot</p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 bg-rose-500/10 text-rose-500 rounded text-[9px] font-black uppercase tracking-widest border border-rose-500/20">Critical</span>
                    </a>
                    <a href="/database_backup.sql.bak" target="_blank"
                        class="flex items-center justify-between p-5 bg-slate-900/40 rounded-2xl border border-slate-800 hover:border-blue-500/30 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 font-bold text-xs group-hover:bg-blue-600 group-hover:text-white transition-all">
                                SQL</div>
                            <div>
                                <p class="text-xs font-bold text-slate-300">Database Archive</p>
                                <p class="text-[10px] text-slate-600 uppercase font-black tracking-tighter">Structural
                                    Dump</p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 bg-rose-500/10 text-rose-500 rounded text-[9px] font-black uppercase tracking-widest border border-rose-500/20">Critical</span>
                    </a>
                    <a href="/info.php" target="_blank"
                        class="flex items-center justify-between p-5 bg-slate-900/40 rounded-2xl border border-slate-800 hover:border-blue-500/30 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 font-bold text-xs group-hover:bg-blue-600 group-hover:text-white transition-all">
                                PHP</div>
                            <div>
                                <p class="text-xs font-bold text-slate-300">Standard Runtime Info</p>
                                <p class="text-[10px] text-slate-600 uppercase font-black tracking-tighter">Core
                                    Configuration</p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 bg-amber-500/10 text-amber-500 rounded text-[9px] font-black uppercase tracking-widest border border-amber-500/20">High</span>
                    </a>
                </div>
            </div>

            <!-- Runtime Context -->
            <div class="industrial-panel rounded-[2rem] p-8 flex flex-col">
                <h2
                    class="text-xs font-black text-slate-200 uppercase tracking-widest mb-8 border-b border-slate-800 pb-6">
                    System Runtime Context (Live Reflection)</h2>
                <div class="bg-black/40 p-6 rounded-2xl border border-slate-800/50 flex-1 overflow-hidden">
                    <div
                        class="font-mono text-[11px] text-emerald-400/80 space-y-2 overflow-y-auto h-full custom-scrollbar">
                        <p class="text-slate-600 italic">// Active Environment Variables</p>
                        <p>DB_HOST=labsec-db</p>
                        <p>DB_PASSWORD=labsec_root_2026</p>
                        <p>SECRET_KEY=super_secret_key_12345</p>
                        <p>JWT_SECRET=jwt_secret_do_not_share</p>
                        <p>AWS_ACCESS_KEY_ID=AKIAIOSFODNN7EXAMPLE</p>
                        <p>AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY</p>
                        <p>PAYMENT_GATEWAY_KEY=sk_live_51AbCdEfGhIjKlMnOpQ</p>
                        <div class="animate-pulse">_</div>
                    </div>
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
            background: #1e293b;
            border-radius: 10px;
        }
    </style>

</body>

</html>