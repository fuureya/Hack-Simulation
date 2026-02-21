<?php
require_once 'db.php';

$id = $_GET['id'] ?? '';
$error = '';
$patient = null;
$records = [];

if ($id) {
    // VULNERABLE: Direct concatenation (Error-Based & Union-Based)
    $query = "SELECT * FROM patients WHERE id = '$id'";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        // Sengaja menampilkan error database untuk Error-Based SQLi
        $error = "Database Error: " . mysqli_error($conn);
    } else {
        $patient = mysqli_fetch_assoc($result);
        
        if ($patient) {
            // Ambil medical records (juga rentan jika dikembangkan)
            $p_id = $patient['id'];
            $record_res = mysqli_query($conn, "SELECT mr.*, d.name as doctor_name FROM medical_records mr JOIN doctors d ON mr.doctor_id = d.id WHERE patient_id = $p_id");
            while ($row = mysqli_fetch_assoc($record_res)) {
                $records[] = $row;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pasien - NeoHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-white border-b border-slate-200 py-4 px-8 flex justify-between items-center shadow-sm">
        <a href="index.php" class="text-xl font-black text-blue-600 tracking-tight">NeoHMS</a>
        <a href="index.php" class="text-sm font-bold text-slate-500 hover:text-blue-600 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Kembali
        </a>
    </nav>

    <main class="max-w-4xl mx-auto py-12 px-6">
        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-2xl mb-8 shadow-sm animate-pulse">
                <h3 class="text-red-800 font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    Critical Database Error
                </h3>
                <p class="text-red-700 text-sm mt-2 font-mono break-all"><?= $error ?></p>
            </div>
        <?php endif; ?>

        <?php if ($patient): ?>
            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-slate-100">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-10 text-white">
                    <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-2">Profil Pasien (#<?= $patient['id'] ?>)</p>
                    <h2 class="text-4xl font-black"><?= htmlspecialchars($patient['name']) ?></h2>
                    <div class="flex flex-wrap gap-6 mt-6">
                        <div class="flex items-center gap-2 text-sm font-medium text-blue-50">
                            <svg class="w-4 h-4 opacity-70" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                            Tgl Lahir: <?= $patient['birthdate'] ?>
                        </div>
                        <div class="flex items-center gap-2 text-sm font-medium text-blue-50">
                            <svg class="w-4 h-4 opacity-70" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path></svg>
                            NIK: <?= $patient['nik'] ?>
                        </div>
                    </div>
                </div>

                <div class="p-10">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Rekam Medis Terakhir
                    </h3>
                    
                    <div class="space-y-6">
                        <?php foreach ($records as $rec): ?>
                            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Diagnosa</span>
                                    <span class="text-[10px] font-bold text-slate-400 italic">Oleh: <?= htmlspecialchars($rec['doctor_name']) ?></span>
                                </div>
                                <h4 class="text-xl font-bold text-slate-800 mb-2"><?= htmlspecialchars($rec['diagnosis']) ?></h4>
                                <p class="text-slate-600 text-sm leading-relaxed mb-4"><?= htmlspecialchars($rec['treatment']) ?></p>
                                <div class="pt-4 border-t border-slate-200">
                                    <p class="text-xs text-slate-500 font-medium">Catatan Dokter: <span class="italic font-normal"><?= htmlspecialchars($rec['notes']) ?></span></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($records)): ?>
                            <p class="text-slate-400 text-sm italic">Tidak ada catatan medis yang ditemukan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
             <div class="text-center py-20 bg-white rounded-[2.5rem] shadow-sm border border-slate-100">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-slate-800 font-bold text-xl">Pasien tidak ditemukan</h3>
                <p class="text-slate-500 text-sm mt-2">Silakan periksa kembali ID pasien yang Anda masukkan.</p>
            </div>
        <?php endif; ?>

        <!-- Educational hint -->
        <div class="mt-20 p-8 bg-blue-50 border border-blue-100 rounded-[2rem] relative overflow-hidden group">
             <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-200 rounded-full blur-xl opacity-30 group-hover:scale-150 transition-transform duration-700"></div>
            <h4 class="text-blue-800 font-bold mb-4 flex items-center gap-2 tracking-tight">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Lab Security: SQL Injection Discovery
            </h4>
            <div class="space-y-3">
                <p class="text-xs text-blue-700 leading-relaxed italic">
                    Modul ini memiliki parameter <code class="bg-blue-100 px-1 rounded">id</code> yang rentan terhadap <strong>Error-Based</strong> dan <strong>Union-Based SQLi</strong>. 
                </p>
                <div class="bg-white/50 p-4 rounded-xl text-[10px] font-mono text-blue-600 border border-blue-200">
                    Payload Test (Error): patient.php?id=1' <br>
                    Payload Test (Union): patient.php?id=1' UNION SELECT 1,2,3,4,5-- -
                </div>
            </div>
        </div>
    </main>
</body>
</html>
