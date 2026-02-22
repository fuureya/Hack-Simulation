<?php
session_start();

$error = '';
// Legacy Bootstrap Credentials (A05: Security Misconfiguration)
$validCredentials = [
    'admin' => 'admin',
    'devops' => 'devops',
    'root' => 'root',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    if (isset($validCredentials[$u]) && $validCredentials[$u] === $p) {
        $_SESSION['sm_user'] = $u;
        header("Location: portal.php");
        exit;
    } else {
        // Diagnostic Feedback (Verbose Errors)
        if (!isset($validCredentials[$u])) {
            $error = "Node identity <strong>'$u'</strong> not recognized in registry.";
        } else {
            $error = "Credential mismatch for node <strong>'$u'</strong>. Registry Version: 8.2-STABLE.";
        }
    }
}

// System Diagnostic Trigger
if (isset($_GET['diag_trace'])) {
    $undefined_var;
    $arr = ['a' => 1];
    echo $arr['nonexistent'];
    require_once 'nonexistent_file.php';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Gateway | Titanium Cloud</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background-color: #0f172a;
        }

        .industrial-blur {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .accent-border {
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-6 bg-[#0f172a] text-slate-400">

    <div class="max-w-[440px] w-full">
        <!-- Brand Header -->
        <div class="text-center mb-12">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white border border-slate-200 mb-6 shadow-2xl shadow-blue-500/10">
                <svg class="w-10 h-10 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2">Titanium</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Cloud Orchestration Node</p>
        </div>

        <!-- Login Container -->
        <div
            class="industrial-blur rounded-[2.5rem] p-10 shadow-[0_32px_64px_rgba(0,0,0,0.5)] relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-8">Node Authentication</h2>

                <?php if ($error): ?>
                    <div
                        class="mb-8 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl text-[11px] font-bold accent-border">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Node
                            Identifier</label>
                        <input type="text" name="username" required
                            class="w-full bg-slate-900/50 border border-slate-800 rounded-2xl p-4 text-sm font-bold text-white focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none"
                            placeholder="Enter username">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Access
                            Passkey</label>
                        <input type="password" name="password" required
                            class="w-full bg-slate-900/50 border border-slate-800 rounded-2xl p-4 text-sm font-bold text-white focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none"
                            placeholder="••••••••">
                    </div>

                    <button type="submit"
                        class="w-full bg-white hover:bg-slate-200 py-5 rounded-2xl text-slate-950 font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-white/5 transition-all active:scale-[0.98]">
                        Establish Control Link
                    </button>
                </form>

                <div class="mt-12 pt-8 border-t border-slate-800/50">
                    <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-4">System Manifests</p>
                    <div
                        class="flex flex-wrap gap-x-6 gap-y-3 text-[10px] font-bold text-blue-500/60 uppercase tracking-widest">
                        <a href="info.php" target="_blank" class="hover:text-blue-400">Node Info</a>
                        <a href=".env" target="_blank" class="hover:text-blue-400">Env Manifest</a>
                        <a href="config.bak" target="_blank" class="hover:text-blue-400">Config Backup</a>
                        <a href="database_backup.sql.bak" target="_blank" class="hover:text-blue-400">DB Archive</a>
                        <a href="?diag_trace=1" target="_blank" class="hover:text-blue-400">Diagnostic Trace</a>
                    </div>
                </div>
            </div>

            <!-- Subtle glow -->
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-blue-500/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Meta info -->
        <div class="mt-10 text-center">
            <p class="text-[10px] font-bold text-slate-700 uppercase tracking-[0.3em] leading-relaxed">
                Protected by Titanium Core Security Protocols.<br>
                Unauthorized Access is Prohibited.
            </p>
        </div>
    </div>

</body>

</html>