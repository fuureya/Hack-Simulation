-- Create CTF Database
CREATE DATABASE IF NOT EXISTS labsec_ctf;
USE labsec_ctf;

-- Table for challenges
CREATE TABLE IF NOT EXISTS challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_id VARCHAR(50) NOT NULL,
    level ENUM('easy', 'medium', 'hard') NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    flag VARCHAR(255) NOT NULL,
    points INT DEFAULT 100,
    hint TEXT
);

-- Table for user progress (simulated for admin)
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    challenge_id INT NOT NULL,
    submitted_flag VARCHAR(255),
    is_correct BOOLEAN DEFAULT FALSE,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (challenge_id) REFERENCES challenges(id)
);

-- Seed Initial Challenges (Flags are examples, will be integrated into labs)
INSERT INTO challenges (lab_id, level, title, description, flag, points, hint) VALUES
('authfail', 'easy', 'Session Hijacking', 'Temukan session ID milik user Alice dan akses dashboard-nya.', 'FLAG{S3ssion_F1xation_1s_D4ng3rous}', 100, 'Lihat URL parameter saat login.'),
('authfail', 'medium', 'Predictable Token', 'Gunakan kelemahan pada fitur "Remember Me" untuk masuk sebagai Bob.', 'FLAG{Pr3d1ct4bl3_R3m3mb3r_M3}', 200, 'Token menggunakan format base64(user_id:timestamp).'),
('authfail', 'hard', 'Admin Takeover', 'Dapatkan akses ke akun Admin dengan menebak password atau bypass mekanisme autentikasi.', 'FLAG{Adm1n_P4ssw0rd_Bru73_F0rc3d}', 300, 'Gunakan wordlist umum untuk menebak password admin.'),

('sqli', 'easy', 'Simple Bypass', 'Bypass form login menggunakan SQL Injection sederhana.', 'FLAG{SQL1_L0g1n_Byp4ss_Succ3ss}', 100, 'Coba gunakan or 1=1--'),
('sqli', 'medium', 'Data Extraction', 'Ekstrak informasi sensitif dari database pasien.', 'FLAG{SQL1_D4t4_Exf1ltr4t1on}', 200, 'Gunakan UNION SELECT untuk mengambil data.'),
('sqli', 'hard', 'Blind SQLi', 'Temukan versi database menggunakan teknik Blind SQL Injection.', 'FLAG{Bl1nd_SQL1_M4st3r_D0n3}', 300, 'Gunakan fungsi sleep() atau benchmark() untuk mendeteksi respons.'),

('xss', 'easy', 'Reflected XSS', 'Jalankan skrip alert pada halaman pencarian.', 'FLAG{R3fl3ct3d_XSS_4l3rt_Ok}', 100, 'Masukkan skrip <script>alert(1)</script>'),
('xss', 'medium', 'Stored XSS', 'Tinggalkan pesan berbisa pada kolom review yang akan dieksekusi oleh admin.', 'FLAG{S70r3d_XSS_C00k13_S734l}', 200, 'Coba curi cookie admin menggunakan skrip JS.'),
('xss', 'hard', 'DOM-based XSS', 'Eksekusi kode JS melalui manipulasi DOM pada URL.', 'FLAG{DOM_XSS_M4n1pul471on_V1a_URL}', 300, 'Periksa bagaimana fragment URL (#) diproses oleh script.'),

-- Broken Access Control (bac)
('bac', 'easy', 'Admin Panel Access', 'Temukan cara mengakses halaman admin tanpa kredensial yang valid.', 'FLAG{B4C_Adm1n_Acc3ss_Unl0ck3d}', 100, 'Coba periksa file robots.txt atau struktur URL.'),
('bac', 'medium', 'Privilege Escalation', 'Ubah role user biasa menjadi admin melalui manipulasi request.', 'FLAG{B4C_Pr1v1l3g3_Esc4l471on}', 200, 'Periksa parameter role di request POST saat update profil.'),
('bac', 'hard', 'Horizontal Escalation', 'Akses data milik user lain dengan memanipulasi ID session atau parameter.', 'FLAG{B4C_H0r1z0n74l_M0v3m3n7}', 300, 'Gunakan Burp Suite untuk melihat perbedaan request antar user.'),

-- IDOR
('idor', 'easy', 'Ticket Browsing', 'Lihat tiket dukungan milik orang lain dengan mengubah ID di URL.', 'FLAG{ID0R_71ck37_Sno0p1ng}', 100, 'Ubah parameter id=1 menjadi id=2.'),
('idor', 'medium', 'Invoice Download', 'Unduh invoice PDF milik perusahaan lain.', 'FLAG{ID0R_Inv01c3_D0wnl04d}', 200, 'Prediksi nama file invoice di folder uploads.'),
('idor', 'hard', 'Mass Assignment', 'Update data sensitif user lain menggunakan IDOR pada API update.', 'FLAG{ID0R_M4ss_4ss1gnm3n7}', 300, 'Coba tambahkan field json baru di request PUT.'),

