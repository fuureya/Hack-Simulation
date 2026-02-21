<?php
session_start();

// VULNERABILITY: It only checks if a session exists, but not the ROLE.
// Anyone logged in as a 'user' can access this 'admin' page.
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Success flag
$flag = "FLAG{HORIZONTAL_ACCESS_BYPASSED_2026}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Broken Access Control Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 min-h-screen text-white p-8">

    <div class="max-w-4xl mx-auto">
        <div class="bg-red-600 p-6 rounded-t-2xl shadow-xl">
            <h1 class="text-3xl font-black uppercase tracking-widest">Admin Control Panel</h1>
            <p class="text-red-200 text-sm">Strictly restricted to administrative personnel only.</p>
        </div>
        
        <div class="bg-slate-800 p-8 rounded-b-2xl shadow-2xl border border-slate-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-slate-700 p-6 rounded-xl border border-slate-600">
                    <h2 class="text-slate-400 font-bold uppercase text-xs mb-2">System Status</h2>
                    <p class="text-2xl font-bold text-emerald-400">All Systems Operational</p>
                </div>
                <div class="bg-slate-700 p-6 rounded-xl border border-slate-600">
                    <h2 class="text-slate-400 font-bold uppercase text-xs mb-2">Logged in as</h2>
                    <p class="text-2xl font-bold"><?= htmlspecialchars($_SESSION['user']) ?></p>
                </div>
            </div>

            <div class="bg-yellow-900/30 border border-yellow-700/50 p-6 rounded-xl mb-8">
                <h3 class="text-yellow-500 font-black mb-2">ADMIN SECRET REVEALED:</h3>
                <code class="text-xl bg-black/40 p-4 rounded block border border-yellow-900/50 text-center">
                    <?= $flag ?>
                </code>
            </div>

            <div class="flex justify-between items-center">
                <p class="text-slate-500 text-sm italic">Congratulations! You discovered that this page doesn't check for administrative privileges.</p>
                <a href="index.php" class="bg-slate-700 hover:bg-slate-600 px-6 py-2 rounded-lg font-bold transition-colors">Back to Dashboard</a>
            </div>
        </div>
    </div>

</body>
</html>
