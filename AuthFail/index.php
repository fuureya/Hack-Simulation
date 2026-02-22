<?php
// VULN A07: Session Fixation
if (isset($_GET['PHPSESSID'])) {
    session_id($_GET['PHPSESSID']);
}
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM sl_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // VULN A07: Session ID NOT regenerated
        $_SESSION['sl_user_id'] = $user['id'];
        $_SESSION['sl_username'] = $user['username'];
        $_SESSION['sl_role'] = $user['role'];

        if (isset($_POST['remember'])) {
            // VULN A07: Predictable remember-me token
            $token = base64_encode($user['id'] . ':' . strtotime('today'));
            setcookie('remember_me', $token, time() + 86400 * 30, '/');
            $pdo->prepare("UPDATE sl_users SET remember_token = ? WHERE id = ?")->execute([$token, $user['id']]);
        }

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Identitas atau kredensial yang Anda masukkan tidak valid.";
    }
}

// Handle remember_me token
if (!isset($_SESSION['sl_user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $pdo->prepare("SELECT * FROM sl_users WHERE remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['sl_user_id'] = $user['id'];
        $_SESSION['sl_username'] = $user['username'];
        $_SESSION['sl_role'] = $user['role'];
        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureLogin Enterprise SSO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            background-image: radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.05) 0px, transparent 50%);
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-[480px]">
        <div class="text-center mb-10">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-indigo-600 shadow-2xl shadow-indigo-200 mb-6 bg-gradient-to-tr from-indigo-600 to-indigo-500">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002-2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">SecureLogin SSO</h1>
            <p class="text-slate-500 mt-2 font-medium">Sistem Autentikasi Terpusat Perusahaan</p>
        </div>

        <div class="glass rounded-[2.5rem] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-white/40">
            <?php if ($error): ?>
                <div
                    class="mb-8 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-xs font-bold flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama
                        Pengguna (SSO ID)</label>
                    <input type="text" name="username" required
                        class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                        placeholder="Masukkan username Anda">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kata
                            Sandi</label>
                        <a href="#"
                            class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-widest">Lupa
                            Sandi?</a>
                    </div>
                    <input type="password" name="password" required
                        class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-4 text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between py-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="remember"
                                class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border-2 border-slate-200 transition-all checked:bg-indigo-600 checked:border-indigo-600" />
                            <svg class="absolute h-3.5 w-3.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100 left-0.5 top-0.5"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span
                            class="text-xs font-bold text-slate-500 group-hover:text-slate-700 transition-colors">Ingat
                            saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                    class="btn-primary w-full py-5 rounded-2xl text-white font-bold shadow-xl shadow-indigo-200 active:scale-[0.98]">
                    Masuk Sekarang
                </button>
            </form>
        </div>

        <p class="text-center mt-10 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
            &copy; 2026 SecureLogin Corp Enterprise Division
        </p>
    </div>
</body>

</html>