<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$flag = "FLAG{HORIZONTAL_ACCESS_BYPASSED_2026}";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Node: Administrative Control | SmartRetail</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }

        .terminal {
            background: #020617;
            border: 1px solid #1e293b;
        }

        .scan-line {
            width: 100%;
            height: 2px;
            background: rgba(34, 197, 94, 0.1);
            position: absolute;
            animation: scan 4s linear infinite;
        }

        @keyframes scan {
            from {
                top: 0;
            }

            to {
                top: 100%;
            }
        }
    </style>
</head>

<body
    class="bg-[#020617] text-slate-400 min-h-screen p-8 flex flex-col items-center justify-center relative overflow-hidden">
    <div class="scan-line"></div>

    <div class="max-w-4xl w-full relative z-10">
        <!-- Header -->
        <header class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-center justify-center text-rose-500">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002-2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight uppercase">Admin Node Management</h1>
                    <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.3em]">Restricted Access Area •
                        Node-774</p>
                </div>
            </div>
            <div
                class="px-4 py-2 bg-rose-500/20 text-rose-400 rounded-lg text-[10px] font-black uppercase tracking-widest border border-rose-500/20">
                Unauthorized Access Warning
            </div>
        </header>

        <div class="terminal rounded-[2rem] overflow-hidden shadow-2xl shadow-indigo-500/10">
            <div class="bg-slate-900/50 p-4 border-b border-slate-800 flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                <span class="ml-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">system_dump.log</span>
            </div>

            <div class="p-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="bg-slate-900/80 p-6 rounded-2xl border border-slate-800">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Authenticated As
                        </p>
                        <p class="text-2xl font-bold text-white"><?= htmlspecialchars($_SESSION['user']) ?></p>
                        <p class="text-[10px] font-semibold text-indigo-400 mt-1 uppercase tracking-widest">
                            <?= strtoupper($_SESSION['role']) ?> Level Credentials</p>
                    </div>
                    <div class="bg-slate-900/80 p-6 rounded-2xl border border-slate-800">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">System Entropy
                        </p>
                        <p class="text-2xl font-bold text-emerald-400">99.98% Stable</p>
                        <p class="text-[10px] font-semibold text-slate-600 mt-1 uppercase tracking-widest">Global Relay
                            Active</p>
                    </div>
                </div>

                <div class="bg-emerald-500/5 border border-emerald-500/20 p-8 rounded-[2rem] text-center">
                    <div
                        class="w-16 h-16 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-emerald-400 mb-2">ACCESS BYPASSED SUCCESSFULLY</h3>
                    <p class="text-slate-500 text-sm font-medium mb-8 uppercase tracking-widest">System security check
                        skipped for horizontal node relay</p>

                    <div class="relative group">
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-indigo-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
                        </div>
                        <code
                            class="relative block bg-[#020617] p-6 rounded-2xl border border-emerald-500/30 text-emerald-100 font-mono text-xl tracking-wider select-all">
                            <?= $flag ?>
                        </code>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            <a href="index.php"
                class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-600 hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Return to Member Node
            </a>
        </div>
    </div>
</body>

</html>