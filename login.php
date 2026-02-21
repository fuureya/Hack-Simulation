<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabSec Manager - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-gradient-to-tr from-purple-500 to-pink-500 rounded-3xl flex items-center justify-center shadow-2xl shadow-purple-500/50 mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tight">LabSec Manager</h1>
            <p class="text-purple-300 font-medium mt-2">Security Lab Management Dashboard</p>
        </div>

        <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-10 rounded-[2.5rem] shadow-2xl">
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-rose-500/20 border border-rose-500/50 text-rose-200 p-4 rounded-2xl text-sm font-bold text-center mb-6">
                    Invalid credentials!
                </div>
            <?php endif; ?>

            <form action="login_process.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-white/60 uppercase tracking-widest mb-2">Username</label>
                    <input type="text" name="username" required 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-purple-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-white/60 uppercase tracking-widest mb-2">Password</label>
                    <input type="password" name="password" required 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-purple-500 outline-none transition-all">
                </div>
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-5 rounded-3xl shadow-xl shadow-purple-500/30 active:scale-95 transition-all text-sm">
                    Access Dashboard
                </button>
            </form>
        </div>

        <p class="text-center mt-8 text-[10px] font-bold text-white/40 uppercase tracking-[0.2em]">
            Powered by LabSec Framework
        </p>
    </div>
</body>
</html>
