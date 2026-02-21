<?php
session_start();

if (!isset($_SESSION['user_idor'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$username = $_SESSION['user_idor'];

// Simple mock data for tickets
$tickets = [
    101 => ['owner' => 'bob', 'subject' => 'Issue with checkout', 'message' => 'I cannot complete my purchase. The cart button is not responsive.', 'date' => '2026-02-10 14:30'],
    102 => ['owner' => 'alice', 'subject' => 'Password reset request', 'message' => 'I forgot my password and need a reset link sent to my email.', 'date' => '2026-02-12 09:15'],
    105 => ['owner' => 'admin', 'subject' => 'CONFIDENTIAL: Server Migration Plan', 'message' => 'FLAG{IDOR_ACCESS_ADMIN_TICKET_2026}. Secret password for the new server is: P@ssw0rd_Admin!', 'date' => '2026-02-14 10:00']
];

// VULNERABILITY: It checks if the ticket EXISTS, but NOT if the user OWNS it.
if (!isset($tickets[$id])) {
    die("Ticket not found.");
}

$ticket = $tickets[$id];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Details - Support Ticket System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-8">

    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="dashboard.php" class="text-indigo-600 font-bold hover:underline">← Back to Dashboard</a>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-100 flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-black text-slate-900"><?= htmlspecialchars($ticket['subject']) ?></h1>
                    <p class="text-slate-500 text-sm mt-1">Ticket #<?= htmlspecialchars($id) ?> • Posted on <?= htmlspecialchars($ticket['date']) ?></p>
                </div>
                <div class="bg-indigo-50 text-indigo-600 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest">
                    <?= htmlspecialchars($ticket['owner']) ?>
                </div>
            </div>
            
            <div class="p-8">
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <p class="text-slate-700 leading-relaxed font-medium">
                        <?= htmlspecialchars($ticket['message']) ?>
                    </p>
                </div>

                <?php if ($ticket['owner'] === 'admin'): ?>
                    <div class="mt-8 p-6 bg-emerald-50 border border-emerald-100 rounded-2xl">
                        <h4 class="text-emerald-800 font-bold mb-2">Simulation Success!</h4>
                        <p class="text-emerald-700 text-sm">You have successfully exploited an IDOR vulnerability to view sensitive administrative information.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
