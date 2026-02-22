<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$userData = [
    'user' => [
        'full_name' => 'Basic Member Account',
        'email' => 'member.user@smartretail.com',
        'joined' => '15 Aug 2025',
        'points' => '1,250'
    ],
    'guest' => [
        'full_name' => 'Guest Visitor Account',
        'email' => 'guest.visit@smartretail.com',
        'joined' => 'N/A',
        'points' => '0'
    ]
];

$user = $userData[$_SESSION['user']];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya | SmartRetail Member</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 flex flex-col">

    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="index.php"
                class="flex items-center gap-2 text-slate-900 font-bold hover:text-emerald-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Portal
            </a>
            <span class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Pengaturan Akun</span>
        </div>
    </nav>

    <main class="max-w-2xl mx-auto w-full px-6 py-12">
        <div
            class="bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.03)] border border-slate-100 overflow-hidden">
            <!-- Header/Cover -->
            <div class="h-32 bg-gradient-to-r from-emerald-500 to-teal-500"></div>

            <div class="p-10 -mt-16 text-center">
                <!-- Avatar -->
                <div class="w-32 h-32 rounded-3xl bg-white p-2 shadow-xl mx-auto mb-6">
                    <div class="w-full h-full rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-black text-slate-900 leading-tight mb-1">
                    <?= htmlspecialchars($user['full_name']) ?></h1>
                <p class="text-slate-400 font-medium text-sm mb-4"><?= htmlspecialchars($user['email']) ?></p>

                <div class="flex items-center justify-center gap-2 mb-8">
                    <span
                        class="px-4 py-1.5 bg-emerald-100 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-200"><?= strtoupper($_SESSION['role']) ?>
                        Level</span>
                    <span
                        class="px-4 py-1.5 bg-slate-100 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-200">Verified
                        Member</span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-left">
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Mulai Bergabung
                        </p>
                        <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($user['joined']) ?></p>
                    </div>
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Point Reward</p>
                        <p class="text-sm font-bold text-indigo-600"><?= htmlspecialchars($user['points']) ?> Pts</p>
                    </div>
                </div>

                <button
                    class="w-full mt-10 py-5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all active:scale-[0.98]">
                    Ubah Informasi Profil
                </button>
            </div>
        </div>

        <p class="mt-8 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-loose">
            Data Anda dilindungi oleh kebijakan privasi SmartRetail.<br>Kami tidak membagikan data Anda kepada pihak
            ketiga.
        </p>
    </main>
</body>

</html>