-- Crypto Failures (cryptofail)
('cryptofail', 'easy', 'MD5 Cracking', 'Dekripsi password admin yang hanya menggunakan MD5 tanpa salt.', 'FLAG{Cr1p70_MD5_Is_Br0k3n}', 100, 'Gunakan online MD5 cracker.'),
('cryptofail', 'medium', 'Sensitive Cookie', 'Temukan data sensitif yang disimpan dalam plaintext di dalam cookie.', 'FLAG{Cr1p70_Pl41n73x7_C00k13}', 200, 'Periksa tab Application di DevTools browser.'),
('cryptofail', 'hard', 'Insecure Algorithm', 'Eksploitasi penggunaan algoritma enkripsi yang lemah (misal: ROT13 atau XOR sederhana).', 'FLAG{Cr1p70_W34k_4lg0r17hm}', 300, 'Analisis pola ciphertext yang dihasilkan.'),

-- Insecure Design (insecuredesign)
('insecuredesign', 'easy', 'Security Question Bypass', 'Bypass pertanyaan keamanan yang terlalu mudah ditebak.', 'FLAG{D3s1gn_W34k_Qu3s71on}', 100, 'Jawaban biasanya ada di profil publik.'),
('insecuredesign', 'medium', 'Logic Error', 'Lakukan checkout belanja dengan harga 0 atau minus.', 'FLAG{D3s1gn_L0g1c_Byp4ss}', 200, 'Ubah parameter price di request sebelum submit.'),
('insecuredesign', 'hard', 'Race Condition', 'Eksploitasi celah race condition pada sistem penarikan saldo.', 'FLAG{D3s1gn_R4c3_C0nd171on}', 300, 'Kirim request secara bersamaan (multi-threading).'),

-- Security Misconfiguration (secmisconfig)
('secmisconfig', 'easy', 'Directory Listing', 'Temukan file rahasia melalui fitur directory listing yang aktif.', 'FLAG{M1sc0nf1g_D1r_L1s71ng}', 100, 'Coba buka folder /backup atau /config.'),
('secmisconfig', 'medium', 'Default Credentials', 'Masuk ke panel admin menggunakan username/password default.', 'FLAG{M1sc0nf1g_D3f4ul7_Cr3ds}', 200, 'Cek dokumentasi developer untuk default password.'),
('secmisconfig', 'hard', 'Public .env', 'Baca file konfigurasi .env yang terekspos ke publik.', 'FLAG{M1sc0nf1g_Ennv_L34k4g3}', 300, 'Coba akses langsung /.env di URL.'),

-- Logging Failures (loggingfail)
('loggingfail', 'easy', 'Public Log Files', 'Temukan file log aplikasi yang tersimpan di direktori publik.', 'FLAG{L0gg1ng_Publ1c_L0gs}', 100, 'Cek folder /logs/app.log'),
('loggingfail', 'medium', 'Log Injection', 'Masukkan data palsu ke dalam log untuk mengelabui admin.', 'FLAG{L0gg1ng_Inj3c71on_V1a_UA}', 200, 'Manipulasi User-Agent untuk memasukkan karakter newline.'),
('loggingfail', 'hard', 'Sensitive Data in Logs', 'Temukan kredensial user yang secara tidak sengaja tercatat di log.', 'FLAG{L0gg1ng_S3ns171v3_D474}', 300, 'Cari keyword "password" atau "token" di file log.'),

-- SSRF (ssrf)
('ssrf', 'easy', 'Internal Scan', 'Gunakan server untuk melakukan scan port pada localhost.', 'FLAG{SSRF_L0c4lh0s7_Sc4n}', 100, 'Gunakan fileter http://localhost:22'),
('ssrf', 'medium', 'Cloud Metadata', 'Akses metadata instance cloud melalui celah SSRF.', 'FLAG{SSRF_Cl0ud_M374d474}', 200, 'Gunakan IP 169.254.169.254'),
('ssrf', 'hard', 'File Read', 'Baca file /etc/passwd menggunakan wrapper file://.', 'FLAG{SSRF_F1l3_R34d_Succ3ss}', 300, 'Gunakan wrapper file:///etc/passwd'),

-- Insecure Component (insecurelibrary)
('insecurelibrary', 'easy', 'Known CVE', 'Eksploitasi library yang memiliki kerentanan publik yang sudah diketahui.', 'FLAG{L1br4ry_Old_V3rs1on}', 100, 'Cek versi library di file composer.json atau package.json.'),
('insecurelibrary', 'medium', 'PHP Object Injection', 'Eksploitasi fungsi unserialize() pada library yang tidak aman.', 'FLAG{L1br4ry_PHP_Obj_Inj}', 200, 'Ciptakan payload serialized object.'),
('insecurelibrary', 'hard', 'RCE via Component', 'Dapatkan shell remote melalui eksploitasi celah di library.', 'FLAG{L1br4ry_RCE_W3bsh3ll}', 300, 'Gunakan exploit script yang sesuai dengan CVE.'),

-- Integrity Failure (integrityfail)
('integrityfail', 'easy', 'Trusting Input', 'Ubah data yang dikirim melalui hidden field tanpa validasi integritas.', 'FLAG{In73gr17y_H1dd3n_F13ld}', 100, 'Ubah value input type="hidden"'),
('integrityfail', 'medium', 'Cookie Manipulation', 'Ubah data session yang disimpan di client-side tanpa signature (HMAC).', 'FLAG{In73gr17y_PHP_S3ss1on}', 200, 'Base64 decode cookie, ubah datanya, lalu encode kembali.'),
('integrityfail', 'hard', 'Software Update Bypass', 'Inject file jahat ke dalam mekanisme update otomatis.', 'FLAG{In73gr17y_Upd473_Inj3c7}', 300, 'Manipulasi server update URL melalui hosts file atau DNS.');

-- Add more for other labs...
