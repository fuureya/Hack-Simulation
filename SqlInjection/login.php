<?php
session_start();
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // VULNERABLE: Boolean-Based Blind SQLi
    // Query ini rentan bypass dan ekstraksi data meskipun tidak menampilkan error DB
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard_admin.php');
        exit;
    } else {
        $error = "Login Gagal! Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Login Staff - NeoHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-200 min-h-screen flex items-center justify-center p-6 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-slate-900 via-slate-900 to-blue-900">
    
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-500/20 mx-auto mb-6">
                 <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Login Portal Staff</h1>
            <p class="text-slate-500 mt-2 text-sm">Akses dashboard internal rumah sakit.</p>
        </div>

        <div class="bg-white/5 backdrop-blur-2xl border border-white/10 p-10 rounded-[2.5rem] shadow-2xl">
            <?php if ($error): ?>
                <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl mb-6 text-sm text-center font-medium animate-shake">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Username</label>
                    <input type="text" name="username" required 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm text-white placeholder-slate-600"
                        placeholder="Masukkan username">
                </div>
                <div>
                    <div class="flex justify-between mb-2 px-1">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest">Password</label>
                    </div>
                    <input type="password" name="password" required 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm text-white placeholder-slate-600"
                        placeholder="••••••••">
                </div>
                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-5 rounded-2xl transition-all shadow-xl shadow-blue-600/25 active:scale-95">
                    Masuk Sekarang
                </button>
            </form>
        </div>

        <div class="mt-12 p-6 bg-yellow-500/10 border border-yellow-500/20 rounded-2xl">
            <h4 class="text-yellow-400 font-bold mb-2 flex items-center gap-2 text-xs italic">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Hint: Boolean-Based Blind
            </h4>
            <p class="text-[10px] text-slate-500 leading-relaxed italic">
                Celah ini tidak menampilkan error DB. Jika query benar (misal via SQLi), login akan dialihkan. Jika salah, muncul pesan error standar.
            </p>
            <code class="text-[9px] text-yellow-300/50 block mt-2 font-mono">Bypass: ' OR 1=1 -- -</code>
        </div>
    </div>

    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .animate-shake { animation: shake 0.2s ease-in-out 0s 2; }
    </style>
</body>
</html>
