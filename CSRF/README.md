# Simulasi Kerentanan CSRF - NeoBank

Proyek ini adalah simulasi sederhana bagaimana kerentanan **Cross-Site Request Forgery (CSRF)** bekerja pada aplikasi web perbankan. Simulasi ini mencakup skenario transfer saldo otomatis dan pengambilalihan akun (Privilege Escalation) melalui penggantian password.

## 🚀 Persyaratan
- PHP 7.4 ke atas
- Ekstensi SQLite3 (aktif secara default di kebanyakan instalasi PHP)

## 🛠️ Instalasi & Persiapan
### Metode 1: PHP Local (Manual)
1. Masuk ke folder proyek:
   ```bash
   cd CSRF
   ```
2. Jalankan setup database:
   ```bash
   php setup.php
   ```
3. Jalankan server lokal PHP:
   ```bash
   php -S localhost:8000
   ```

### Metode 2: Docker Compose (Rekomendasi)
1. Masuk ke folder proyek:
   ```bash
   cd CSRF
   ```
2. Jalankan Docker Compose:
   ```bash
   docker-compose up -d
   ```
   *Ini akan membangun image, menjalankan kontainer, dan langsung menginisialisasi database secara otomatis.*
3. Akses aplikasi di `http://localhost:8000`.


## 👥 Akun untuk Simulasi
| Username | Password | Deskripsi |
| :--- | :--- | :--- |
| `victim` | `victim123` | Target serangan (Saldo Awal: $1000) |
| `attacker` | `attacker123` | Pengirim jebakan CSRF |

## 🧪 Skenario Serangan

### Skenario 1: Transfer Saldo Otomatis
1. Buka browser dan login sebagai `victim` di `http://localhost:8000`.
2. Buka tab baru di browser yang sama dan akses halaman jebakan: `http://localhost:8000/attacker/transfer.html`.
3. Tunggu 2 detik hingga form dikirim secara otomatis.
4. Kembali ke halaman dashboard `victim` dan **refresh**. Anda akan melihat saldo berkurang tanpa adanya konfirmasi dari Anda!

### Skenario 2: Privilege Escalation (Account Takeover)
1. Pastikan Anda masih login sebagai `victim`.
2. Akses halaman jebakan kedua: `http://localhost:8000/attacker/profile.html`.
3. Tunggu hingga proses selesai.
4. Coba logout dan login kembali sebagai `victim` menggunakan password lama (`victim123`). Anda akan gagal!
5. Gunakan password baru yang disisipkan attacker: `hacked123`. Anda berhasil login! Ini adalah simulasi pengambilalihan akun.

## 🛡️ Bagaimana Cara Mencegahnya?
Untuk memperbaiki kerentanan ini, Anda harus mengimplementasikan:
1. **CSRF Tokens**: Gunakan token unik dan acak pada setiap form POST yang divalidasi oleh server.
2. **SameSite Cookie Attribute**: Setel atribut cookie menjadi `Strict` atau `Lax`.
3. **Re-authentication**: Minta password lama saat melakukan penggantian password atau konfirmasi 2FA untuk transaksi sensitif.

---
**⚠️ Peringatan:** Proyek ini hanya untuk tujuan edukasi keamanan siber. Jangan gunakan teknik ini pada situs web yang bukan milik Anda tanpa izin.
