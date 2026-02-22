<?php
session_start();

if (!isset($_SESSION['user_idor'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_idor'];

// Enterprise Case Data
$tickets = [
    ['id' => 101, 'owner' => 'bob', 'subject' => 'Issue with checkout flow', 'status' => 'Solved', 'priority' => 'High'],
    ['id' => 102, 'owner' => 'alice', 'subject' => 'Legacy account password recovery', 'status' => 'Pending', 'priority' => 'Medium'],
    ['id' => 105, 'owner' => 'admin', 'subject' => 'INTERNAL: Critical Infrastructure Update', 'status' => 'Open', 'priority' => 'Critical']
];

$userTickets = array_filter($tickets, function ($t) use ($username) {
    return $t['owner'] === $username;
});

$solvedCount = count(array_filter($userTickets, fn($t) => $t['status'] === 'Solved'));
$pendingCount = count($userTickets) - $solvedCount;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Zenith Support</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex flex-col">

    <!-- Premium Nav -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold text-slate-900 tracking-tight block leading-tight">Zenith
                        Support</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Enterprise Case
                        Manager</span>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($username) ?></p>
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">USR-ID: <?= $user_id ?>
                    </p>
                </div>
                <div class="h-8 w-px bg-slate-100"></div>
                <a href="index.php?logout=1"
                    class="text-[10px] font-black uppercase tracking-widest text-rose-500 hover:text-rose-600 transition-colors">Terminate</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto w-full px-6 py-12">
        <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight leading-tight">My Active Cases</h1>
                <p class="text-slate-500 font-medium mt-1">Review and manage your ongoing support requests.</p>
            </div>
            <div class="flex gap-4">
                <div class="px-6 py-4 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Solved</p>
                        <p class="text-xl font-black text-slate-900"><?= $solvedCount ?></p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Pending</p>
                        <p class="text-xl font-black text-slate-900"><?= $pendingCount ?></p>
                    </div>
                </div>
            </div>
        </header>

        <div
            class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.02)] border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-50 bg-slate-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Case
                                Metadata</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Subject</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Priority</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status
                            </th>
                            <th
                                class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($userTickets)): ?>
                            <tr>
                                <td colspan="5" class="px-8 py-10 text-center text-slate-400 font-medium italic">No active
                                    cases found for this account.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($userTickets as $ticket): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-8 py-6">
                                        <span
                                            class="text-xs font-black text-indigo-500 uppercase tracking-widest">#CASE-<?= $ticket['id'] ?></span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($ticket['subject']) ?>
                                        </p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-400 rounded-md text-[9px] font-black uppercase tracking-widest border border-slate-100">
                                            <?= $ticket['priority'] ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full <?= $ticket['status'] === 'Solved' ? 'bg-emerald-500' : 'bg-amber-500' ?>"></span>
                                            <span
                                                class="text-xs font-black uppercase tracking-widest <?= $ticket['status'] === 'Solved' ? 'text-emerald-600' : 'text-amber-600' ?>">
                                                <?= $ticket['status'] ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="ticket.php?id=<?= $ticket['id'] ?>"
                                            class="inline-flex items-center gap-2 bg-white text-slate-900 border border-slate-200 px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-950 hover:text-white hover:border-slate-950 transition-all shadow-sm">
                                            Open Case
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            class="mt-12 p-8 bg-indigo-50/50 rounded-[2rem] border border-indigo-100/50 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div
                    class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Need immediate assistance?</h4>
                    <p class="text-xs text-slate-500 font-medium">Our Global Response Team is available 24/7 for
                        critical infrastructure issues.</p>
                </div>
            </div>
            <button
                class="px-6 py-3 bg-white text-indigo-600 border border-indigo-100 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all">Contact
                Enterprise Support</button>
        </div>
    </main>

</body>

</html>