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
        $error = 'Username atau Password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoBank - Log In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .bca-blue { background-color: #00529C; }
    </style>
</head>
<body class="bg-slate-50 flex flex-col min-h-screen">
    <div class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <div class="text-center mb-10">
                <div class="w-20 h-20 bca-blue rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-500/30 mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter">NeoBank</h1>
                <p class="text-slate-500 font-medium mt-1">Solusi Perbankan Masa Depan</p>
            </div>

            <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100">
                <?php if ($error): ?>
                    <div class="bg-rose-50 text-rose-500 p-4 rounded-2xl text-xs font-bold text-center mb-6">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Access ID</label>
                        <input type="text" name="username" required 
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                            placeholder="Username">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Passkey</label>
                        <input type="password" name="password" required 
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                            placeholder="Password">
                    </div>
                    <button type="submit" 
                        class="w-full bca-blue text-white font-bold py-5 rounded-3xl shadow-xl shadow-blue-500/30 active:scale-95 transition-all text-sm">
                        Masuk Ke Akun
                    </button>
                </form>
            </div>

            <p class="text-center mt-10 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                Licensed by NeoBank Foundation
            </p>
        </div>
    </div>
</body>
</html>
