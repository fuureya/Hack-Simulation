<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && $admin['password'] === $password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $admin['username'];
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CinemaX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-200 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-gradient-to-tr from-rose-500 to-orange-400 rounded-2xl flex items-center justify-center shadow-lg shadow-rose-500/20 mx-auto mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Admin Access</h1>
            <p class="text-slate-500 mt-2">Dashboard Pengelolaan Bioskop</p>
        </div>

        <div class="bg-slate-900 border border-white/5 p-8 rounded-[2rem] shadow-2xl backdrop-blur-xl">
             <?php if ($error): ?>
                <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl mb-6 text-sm text-center">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Username</label>
                    <input type="text" name="username" required 
                        class="w-full bg-slate-800 border border-white/5 rounded-xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-rose-500 transition-all text-sm text-white"
                        placeholder="admin">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Password</label>
                    <input type="password" name="password" required 
                        class="w-full bg-slate-800 border border-white/5 rounded-xl px-5 py-4 focus:outline-none focus:ring-2 focus:ring-rose-500 transition-all text-sm text-white"
                        placeholder="••••••••">
                </div>
                <button type="submit" 
                    class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-rose-600/20">
                    Masuk ke Dashboard
                </button>
            </form>
        </div>
        
        <p class="text-center mt-8 text-slate-600 text-xs italic">
            Default credentials: <span class="text-slate-400">admin / admin123</span>
        </p>
    </div>
</body>
</html>
