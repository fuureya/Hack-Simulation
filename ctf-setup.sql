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
('xss', 'hard', 'DOM-based XSS', 'Eksekusi kode JS melalui manipulasi DOM pada URL.', 'FLAG{DOM_XSS_M4n1pul471on_V1a_URL}', 300, 'Periksa bagaimana fragment URL (#) diproses oleh script.');

-- Add more for other labs...
