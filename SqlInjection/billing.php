<?php
require_once 'db.php';

$invoice_id = $_GET['invoice_id'] ?? '';
$error = '';
$billing = null;

if ($invoice_id) {
    // VULNERABLE: Error-Based & Union-Based
    $query = "SELECT b.*, p.name as patient_name FROM billing b JOIN patients p ON b.patient_id = p.id WHERE b.id = '$invoice_id'";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        $error = "Database Error: " . mysqli_error($conn);
    } else {
        $billing = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Pasien - NeoHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'IBM Plex Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Invoice Pasien</h1>
            <a href="index.php" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Kembali ke Beranda</a>
        </div>

        <?php if ($error): ?>
            <div class="bg-rose-50 border border-rose-200 p-6 rounded-2xl mb-8 flex gap-4 items-start shadow-sm">
                <div class="bg-rose-500 p-2 rounded-lg text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-rose-800 font-bold">SQL Integration Exception</h3>
                    <p class="text-rose-700 text-[11px] font-mono mt-1 leading-relaxed break-all"><?= $error ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($billing): ?>
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200 border-t-8 border-t-rose-600">
                <div class="p-10">
                    <div class="flex justify-between items-start mb-10 pb-8 border-b border-slate-100">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ID TAGIHAN</p>
                            <h2 class="text-xl font-bold font-mono">#INV-<?= str_pad($billing['id'], 6, '0', STR_PAD_LEFT) ?></h2>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">STATUS</p>
                            <span class="bg-emerald-100 text-emerald-700 font-bold text-[10px] px-3 py-1 rounded-full uppercase">LUNAS</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-10 mb-10">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">DITUJUKAN KEPADA</p>
                            <h3 class="text-lg font-bold text-slate-800"><?= htmlspecialchars($billing['patient_name']) ?></h3>
                            <p class="text-xs text-slate-500 mt-1 italic">ID Pasien: <?= $billing['patient_id'] ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">METODE PEMBAYARAN</p>
                            <h3 class="text-lg font-bold text-slate-800"><?= htmlspecialchars($billing['payment_method']) ?></h3>
                            <p class="text-xs text-rose-500 mt-1 font-mono font-bold"><?= htmlspecialchars($billing['card_number']) ?></p>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-8 flex justify-between items-center mb-4">
                        <span class="text-slate-500 font-bold uppercase tracking-widest text-xs">Total Pembayaran</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tighter">Rp <?= number_format($billing['total'], 0, ',', '.') ?></span>
                    </div>

                    <p class="text-[10px] text-slate-400 text-center italic mt-6">Simpan tagihan ini sebagai bukti pembayaran yang sah di NeoHMS.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white p-20 rounded-[2.5rem] border border-slate-200 text-center shadow-sm">
                 <h3 class="text-slate-400 font-bold uppercase tracking-widest text-xs">Invoice tidak ditemukan</h3>
            </div>
        <?php endif; ?>

        <div class="mt-12 p-8 bg-rose-500/10 border border-rose-500/30 rounded-3xl">
            <h4 class="text-rose-600 font-bold flex items-center gap-2 mb-2 text-sm italic">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Vulnerability: Financial Data Leak
            </h4>
            <p class="text-[11px] text-slate-600 leading-relaxed italic">
                Fitur billing ini sengaja mengekspos detail kredensial kartu pembayaran. Kerentanan SQLi di sini dapat dimanfaatkan melalui parameter <code class="bg-rose-100 px-1 rounded">invoice_id</code>.
            </p>
            <div class="mt-4 bg-slate-900 p-4 rounded-xl text-[9px] font-mono text-rose-400 border border-white/5 uppercase">
                UNION Payload: ?invoice_id=1' UNION SELECT 1,2,username,password,role,6 FROM users -- -
            </div>
        </div>
    </div>
</body>
</html>
