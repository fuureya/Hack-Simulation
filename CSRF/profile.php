<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = '';
$error = '';

// Proses Update Password (CSRF Vulnerable)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_password = $_POST['new_password'] ?? '';
    
    if (!empty($new_password)) {
        try {
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_password, $user_id]);
            $msg = "Password Anda berhasil diperbarui!";
        } catch (Exception $e) {
            $error = "Terjadi kesalahan saat memperbarui password.";
        }
    } else {
        $error = "Password baru tidak boleh kosong!";
    }
}

// Ambil data user
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoBank - Profil Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen">
    <!-- Navbar -->
    <nav class="border-b border-white/10 bg-white/5 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <a href="dashboard.php" class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">NeoBank</a>
                </div>
                <div class="flex items-center gap-6">
                    <a href="dashboard.php" class="text-sm hover:text-indigo-400 transition-colors">Dashboard</a>
                    <a href="logout.php" class="bg-red-500/10 text-red-400 hover:bg-red-500/20 px-4 py-2 rounded-lg text-sm font-medium transition-all border border-red-500/30">Keluar</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-lg shadow-indigo-500/20">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">Profil Pengguna</h2>
                    <p class="text-slate-400 text-sm">Kelola informasi akun Anda</p>
                </div>
            </div>

            <?php if ($msg): ?>
                <div class="bg-green-500/20 border border-green-500/50 text-green-200 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <?= $msg ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-500/50 text-red-200 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Username</label>
                    <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled
                        class="w-full bg-slate-800/50 border border-white/5 rounded-2xl px-5 py-4 text-slate-500 cursor-not-allowed">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Ganti Password Baru</label>
                    <input type="password" name="new_password" required
                        class="w-full bg-slate-900 border border-white/10 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all placeholder-slate-600"
                        placeholder="••••••••">
                    <p class="mt-2 text-xs text-slate-500 italic">Simulasi Privilege Escalation: Seorang attacker bisa memaksa Anda mengganti password ini tanpa sepengetahuan Anda melalui CSRF.</p>
                </div>

                <div class="pt-4">
                    <button type="submit" name="update_profile"
                        class="w-full px-10 py-4 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-2xl transition-all shadow-lg shadow-purple-500/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            <div class="mt-12 p-6 bg-red-500/10 border border-red-500/30 rounded-2xl">
                <h4 class="text-red-400 font-bold flex items-center gap-2 mb-2 text-sm italic">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Privilege Escalation Risk
                </h4>
                <p class="text-xs text-slate-400 leading-relaxed italic">
                    Form ganti password ini tidak menggunakan mekanisme keamanan seperti konfirmasi password lama atau <strong>CSRF token</strong>. 
                    Akibatnya, session admin/user yang sedang aktif dapat dimanipulasi oleh attacker untuk melakukan pengambilalihan akun (Account Takeover).
                </p>
            </div>
        </div>
    </main>
</body>
</html>
