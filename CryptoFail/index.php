<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Legacy Auth Support (MD5)
    $hashed = md5($password);

    $stmt = $pdo->prepare("SELECT * FROM sv_users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $hashed]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Session Sync Metadata
        setcookie('user_info', json_encode([
            'id' => $user['id'],
            'username' => $user['username'],
            'credit_card' => $user['credit_card'],
            'pin' => $user['pin'],
            'balance' => $user['balance'],
        ]), time() + 3600, '/');

        // Persistence Token
        setcookie('remember_token', md5($user['username'] . 'safevault'), time() + 86400, '/');

        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Access Denied: Invalid credentials provided.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuantumGuard | Secure Access Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: #020617;
        }

        .glow-border {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .cyber-bg {
            background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #020617 100%);
        }

        .scanline {
            width: 100%;
            height: 2px;
            background: rgba(99, 102, 241, 0.1);
            position: absolute;
            animation: scan 4s linear infinite;
        }

        @keyframes scan {
            from {
                top: 0;
            }

            to {
                top: 100%;
            }
        }
    </style>
</head>

<body class="cyber-bg min-h-screen flex items-center justify-center p-6 text-slate-400 overflow-hidden relative">
    <div class="scanline"></div>

    <div class="max-w-[480px] w-full relative z-10">
        <div class="text-center mb-12">
            <div
                class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-indigo-500/10 border border-indigo-500/20 shadow-2xl shadow-indigo-500/10 mb-6">
                <svg class="w-12 h-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-widest uppercase mb-2">QuantumGuard</h1>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em]">Enterprise Cryptographic Vault
                v4.2</p>
        </div>

        <div class="bg-white/5 backdrop-blur-3xl rounded-[2.5rem] p-12 glow-border relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl"></div>

            <?php if ($error): ?>
                <div
                    class="mb-8 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8 relative z-10">
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 ml-1">Identity
                        UID</label>
                    <input type="text" name="username" required
                        class="w-full bg-black/40 border border-white/5 rounded-2xl p-5 text-sm font-semibold focus:outline-none focus:border-indigo-500 transition-all text-white placeholder-slate-800"
                        placeholder="Enter access ID">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-3 ml-1">Encodings
                        Key</label>
                    <input type="password" name="password" required
                        class="w-full bg-black/40 border border-white/5 rounded-2xl p-5 text-sm font-semibold focus:outline-none focus:border-indigo-500 transition-all text-white placeholder-slate-800"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 py-6 rounded-2xl text-white font-bold text-xs uppercase tracking-[0.3em] shadow-2xl shadow-indigo-500/20 transition-all active:scale-[0.98]">
                    Initiate Decryption Flow
                </button>
            </form>
        </div>

        <div class="mt-12 flex justify-between items-center px-4">
            <div class="flex flex-col">
                <span class="text-[8px] font-bold text-slate-600 uppercase tracking-widest">Node Status</span>
                <span
                    class="text-[10px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Encrypted Connection
                </span>
            </div>
            <div class="flex flex-col text-right">
                <span class="text-[8px] font-bold text-slate-600 uppercase tracking-widest">Protocol</span>
                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Legacy-Compatible
                    (v2)</span>
            </div>
        </div>
    </div>
</body>

</html>