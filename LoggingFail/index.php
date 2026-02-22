<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM at_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['at_user_id'] = $user['id'];
        $_SESSION['at_username'] = $user['username'];
        $_SESSION['at_role'] = $user['role'];

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $logLine = date('Y-m-d H:i:s') . " | SUCCESS | Node Auth: $username (IP: $ip)\n";
        file_put_contents(__DIR__ . '/audit.log', $logLine, FILE_APPEND);

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Authentication failed: Invalid credentials for node access.";
    }
}

// Node Handshake Sequence (Log Injection)
if (isset($_GET['handshake'])) {
    $sequence = $_GET['handshake'];
    $logLine = date('Y-m-d H:i:s') . " | SUCCESS | Node Auth: $sequence (IP: 127.0.0.1)\n";
    file_put_contents(__DIR__ . '/audit.log', $logLine, FILE_APPEND);
    header("Location: index.php?sync=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Portal | Sentinel Threat Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #020617;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .cyber-grid {
            background-image: linear-gradient(rgba(15, 118, 110, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 118, 110, 0.05) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .glow-teal {
            box-shadow: 0 0 20px rgba(20, 184, 166, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-[#020617] text-slate-400 cyber-grid">

    <!-- Hero Side -->
    <div
        class="hidden md:flex md:w-5/12 p-12 flex-col justify-between relative overflow-hidden border-r border-teal-900/30">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-teal-500 rounded-lg flex items-center justify-center text-slate-950">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-white tracking-widest uppercase">Sentinel<span
                        class="text-teal-500 italic">Core</span></span>
            </div>

            <div class="space-y-6">
                <h1 class="text-5xl font-black text-white leading-tight tracking-tighter uppercase">Total <span
                        class="text-teal-500 italic">Awareness</span></h1>
                <p class="text-slate-500 font-medium text-lg leading-relaxed max-w-sm">Global threat intelligence and
                    heuristic log telemetry for enterprise infrastructure.</p>
            </div>
        </div>

        <div class="relative z-10 mono text-[10px] space-y-2 opacity-40">
            <p>> initializing sentinel.node.auth</p>
            <p>> loading global_threat_feeds... [OK]</p>
            <p>> heartbeating relay_cluster_01... [LIVE]</p>
        </div>

        <!-- Decorative blur -->
        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Auth Side -->
    <div class="flex-1 flex flex-col justify-center p-8 md:p-24 relative bg-slate-950/50 backdrop-blur-sm">
        <div class="max-w-[380px] w-full mx-auto">
            <div class="mb-10">
                <h2 class="text-2xl font-black text-white tracking-widest uppercase mb-2">Operator Login</h2>
                <div class="h-1 w-12 bg-teal-500 mb-4"></div>
                <p class="text-sm font-medium text-slate-500">Provide node-access credentials to synchronize telemetry.
                </p>
            </div>

            <?php if ($error): ?>
                <div
                    class="mb-8 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['sync'])): ?>
                <div
                    class="mb-8 p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Node Handshake Synchronized Successfully
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label
                        class="block text-[10px] font-black text-teal-500 uppercase tracking-[0.2em] mb-3 ml-1">Operator
                        Identifier</label>
                    <input type="text" name="username" required
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl p-4 text-sm font-bold text-white focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/5 transition-all outline-none"
                        placeholder="Enter username">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-teal-500 uppercase tracking-[0.2em] mb-3 ml-1">Access
                        Key</label>
                    <input type="password" name="password" required
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl p-4 text-sm font-bold text-white focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/5 transition-all outline-none"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full bg-teal-600 hover:bg-teal-500 py-4 rounded-xl text-slate-950 font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-teal-500/10 transition-all active:scale-[0.98]">
                    Establish Node Link
                </button>
            </form>

            <div class="mt-16 pt-8 border-t border-teal-900/20 flex flex-col gap-4">
                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                    <span class="text-slate-600">Infrastructure Logs</span>
                    <a href="audit.log" target="_blank" class="text-teal-600 hover:text-teal-400">View Node Trace</a>
                </div>
                <p class="text-[9px] text-slate-700 leading-relaxed uppercase tracking-tighter">
                    NOTICE: Unauthorized access to Sentinel nodes is monitored and logged in accordance with Directive
                    7. All telemetry is under encryption.
                </p>
            </div>
        </div>
    </div>

</body>

</html>