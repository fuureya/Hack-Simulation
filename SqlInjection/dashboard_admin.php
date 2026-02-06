<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - NeoHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-blue-600 p-4 text-white flex justify-between">
        <span class="font-bold">NeoHMS Admin Dashboard</span>
        <a href="logout.php" class="text-sm border border-white/50 px-3 py-1 rounded">Logout</a>
    </nav>
    <main class="p-12 max-w-4xl mx-auto">
        <div class="bg-white p-10 rounded-3xl shadow-xl">
            <h1 class="text-3xl font-black mb-4">Selamat Datang, <?= $_SESSION['username'] ?>!</h1>
            <p class="text-slate-600 mb-8">Anda berhasil masuk dengan role: <span class="font-bold text-blue-600 uppercase"><?= $_SESSION['role'] ?></span></p>
            
            <div class="grid grid-cols-2 gap-6">
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                    <h3 class="font-bold mb-2">Statistik Pasien</h3>
                    <p class="text-2xl font-black text-slate-800">1.254</p>
                </div>
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                    <h3 class="font-bold mb-2">Rekam Medis</h3>
                    <p class="text-2xl font-black text-slate-800">5.820</p>
                </div>
            </div>
            
            <div class="mt-8 p-6 bg-emerald-50 border border-emerald-100 rounded-2xl">
                 <h4 class="text-emerald-800 font-bold mb-2">Simulasi Berhasil!</h4>
                 <p class="text-sm text-emerald-700">Jika Anda sampai di sini menggunakan teknik SQL Injection (Login Bypass), berarti simulasi berhasil dilakukan.</p>
            </div>
        </div>
    </main>
</body>
</html>
<?php
// logout.php placeholder
?>
