<?php
session_start();

if (!isset($_SESSION['user_idor'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_idor'];

// Simple mock data for tickets
$tickets = [
    ['id' => 101, 'owner' => 'bob', 'subject' => 'Issue with checkout', 'status' => 'Solved'],
    ['id' => 102, 'owner' => 'alice', 'subject' => 'Password reset request', 'status' => 'Pending'],
    ['id' => 105, 'owner' => 'admin', 'subject' => 'CONFIDENTIAL: Server Migration Plan', 'status' => 'Open']
];

$userTickets = array_filter($tickets, function($t) use ($username) {
    return $t['owner'] === $username;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Support Ticket System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-8">

    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-900">Welcome, <?= htmlspecialchars($username) ?></h1>
                <p class="text-slate-500 font-medium">Your ID: <span class="text-indigo-600">USR-00<?= $user_id ?></span></p>
            </div>
            <a href="index.php?logout=1" class="text-rose-600 font-bold hover:bg-rose-50 px-4 py-2 rounded-xl transition-all">Logout</a>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
            <div class="bg-indigo-600 p-6">
                <h2 class="text-white font-bold text-lg">Your Support Tickets</h2>
            </div>
            
            <div class="p-8">
                <?php if (empty($userTickets)): ?>
                    <p class="text-slate-400 italic">No tickets found.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($userTickets as $ticket): ?>
                            <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:border-indigo-200 transition-all group">
                                <div>
                                    <h3 class="font-bold text-slate-800"><?= htmlspecialchars($ticket['subject']) ?></h3>
                                    <p class="text-sm text-slate-500 mt-1">Ticket ID: #<?= $ticket['id'] ?></p>
                                </div>
                                <div class="flex items-center gap-6">
                                    <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest <?= $ticket['status'] === 'Solved' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' ?>">
                                        <?= $ticket['status'] ?>
                                    </span>
                                    <a href="ticket.php?id=<?= $ticket['id'] ?>" class="bg-white text-indigo-600 border border-indigo-200 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-8 bg-amber-50 border border-amber-200 p-6 rounded-2xl">
            <h4 class="text-amber-800 font-bold flex items-center gap-2 mb-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                Hint for Simulation
            </h4>
            <p class="text-amber-700 text-sm">Observe the URL parameter when viewing a ticket. Can you access tickets that don't belong to you? Try finding the admin's migration plan.</p>
        </div>
    </div>

</body>
</html>
