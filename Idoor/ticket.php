<?php
session_start();

if (!isset($_SESSION['user_idor'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$username = $_SESSION['user_idor'];

// Enterprise Case Repository
$tickets = [
    101 => ['owner' => 'bob', 'subject' => 'Issue with checkout flow', 'message' => 'I cannot complete my purchase. The cart button is not responsive on the final step.', 'date' => '2026-02-10 14:30', 'priority' => 'High', 'department' => 'Commerce'],
    102 => ['owner' => 'alice', 'subject' => 'Legacy account password recovery', 'message' => 'I forgot my password and need a reset link sent to my primary email address.', 'date' => '2026-02-12 09:15', 'priority' => 'Medium', 'department' => 'Identity'],
    105 => ['owner' => 'admin', 'subject' => 'INTERNAL: Critical Infrastructure Update', 'message' => 'FLAG{IDOR_ACCESS_ADMIN_TICKET_2026}. The root access key for the new migration node is: P@ssw0rd_Admin!', 'date' => '2026-02-14 10:00', 'priority' => 'Critical', 'department' => 'Infrastructure']
];

// VULNERABILITY: It checks if the ticket EXISTS, but NOT if the user OWNS it.
if (!isset($tickets[$id])) {
    die("Case not found or access denied.");
}

$ticket = $tickets[$id];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Detail | Zenith Support</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }

        .thread-item {
            position: relative;
        }

        .thread-item::before {
            content: '';
            position: absolute;
            left: 1.5rem;
            top: 3.5rem;
            bottom: 0;
            width: 2px;
            background: #f1f5f9;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen py-10 px-6">

    <div class="max-w-3xl mx-auto">
        <header class="mb-8 flex items-center justify-between">
            <a href="dashboard.php"
                class="inline-flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                Return to Cases
            </a>
            <div class="flex items-center gap-2">
                <span
                    class="px-3 py-1 bg-white border border-slate-100 rounded-lg text-[10px] font-black text-slate-400 uppercase tracking-widest shadow-sm">
                    Node: HQ-SGP-1
                </span>
            </div>
        </header>

        <div
            class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.02)] border border-slate-100 overflow-hidden">
            <!-- Case Header -->
            <div class="p-10 border-b border-slate-50 bg-slate-50/30">
                <div class="flex items-start justify-between gap-6 mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <span
                                class="px-2.5 py-1 bg-indigo-600 text-white rounded-md text-[9px] font-black uppercase tracking-widest">
                                #CASE-<?= htmlspecialchars($id) ?>
                            </span>
                            <span
                                class="px-2.5 py-1 bg-slate-200 text-slate-500 rounded-md text-[9px] font-black uppercase tracking-widest">
                                <?= htmlspecialchars($ticket['priority']) ?> Priority
                            </span>
                        </div>
                        <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-tight">
                            <?= htmlspecialchars($ticket['subject']) ?>
                        </h1>
                    </div>
                </div>

                <div
                    class="flex flex-wrap items-center gap-y-4 gap-x-8 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Requester: <span class="text-slate-900 ml-1"><?= htmlspecialchars($ticket['owner']) ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Created: <span class="text-slate-900 ml-1"><?= htmlspecialchars($ticket['date']) ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1" />
                        </svg>
                        Dept: <span class="text-slate-900 ml-1"><?= htmlspecialchars($ticket['department']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Case Content (Thread View) -->
            <div class="p-10 space-y-12">
                <div class="thread-item flex gap-6">
                    <div
                        class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 shrink-0 relative z-10 border-4 border-white">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <span
                                class="text-xs font-black text-slate-900 tracking-wide"><?= strtoupper($ticket['owner']) ?>
                                (Requester)</span>
                            <span
                                class="text-[10px] font-bold text-slate-300 uppercase"><?= htmlspecialchars($ticket['date']) ?></span>
                        </div>
                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                            <p class="text-slate-700 leading-relaxed font-medium whitespace-pre-wrap">
                                <?= htmlspecialchars($ticket['message']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-6 opacity-60">
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shrink-0 relative z-10 border-4 border-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black text-indigo-600 tracking-wide uppercase">Assistant Response
                                (Draft)</span>
                        </div>
                        <div class="bg-indigo-50/50 p-6 rounded-3xl border border-indigo-100 border-dashed">
                            <p class="text-slate-400 text-xs font-medium italic">Your draft response will appear here.
                                As an enterprise partner, you can only view cases assigned to your UID.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-slate-50/50 border-t border-slate-50 flex justify-end gap-4">
                <button
                    class="px-6 py-3 bg-white text-slate-600 border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">Archive
                    Case</button>
                <button
                    class="px-6 py-3 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Submit
                    Update</button>
            </div>
        </div>
    </div>

</body>

</html>