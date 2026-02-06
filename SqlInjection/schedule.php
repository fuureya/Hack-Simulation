<?php
require_once 'db.php';

$doctor_id = $_GET['doctor_id'] ?? '';
$schedules = [];

if ($doctor_id) {
    // VULNERABLE: Union-Based SQLi
    $query = "SELECT d.name as doctor_name, d.specialist, d.schedule FROM doctors d WHERE d.id = $doctor_id";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $schedules[] = $row;
        }
    }
} else {
    // List all doctors initially if no ID specified
    $query = "SELECT id, name, specialist FROM doctors";
    $result = mysqli_query($conn, $query);
    $all_doctors = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Dokter - NeoHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 min-h-screen">
    <nav class="bg-white border-b border-slate-200 py-4 px-8 flex justify-between items-center shadow-sm">
        <a href="index.php" class="text-xl font-black text-blue-600 tracking-tight">NeoHMS</a>
        <a href="index.php" class="text-sm font-bold text-slate-500 hover:text-blue-600 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Beranda
        </a>
    </nav>

    <main class="max-w-5xl mx-auto py-12 px-6">
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Jadwal Praktek Dokter</h1>
            <p class="text-slate-500 mt-2">Daftar dokter spesialis dan waktu konsultasi yang tersedia.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Sidebar Doctor list -->
            <div class="md:col-span-1 space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest px-4 mb-4">Pilih Dokter</h3>
                <?php if (isset($all_doctors)): ?>
                    <?php foreach ($all_doctors as $doc): ?>
                        <a href="schedule.php?doctor_id=<?= $doc['id'] ?>" 
                           class="block px-6 py-4 rounded-2xl border <?= $doctor_id == $doc['id'] ? 'bg-blue-600 border-blue-600 text-white shadow-lg' : 'bg-white border-slate-100 hover:border-blue-300 text-slate-600 shadow-sm' ?> transition-all font-semibold text-sm">
                            <?= htmlspecialchars($doc['name']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Main Schedule Display -->
            <div class="md:col-span-3">
                <?php if ($doctor_id && !empty($schedules)): ?>
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl overflow-hidden animate-in fade-in duration-500">
                        <?php foreach ($schedules as $sch): ?>
                            <div class="p-10 border-b border-slate-50 last:border-0">
                                <span class="text-blue-600 font-bold text-xs uppercase tracking-widest"><?= htmlspecialchars($sch['specialist']) ?></span>
                                <h2 class="text-3xl font-black text-slate-900 mt-2 mb-6"><?= htmlspecialchars($sch['doctor_name']) ?></h2>
                                
                                <div class="bg-slate-50 rounded-2xl p-6 flex justify-between items-center group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Waktu Konsultasi</p>
                                            <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($sch['schedule']) ?></p>
                                        </div>
                                    </div>
                                    <button class="bg-slate-900 text-white px-6 py-3 rounded-xl text-xs font-bold hover:bg-blue-600 transition-colors shadow-lg">Pesan Jadwal</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($doctor_id): ?>
                    <div class="bg-white rounded-3xl p-12 border border-slate-100 shadow-sm text-center">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h3 class="font-bold text-slate-800">Tidak ada jadwal ditemukan</h3>
                        <p class="text-slate-500 text-sm mt-1">Silakan pilih dokter dari menu di samping.</p>
                    </div>
                <?php else: ?>
                    <div class="bg-blue-600/5 border-2 border-dashed border-blue-200/50 rounded-[2.5rem] py-32 text-center">
                         <div class="w-20 h-20 bg-white rounded-3xl shadow-xl flex items-center justify-center mx-auto mb-6 text-blue-600">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h2 class="text-2xl font-black text-blue-900 tracking-tight">Pilih Dokter Terlebih Dahulu</h2>
                        <p class="text-blue-700/60 max-w-sm mx-auto mt-2 font-medium">Klik pada salah satu nama dokter di sebelah kiri untuk melihat jadwal praktek mereka.</p>
                    </div>
                <?php endif; ?>

                <div class="mt-12 p-8 bg-slate-900 rounded-[2.5rem] text-white">
                    <h4 class="text-amber-400 font-bold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Lab Security: Info Kerentanan
                    </h4>
                    <p class="text-xs text-slate-400 leading-relaxed italic mb-4">
                        Parameter <code class="bg-white/10 px-1 rounded text-slate-300">doctor_id</code> pada halaman ini tidak difilter. 
                        Penyerang dapat menggunakan teknik <strong>Union-Based SQL Injection</strong> untuk mengekstrak tabel akun atau billing.
                    </p>
                    <div class="bg-white/5 p-4 rounded-xl text-[10px] font-mono text-slate-300 border border-white/5 ring-1 ring-white/10 uppercase tracking-widest">
                        Payload Target: Dump Billing <br>
                        ?doctor_id=1 UNION SELECT patient_id, total, card_number FROM billing -- -
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
