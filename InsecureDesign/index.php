<?php
session_start();
require_once 'db.php';

$error = '';
$step = $_GET['step'] ?? 'login'; // login | reset

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM ql_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $pdo->prepare("UPDATE ql_users SET failed_attempts = 0 WHERE id = ?")->execute([$user['id']]);
        $_SESSION['ql_user_id'] = $user['id'];
        $_SESSION['ql_username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        if ($user) {
            $pdo->prepare("UPDATE ql_users SET failed_attempts = failed_attempts + 1 WHERE id = ?")->execute([$user['id']]);
        }
        $error = "Access Denied: The member identifier or security key is incorrect.";
    }
}

$resetSuccess = false;
$resetError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'reset') {
    $username = trim($_POST['username'] ?? '');
    $answer = strtolower(trim($_POST['answer'] ?? ''));

    $stmt = $pdo->prepare("SELECT * FROM ql_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && strtolower($user['security_answer']) === $answer) {
        $newPass = password_hash('NewPass123!', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE ql_users SET password = ? WHERE id = ?")->execute([$newPass, $user['id']]);
        $resetSuccess = true;
    } else {
        $resetError = 'Verification failed: Security answer is incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickLoan Elite | Instant Digital Lending</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background-color: #f7fee7;
        }

        .hero-pattern {
            background-color: #064e3b;
            background-image: radial-gradient(#059669 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col md:flex-row bg-[#f7fee7]">

    <!-- Sidebar Hero -->
    <div class="hidden md:flex md:w-1/2 hero-pattern p-12 flex-col justify-between relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-10 text-emerald-400">
                <div
                    class="w-12 h-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center border border-emerald-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-black tracking-tight text-white italic">QuickLoan<span
                        class="text-emerald-500 ml-1">Elite</span></span>
            </div>

            <div class="max-w-md">
                <h1 class="text-5xl font-black text-white leading-tight mb-6">Empowering your future, <span
                        class="text-emerald-500">instantly.</span></h1>
                <p class="text-emerald-100/60 font-medium text-lg leading-relaxed">Access premium digital lending
                    solutions with zero paperwork. Fast, secure, and tailored for you.</p>
            </div>
        </div>

        <div class="relative z-10 flex items-center gap-8">
            <div class="flex -space-x-3">
                <div
                    class="w-10 h-10 rounded-full border-2 border-[#064e3b] bg-emerald-100 flex items-center justify-center text-[10px] font-bold text-[#064e3b]">
                    JD</div>
                <div
                    class="w-10 h-10 rounded-full border-2 border-[#064e3b] bg-emerald-500 flex items-center justify-center text-[10px] font-bold text-white">
                    AS</div>
                <div
                    class="w-10 h-10 rounded-full border-2 border-[#064e3b] bg-emerald-950 flex items-center justify-center text-[10px] font-bold text-emerald-400">
                    MK</div>
            </div>
            <p class="text-xs text-emerald-500/80 font-black uppercase tracking-widest">Trusted by 2.4k users globally
            </p>
        </div>

        <!-- Decorative blur -->
        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Auth Content -->
    <div class="flex-1 flex flex-col justify-center p-8 md:p-20 relative bg-white md:rounded-l-[3rem] shadow-2xl">
        <div class="max-w-[400px] w-full mx-auto">
            <?php if ($step === 'login'): ?>
                <div class="mb-10">
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Welcome Back</h2>
                    <p class="text-slate-400 font-medium">Please authenticate to manage your capital.</p>
                </div>

                <?php if ($error): ?>
                    <div
                        class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Account
                            Identifier</label>
                        <input type="text" name="username" required
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all outline-none"
                            placeholder="Enter username">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2.5 ml-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Secret
                                Key</label>
                            <a href="?step=reset"
                                class="text-[10px] font-black text-emerald-600 uppercase tracking-widest hover:text-emerald-500">Forgotten?</a>
                        </div>
                        <input type="password" name="password" required
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all outline-none"
                            placeholder="••••••••">
                    </div>

                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 py-5 rounded-2xl text-white font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-emerald-500/20 transition-all active:scale-[0.98]">
                        Authorize Access
                    </button>
                </form>

            <?php elseif ($step === 'reset'): ?>
                <div class="mb-10">
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Recovery Node</h2>
                    <p class="text-slate-400 font-medium">Verify your identity via security challenge.</p>
                </div>

                <?php if ($resetSuccess): ?>
                    <div class="mb-8 p-6 bg-emerald-50 border border-emerald-100 rounded-3xl">
                        <div
                            class="flex items-center gap-3 text-emerald-600 font-black text-[10px] uppercase tracking-widest mb-4">
                            <div class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            Success
                        </div>
                        <p class="text-sm text-slate-600 font-medium mb-4 leading-relaxed">Your password has been reset to its
                            default state for security.</p>
                        <div class="bg-white border border-emerald-100 p-4 rounded-xl text-center">
                            <code class="text-emerald-600 font-black">NewPass123!</code>
                        </div>
                        <a href="index.php"
                            class="block w-full text-center mt-6 text-[10px] font-black text-emerald-600 uppercase tracking-widest hover:underline">Proceed
                            to Login</a>
                    </div>
                <?php else: ?>
                    <?php if ($resetError): ?>
                        <div
                            class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <?= $resetError ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Account
                                Identifier</label>
                            <input type="text" name="username" required
                                class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all outline-none"
                                placeholder="Username">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Security
                                Challenge</label>
                            <p class="text-[10px] text-slate-400 font-medium mb-3 ml-1">System Prompt: First pet name / Birth
                                city / Favorite color</p>
                            <input type="text" name="answer" required
                                class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all outline-none"
                                placeholder="Enter answer">
                        </div>

                        <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-500 py-5 rounded-2xl text-white font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-emerald-500/20 transition-all active:scale-[0.98]">
                            Verify Identity
                        </button>
                    </form>
                    <div class="mt-6 text-center">
                        <a href="index.php"
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-emerald-600">Cancel
                            Recovery</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="mt-20 pt-10 border-t border-slate-100">
                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.2em] leading-relaxed text-center">
                    Protected by QuantumEncrypt™ Technology.<br>
                    © 2026 QuickLoan Global Partners.
                </p>
            </div>
        </div>
    </div>

</body>

</html>