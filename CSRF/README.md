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
| Username | Password | Nomor Rekening | PIN | Deskripsi |
| :--- | :--- | :--- | :--- | :--- |
| `victim` | `victim123` | `1234567890` | `123456` | Target serangan (Saldo Besar) |
| `attacker` | `attacker123` | `0987654321` | `654321` | Penerima dana curian |
| `budi`, `siti` | `...` | `...` | `...` | User lain (Kontak Transfer) |

## 🧪 Skenario Serangan

### Skenario 1: Transfer Saldo Otomatis (Bypass PIN)
1.  **Login sebagai Victim**: Buka `http://localhost:8000` dan login dengan akun victim.
2.  **Pancing Korban**: Penyerang membuat halaman jebakan `attacker/transfer.html` yang berisi form tersembunyi dengan parameter `bypass_pin=1`.
3.  **Eksekusi**: Saat Victim membuka halaman tersebut, browser akan otomatis mengirim POST ke `transfer.php`.
4.  **Hasil**: Cek dashboard Victim, saldo akan berkurang Rp 5.000.000 tanpa Victim memasukkan PIN!

### Skenario 2: Privilege Escalation (Account Takeover)
*(Masih tersedia, memicu perubahan password tanpa token).*
1. Pastikan masih login sebagai `victim`.
2. Akses halaman jebakan: `http://localhost:8000/attacker/profile.html`.
3. Akun victim akan terambil alih dengan password baru dari attacker.

## 🛡️ Bagaimana Cara Mencegahnya?
Untuk memperbaiki kerentanan ini, Anda harus mengimplementasikan:
1. **CSRF Tokens**: Gunakan token unik dan acak pada setiap form POST yang divalidasi oleh server.
2. **SameSite Cookie Attribute**: Setel atribut cookie menjadi `Strict` atau `Lax`.
3. **Re-authentication**: Minta password lama saat melakukan penggantian password atau konfirmasi 2FA untuk transaksi sensitif.

---
**⚠️ Peringatan:** Proyek ini hanya untuk tujuan edukasi keamanan siber. Jangan gunakan teknik ini pada situs web yang bukan milik Anda tanpa izin.
