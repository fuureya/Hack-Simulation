<?php
// Core logic for Nebula Webhook Engine (Stateless Execution)
$result = '';
$requestedUrl = '';
$error = '';
$responseHeaders = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');
    $requestedUrl = $url;

    if (empty($url)) {
        $error = "Endpoint destination cannot be null.";
    } else {
        // Core fetch logic (Maintained for legacy compatibility)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NebulaEngine/2.1 (Stateless Ingestion)');

        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $error = "Ingestion error: " . $curlError;
        } else {
            $responseHeaders = substr($response, 0, $headerSize);
            $result = substr($response, $headerSize);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webhook Manager | Nebula Cloud</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #030014;
            color: #a5b4fc;
        }

        .glass {
            background: rgba(10, 10, 30, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(139, 92, 246, 0.2);
        }

        .glow-violet {
            box-shadow: 0 0 30px rgba(139, 92, 246, 0.1);
        }

        .text-glow {
            text-shadow: 0 0 10px rgba(139, 92, 246, 0.5);
        }
    </style>
</head>

<body class="min-h-screen py-12 px-6 flex flex-col items-center">

    <div class="max-w-[1000px] w-full grid grid-cols-1 lg:grid-cols-5 gap-8">

        <!-- Controls Column -->
        <div class="lg:col-span-2 space-y-8">
            <div class="logo flex items-center gap-4 mb-8">
                <div
                    class="w-12 h-12 bg-violet-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-violet-600/20">
                    N</div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-widest uppercase italic">Nebula<span
                            class="text-violet-500">Cloud</span></h1>
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.4em]">Webhook Engine v2.1</p>
                </div>
            </div>

            <div class="glass rounded-[2rem] p-8 glow-violet">
                <h2 class="text-xs font-black text-violet-400 uppercase tracking-widest mb-6">Execution Config</h2>

                <form method="POST" class="space-y-6">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4 ml-1">Destination
                            Presets</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="fillUrl('http://localhost/')"
                                class="py-2 px-3 bg-violet-500/5 hover:bg-violet-500/10 border border-violet-500/10 rounded-xl text-[10px] font-bold text-violet-300 transition-all">Node
                                Loopback</button>
                            <button type="button" onclick="fillUrl('http://169.254.169.254/latest/meta-data/')"
                                class="py-2 px-3 bg-violet-500/5 hover:bg-violet-500/10 border border-violet-500/10 rounded-xl text-[10px] font-bold text-violet-300 transition-all">Orchestrator
                                Meta</button>
                            <button type="button" onclick="fillUrl('http://nebula-db:3306/')"
                                class="py-2 px-3 bg-violet-500/5 hover:bg-violet-500/10 border border-violet-500/10 rounded-xl text-[10px] font-bold text-violet-300 transition-all">Relay
                                DB</button>
                            <button type="button" onclick="fillUrl('file:///etc/hosts')"
                                class="py-2 px-3 bg-violet-500/5 hover:bg-violet-500/10 border border-violet-500/10 rounded-xl text-[10px] font-bold text-violet-300 transition-all">Local
                                Mapping</button>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 ml-1">Webhook
                            Endpoint</label>
                        <input type="text" id="urlInput" name="url" required
                            class="w-full bg-slate-950 border border-violet-900/30 rounded-2xl p-4 text-sm font-bold text-white focus:outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-500/5 transition-all outline-none"
                            placeholder="https://..." value="<?= htmlspecialchars($requestedUrl) ?>">
                    </div>

                    <button type="submit"
                        class="w-full bg-violet-600 hover:bg-violet-500 py-4 rounded-2xl text-white font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-violet-600/20 transition-all active:scale-[0.98]">
                        Execute Webhook
                    </button>
                </form>

                <?php if ($error): ?>
                    <div
                        class="mt-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <?= $error ?>
                    </div>
                <?php endif; ?>
            </div>

            <div
                class="p-4 rounded-2xl border border-slate-800/50 text-[10px] text-slate-600 leading-relaxed uppercase tracking-tighter">
                Nebula Cloud employs a stateless proxy for all outgoing webhook requests. Execution logs are stored in
                the orchestrator node for 7 days.
            </div>
        </div>

        <!-- Output Column -->
        <div class="lg:col-span-3">
            <div class="glass rounded-[2rem] p-8 h-full flex flex-col glow-violet">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xs font-black text-violet-400 uppercase tracking-widest">Execution Output</h2>
                    <div class="flex gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Relay
                            Ready</span>
                    </div>
                </div>

                <?php if ($result || $responseHeaders): ?>
                    <div class="space-y-6 flex-1 flex flex-col overflow-hidden">
                        <div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Response Headers
                            </p>
                            <div
                                class="bg-black/40 p-4 rounded-xl border border-violet-900/20 font-mono text-[9px] text-violet-400/70 overflow-y-auto max-h-32">
                                <?= nl2br(htmlspecialchars($responseHeaders)) ?>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col overflow-hidden">
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Payload Data</p>
                            <div
                                class="bg-black/40 p-5 rounded-xl border border-violet-900/20 font-mono text-[11px] text-blue-300/80 overflow-y-auto custom-scrollbar flex-1 leading-relaxed">
                                <?= htmlspecialchars(mb_substr($result, 0, 5000)) ?>
                                <?= (strlen($result) > 5000) ? "\n\n[...truncated " . strlen($result) . " bytes total...]" : '' ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex-1 flex flex-col items-center justify-center text-center opacity-30">
                        <svg class="w-16 h-16 text-slate-800 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <p class="text-sm font-bold text-slate-700 uppercase tracking-widest">Ready for Execution</p>
                        <p class="text-[10px] text-slate-800 mt-2">Initialize a webhook destination for trace analysis.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function fillUrl(url) {
            document.getElementById('urlInput').value = url;
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4c1d95;
            border-radius: 10px;
        }
    </style>
</body>

</html>