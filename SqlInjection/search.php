<?php
require_once 'db.php';

$name = $_GET['name'] ?? '';
$results = [];

if ($name) {
    // VULNERABLE: Time-Based Blind SQLi (Sengaja menggunakan query mentah)
    // Karakteristik: Hasil pencarian bisa kosong, cocok untuk SLEEP()
    $query = "SELECT * FROM patients WHERE name LIKE '%$name%'";
    
    // Eksekusi query tanpa error handling yang ditampilkan ke user
    $res = mysqli_query($conn, $query);
    if ($res) {
        $results = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Pasien - NeoHMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f1f5f9] min-h-screen">
    <nav class="bg-white border-b border-slate-200 py-4 px-8 flex justify-between items-center shadow-sm">
        <a href="index.php" class="text-xl font-black text-blue-600 tracking-tight">NeoHMS</a>
        <a href="index.php" class="text-sm font-bold text-slate-500 hover:text-blue-600 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Beranda
        </a>
    </nav>

    <main class="max-w-4xl mx-auto py-12 px-6">
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-black text-slate-900 mb-4">Cari Database Pasien</h1>
            <form action="" method="GET" class="relative max-w-xl mx-auto group">
                <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Masukkan nama pasien..." 
                    class="w-full bg-white border border-slate-200 rounded-2xl py-5 px-8 pr-16 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all shadow-xl shadow-blue-500/5">
                <button type="submit" class="absolute right-6 top-5 text-slate-400 group-focus-within:text-blue-600">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        <?php if ($name): ?>
            <div class="space-y-4">
                <p class="text-sm font-medium text-slate-500">Menampilkan hasil untuk: <span class="text-slate-900 font-bold"><?= htmlspecialchars($name) ?></span></p>
                
                <?php if (empty($results)): ?>
                    <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center shadow-sm">
                         <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                             <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                         </div>
                         <h3 class="font-bold text-slate-700">Hasil pencarian kosong</h3>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($results as $p): ?>
                            <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-100/50 rounded-2xl flex items-center justify-center text-blue-600 font-bold">
                                        <?= strtoupper(substr($p['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900"><?= htmlspecialchars($p['name']) ?></h4>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?= $p['birthdate'] ?></p>
                                    </div>
                                </div>
                                <a href="patient.php?id=<?= $p['id'] ?>" class="p-3 bg-slate-100 rounded-xl text-slate-400 hover:bg-blue-600 hover:text-white transition-all shadow-inner group-hover:bg-blue-600 group-hover:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="mt-20 p-8 bg-white border border-slate-200 rounded-[2.5rem] shadow-sm">
            <h4 class="text-indigo-600 font-bold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Lab Security: Time-Based Extraction
            </h4>
            <p class="text-xs text-slate-500 leading-relaxed italic mb-4">
                Halaman pencarian ini rentan terhadap <strong>Time-Based Blind SQLi</strong>. Jika query Anda menyertakan fungsi <code class="bg-slate-100 px-1 rounded">SLEEP()</code>, halaman akan terhenti loading-nya selama waktu yang ditentukan.
            </p>
            <div class="bg-slate-50 p-4 rounded-xl text-[10px] font-mono text-indigo-500 border border-slate-100">
                Payload Demo: search.php?name=Apapun' AND SLEEP(5)-- -
            </div>
        </div>
    </main>
</body>
</html>
