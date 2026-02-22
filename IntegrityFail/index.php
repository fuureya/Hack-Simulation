<?php
require_once 'session.php';

$user = get_session();

if (!$user) {
    $user = new UserProfile('guest', 'visitor');
    set_session($user);
}

$isAdmin = (isset($user->is_admin) && $user->is_admin === true) || (isset($user->role) && $user->role === 'admin');

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identity Hub | Orbital DevOps</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: #0f172a;
        }

        .hero-gradient {
            background: radial-gradient(circle at top right, #1e293b 0%, #0f172a 100%);
        }

        .glass {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(51, 65, 85, 0.5);
        }
    </style>
</head>

<body class="hero-gradient text-slate-300 min-h-screen flex flex-col items-center justify-center p-6">

    <div class="max-w-[540px] w-full">
        <!-- Logo Area -->
        <div class="text-center mb-10">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600/10 border border-blue-500/20 mb-6 text-blue-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight mb-2 italic">Orbital<span
                    class="text-blue-500">DevOps</span></h1>
            <p class="text-xs font-black text-slate-500 uppercase tracking-[0.3em]">Integrity Protocol v2.8</p>
        </div>

        <!-- Main Card -->
        <div class="glass rounded-[2rem] p-10 glow relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Authenticated
                            Identity</p>
                        <h2 class="text-2xl font-bold text-white"><?= htmlspecialchars($user->username) ?></h2>
                    </div>
                    <div>
                        <span
                            class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest <?= $isAdmin ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-slate-700 text-slate-400' ?>">
                            <?= $isAdmin ? 'Admin Node' : 'Observer' ?>
                        </span>
                    </div>
                </div>

                <div class="space-y-6">
                    <?php if ($isAdmin): ?>
                        <div class="p-6 bg-emerald-500/5 border border-emerald-500/20 rounded-3xl">
                            <div class="flex items-center gap-3 mb-4 text-emerald-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span class="text-[10px] font-black uppercase tracking-widest">Protocol Override
                                    Confirmed</span>
                            </div>
                            <p class="text-sm text-slate-400 mb-4 leading-relaxed">Secure credentials verified.
                                Administrative commands available for this node.</p>
                            <div
                                class="bg-slate-900/80 p-4 rounded-xl border border-slate-800 text-center select-all cursor-pointer">
                                <code class="text-emerald-500 font-bold">FLAG{OBJ_SERIAL_INTEGRITY_COMPROMISED}</code>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-6 bg-slate-800/20 border border-slate-800 rounded-3xl">
                            <p class="text-sm text-slate-500 leading-relaxed italic text-center">
                                Limited access mode. Administrative functions are restricted to verified orbital nodes.
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="pt-6 border-t border-slate-800/50">
                        <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-4">Identity Debug
                            Manifest</p>
                        <div
                            class="bg-black/40 p-5 rounded-2xl border border-slate-800 font-mono text-[9px] text-blue-400/80 break-all leading-relaxed hover:text-blue-400 transition-colors">
                            <?= $_COOKIE['session'] ?? 'NULL_CONTEXT' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decorative blur -->
            <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Footer -->
        <div class="mt-10 text-center">
            <p class="text-[10px] font-bold text-slate-600 uppercase tracking-[0.2em] leading-relaxed">
                By accessing this node, you agree to the Orbital Operational Directive.<br>
                © 2026 Orbital DevOps Group. All Rights Reserved.
            </p>
        </div>
    </div>

</body>

</html>