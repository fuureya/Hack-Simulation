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
        $error = 'Invalid username or password';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support Ticket System - IDOR Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center justify-center">

    <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-md border border-slate-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
            </div>
            <h1 class="text-3xl font-black text-slate-900">Ticketing System</h1>
            <p class="text-slate-500">Log in to view your support tickets</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-rose-50 border border-rose-100 text-rose-600 px-4 py-3 rounded-2xl mb-6 text-sm font-bold">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2 ml-1">Username</label>
                <input type="text" name="username" class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" placeholder="Enter username" required>
            </div>
            <div>
                <label class="block text-slate-700 text-xs font-black uppercase tracking-widest mb-2 ml-1">Password</label>
                <input type="password" name="password" class="w-full px-5 py-3 rounded-2xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" placeholder="Enter password" required>
            </div>
            <button type="submit" name="login" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl shadow-lg shadow-indigo-200 transition-all">
                Sign In
            </button>
        </form>

        <div class="mt-8 pt-8 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Available Credentials</p>
            <div class="mt-2 flex justify-center gap-4 text-xs font-medium text-slate-500">
                <span>bob / bob123</span>
                <span>•</span>
                <span>alice / alice123</span>
            </div>
        </div>
    </div>

</body>
</html>
