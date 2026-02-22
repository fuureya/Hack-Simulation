<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['ql_user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['ql_user_id'];
$stmt = $pdo->prepare("SELECT * FROM ql_users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$loanSuccess = '';
$loanError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = floatval($_POST['amount']);
    // VULN A04: Interest rate accepted from POST — can be manipulated to 0
    $interestRate = floatval($_POST['interest_rate'] ?? 2.50);

    // VULN A04: No server-side validation against credit limit. Only client-side check exists.
    $stmt = $pdo->prepare("INSERT INTO ql_loans (user_id, amount, interest_rate, status) VALUES (?, ?, ?, 'approved')");
    $stmt->execute([$userId, $amount, $interestRate]);

    $pdo->prepare("UPDATE ql_users SET total_loan = total_loan + ? WHERE id = ?")->execute([$amount, $userId]);

    $stmt = $pdo->prepare("SELECT * FROM ql_users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $loanSuccess = "Capital deployment of IDR " . number_format($amount, 0, ',', '.') . " finalized at fixed " . $interestRate . "% yield.";
}

$stmt2 = $pdo->prepare("SELECT * FROM ql_loans WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt2->execute([$userId]);
$loans = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Console | QuickLoan Elite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background-color: #f7fee7;
        }

        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex flex-col">

    <!-- Premium Nav -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span
                        class="text-lg font-bold text-slate-900 tracking-tight block leading-tight italic">QuickLoan<span
                            class="text-emerald-500">Elite</span></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Global Capital
                        Management</span>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($user['username']) ?></p>
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Premium Member</p>
                </div>
                <div class="h-8 w-px bg-slate-100"></div>
                <a href="index.php"
                    class="text-[10px] font-black uppercase tracking-widest text-rose-500 hover:text-rose-600 transition-colors">Sign
                    Out</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto w-full px-6 py-12">
        <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight leading-tight">Capital Portfolio</h1>
                <p class="text-slate-500 font-medium mt-1 text-lg">Monitor your liquidity and active credit deployments.
                </p>
            </div>
            <div class="flex gap-4">
                <div class="px-8 py-5 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Available Credit</p>
                        <p class="text-2xl font-black text-slate-900 tracking-tighter">IDR
                            <?= number_format($user['credit_limit'], 0, ',', '.') ?></p>
                    </div>
                </div>
                <div class="px-8 py-5 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 bg-slate-950 text-white rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Exposure</p>
                        <p
                            class="text-2xl font-black <?= ($user['total_loan'] > $user['credit_limit']) ? 'text-rose-500' : 'text-slate-900' ?> tracking-tighter">
                            IDR <?= number_format($user['total_loan'], 0, ',', '.') ?>
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Deployment Form -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white rounded-[2.5rem] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.02)] border border-slate-100">
                    <div class="flex items-center gap-3 mb-8">
                        <div
                            class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Deploy Capital</h2>
                    </div>

                    <?php if ($loanSuccess): ?>
                        <div
                            class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <?= $loanSuccess ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Deployment
                                Amount (IDR)</label>
                            <input type="number" name="amount" required max="<?= $user['credit_limit'] ?>"
                                class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all outline-none"
                                placeholder="Min 100,000">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2.5 ml-1">Term
                                Duration (Months)</label>
                            <select
                                class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 text-sm font-bold focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/5 transition-all outline-none appearance-none">
                                <option>12 Months</option>
                                <option>24 Months</option>
                                <option>36 Months</option>
                            </select>
                        </div>

                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 space-y-3">
                            <div
                                class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                <span>Annual Rate</span>
                                <span class="text-emerald-600 font-black">2.50% Fixed</span>
                            </div>
                            <div
                                class="flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                <span>Service Fee</span>
                                <span class="text-slate-900 font-black">IDR 0.00</span>
                            </div>
                            <div class="h-px bg-slate-200"></div>
                            <div
                                class="flex justify-between items-center text-[11px] font-black uppercase tracking-widest text-slate-900">
                                <span>Status</span>
                                <span class="text-emerald-500 italic">Instant Approval</span>
                            </div>
                        </div>

                        <!-- VULN A04: Interest rate taken from hidden field -->
                        <input type="hidden" name="interest_rate" value="2.50">

                        <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-500 py-5 rounded-2xl text-white font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-emerald-500/20 transition-all active:scale-[0.98]">
                            Finalize Deployment
                        </button>
                    </form>
                </div>
            </div>

            <!-- History -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.02)] border border-slate-100 overflow-hidden">
                    <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between">
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Deployment History</h2>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Live Node
                                Monitoring</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th
                                        class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Deployment ID</th>
                                    <th
                                        class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Capital</th>
                                    <th
                                        class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Rate</th>
                                    <th
                                        class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        Status</th>
                                    <th
                                        class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                        Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php if (empty($loans)): ?>
                                    <tr>
                                        <td colspan="5" class="px-10 py-20 text-center text-slate-400 font-medium italic">No
                                            capital deployments recorded in the current cycle.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($loans as $loan): ?>
                                        <tr class="hover:bg-slate-50/30 transition-colors">
                                            <td class="px-10 py-6">
                                                <span
                                                    class="text-xs font-black text-emerald-600 uppercase tracking-widest">#QL-<?= $loan['id'] ?></span>
                                            </td>
                                            <td class="px-10 py-6">
                                                <p class="text-sm font-bold text-slate-900">IDR
                                                    <?= number_format($loan['amount'], 0, ',', '.') ?></p>
                                            </td>
                                            <td class="px-10 py-6">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="text-sm font-bold <?= $loan['interest_rate'] == 0 ? 'text-rose-500' : 'text-slate-600' ?>">
                                                        <?= $loan['interest_rate'] ?>%
                                                    </span>
                                                    <?php if ($loan['interest_rate'] == 0): ?>
                                                        <span
                                                            class="text-[8px] font-black text-rose-400 uppercase tracking-tighter">Overridden</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="px-10 py-6">
                                                <span
                                                    class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100">
                                                    <?= strtoupper($loan['status']) ?>
                                                </span>
                                            </td>
                                            <td class="px-10 py-6 text-right">
                                                <span
                                                    class="text-[10px] font-bold text-slate-400 whitespace-nowrap"><?= $loan['created_at'] ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8 p-10 bg-slate-900 rounded-[2.5rem] relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-white font-black text-2xl tracking-tight mb-2 italic">QuickLoan<span
                                class="text-emerald-500 ml-1">Elite</span> Plus</h3>
                        <p class="text-slate-400 text-sm font-medium mb-6 max-w-md leading-relaxed">Unlock higher limits
                            and preferred rates with our enterprise partnership program. Contact your relationship node
                            today.</p>
                        <button
                            class="px-6 py-3 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 transition-all">Upgrade
                            Strategy</button>
                    </div>
                    <!-- Decorative Icon -->
                    <div class="absolute top-1/2 -right-10 -translate-y-1/2 opacity-10">
                        <svg class="w-64 h-64 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>

</html>