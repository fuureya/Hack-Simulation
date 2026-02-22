<?php
session_start();

$users = [
    'bob' => 'bob123',
    'alice' => 'alice123'
];

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($users[$username]) && $users[$username] === $password) {
        $_SESSION['user_idor'] = $username;
        $_SESSION['user_id'] = ($username === 'bob') ? 101 : 102;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Access Denied: The credentials provided do not match our records.';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zenith Support | Partner Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }

        .hero-gradient {
            background: radial-gradient(circle at top right, #eef2ff 0%, #f8fafc 100%);
        }
    </style>
</head>

<body class="hero-gradient min-h-screen flex flex-col items-center justify-center p-6 text-slate-600">

    <div class="max-w-[440px] w-full">
        <div class="text-center mb-10">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white shadow-xl shadow-indigo-500/5 mb-6 border border-slate-100">
                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Zenith Support</h1>
            <p class="text-sm font-medium text-slate-400">Manage your enterprise service requests</p>
        </div>

        <div
            class="bg-white rounded-[2.5rem] p-10 shadow-[0_20px_60px_rgba(0,0,0,0.03)] border border-slate-100 relative overflow-hidden">
            <?php if ($error): ?>
                <div
                    class="mb-8 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8">
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Staff
                        Identifier</label>
                    <input type="text" name="username" required
                        class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 transition-all outline-none"
                        placeholder="Enter username">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">Passkey</label>
                    <input type="password" name="password" required
                        class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/5 transition-all outline-none"
                        placeholder="••••••••">
                </div>

                <button type="submit" name="login"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 py-5 rounded-2xl text-white font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-indigo-500/20 transition-all active:scale-[0.98]">
                    Authenticate Node
                </button>
            </form>
        </div>

        <div class="mt-8 flex flex-col items-center gap-4">
            <div
                class="px-4 py-2 bg-slate-100 rounded-full text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                Staff Portal v2.0
            </div>
            <p class="text-[10px] font-bold text-slate-400/60 uppercase tracking-widest text-center leading-relaxed">
                By signing in, you agree to our Internal Data Handling Protocols.<br>
                For technical assistance, contact the <span class="text-indigo-600/60">Global IT Desk</span>.
            </p>
        </div>
    </div>

</body>

</html>