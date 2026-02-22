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

// Process Update Password (CSRF Vulnerable)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_password = $_POST['new_password'] ?? '';

    if (!empty($new_password)) {
        try {
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_password, $user_id]);
            $msg = "Profil keamanan berhasil diperbarui. Password Anda telah diganti.";
        } catch (Exception $e) {
            $error = "Terjadi kegagalan sistem saat memperbarui otentikasi.";
        }
    } else {
        $error = "Credential baru tidak boleh kosong!";
    }
}

// Fetch user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vault Security Management | NeoBank</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #020617;
            color: #94a3b8;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .bg-neo {
            background: radial-gradient(circle at 100% 100%, #1e1b4b 0%, #020617 100%);
        }
    </style>
</head>

<body class="bg-neo min-h-screen">

    <!-- Premium Nav -->
    <nav class="sticky top-0 z-50 glass border-b border-white/5 px-6 py-4">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="dashboard.php"
                    class="p-2.5 bg-white/5 text-slate-400 rounded-xl hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <span class="text-xs font-black text-white uppercase tracking-widest">Security Center</span>
            </div>
            <a href="logout.php"
                class="text-[10px] font-black uppercase tracking-widest text-rose-500/80 hover:text-rose-400 transition-colors">Terminate
                Session</a>
        </div>
    </nav>

    <main class="max-w-xl mx-auto px-6 py-12">
        <div class="glass rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden border border-white/10">
            <div class="absolute -left-20 -top-20 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>

            <div class="flex items-center gap-5 mb-10 relative z-10">
                <div
                    class="w-14 h-14 bg-indigo-600/20 border border-indigo-500/30 rounded-2xl flex items-center justify-center text-indigo-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002-2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-white tracking-tight">Pengaturan Keamanan</h2>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Update your vault access
                        keys</p>
                </div>
            </div>

            <?php if ($msg): ?>
                <div
                    class="mb-8 p-5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-[1.5rem] text-sm font-bold flex items-center gap-4">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <?= $msg ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div
                    class="mb-8 p-5 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-[1.5rem] text-sm font-bold flex items-center gap-4">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8 relative z-10">
                <div class="opacity-50">
                    <label
                        class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 ml-1">Account
                        Identifier</label>
                    <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled
                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-sm font-bold text-slate-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 ml-1">New
                        Secret Passkey</label>
                    <input type="password" name="new_password" required
                        class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-sm font-bold focus:ring-2 focus:ring-indigo-500 outline-none text-white placeholder-slate-800"
                        placeholder="Masukkan password baru">
                    <p class="mt-4 text-[10px] font-medium text-slate-600 leading-relaxed uppercase tracking-wider">
                        Keamanan Anda adalah prioritas kami. Gunakan kombinasi karakter yang kuat untuk melindungi aset
                        digital Anda di NeoBank.
                    </p>
                </div>

                <button type="submit" name="update_profile"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-5 rounded-2xl shadow-xl shadow-indigo-500/10 transition-all active:scale-[0.98]">
                    Konfirmasi Perubahan Akses
                </button>
            </form>
        </div>

        <div class="mt-10 p-8 glass rounded-[2.5rem] border border-white/5 flex items-start gap-4">
            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-500 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-[10px] font-medium text-slate-500 leading-relaxed uppercase tracking-wider">
                Anda sedang mengakses Security Center NeoBank Priority. Semua perubahan pada kunci akses akan dicatat
                dan diverifikasi melalui protokol enkripsi internal.
            </p>
        </div>
    </main>

</body>

</html>