<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Identitas atau PIN yang Anda masukkan tidak valid.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoBank | Digital Priority Banking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #020617;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .grad-bg {
            background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #020617 100%);
        }
    </style>
</head>

<body class="grad-bg min-h-screen flex flex-col items-center justify-center p-6 text-slate-300">

    <div class="w-full max-w-[440px]">
        <div class="text-center mb-10">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-indigo-600 shadow-2xl shadow-indigo-500/20 mb-6 bg-gradient-to-tr from-indigo-600 to-indigo-400">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold text-white tracking-tight">NeoBank</h1>
            <p class="text-slate-500 mt-2 font-medium">Layanan Perbankan Digital Eksklusif</p>
        </div>

        <div class="glass rounded-[2.5rem] p-10 shadow-2xl overflow-hidden relative">
            <div class="absolute -right-20 -top-20 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>

            <?php if ($error): ?>
                <div
                    class="mb-8 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl text-xs font-bold flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6 relative z-10">
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">Access
                        Identifier</label>
                    <input type="text" name="username" required
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm font-semibold focus:outline-none focus:border-indigo-500 transition-all text-white placeholder-slate-700"
                        placeholder="Masukkan username Anda">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Secret
                            Passkey</label>
                    </div>
                    <input type="password" name="password" required
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm font-semibold focus:outline-none focus:border-indigo-500 transition-all text-white placeholder-slate-700"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 py-5 rounded-2xl text-white font-bold shadow-xl shadow-indigo-500/20 transition-all active:scale-[0.98]">
                    Masuk ke Vault Saya
                </button>
            </form>
        </div>

        <p class="text-center mt-10 text-[10px] font-bold text-slate-600 uppercase tracking-[0.3em]">
            &copy; 2026 NeoBank Digital Foundation. Secure Node Active.
        </p>
    </div>

</body>

</html>