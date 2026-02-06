# Simulasi Kerentanan CSRF - NeoBank (BCA Style) 🏦

Proyek ini adalah simulasi kerentanan **Cross-Site Request Forgery (CSRF)** pada aplikasi perbankan modern (NeoBank) yang memiliki tampilan dan alur mirip dengan aplikasi **myBCA**.

## 🚀 Persiapan dengan Docker (Cara Cepat)
Gunakan Docker untuk menjalankan simulasi tanpa perlu install PHP/Database secara manual:

1.  **Masuk ke folder proyek**:
    ```bash
    cd CSRF
    ```
2.  **Jalankan Docker Compose**:
    ```bash
    docker-compose up -d
    ```
    *Perintah ini akan membangun image, menjalankan kontainer pada port 8000, dan menginisialisasi database secara otomatis.*

3.  **Akses Aplikasi**:
    Buka [http://localhost:8000](http://localhost:8000) di browser Anda.

4.  **Reset Database (Jika Perlu)**:
    Jika mas ingin mereset data ke kondisi awal, akses: [http://localhost:8000/setup.php](http://localhost:8000/setup.php).

---

## 👥 Akun untuk Simulasi
| Role | Username | Password | No. Rekening | PIN |
| :--- | :--- | :--- | :--- | :--- |
| **Victim** | `victim` | `victim123` | `1234567890` | `123456` |
| **Attacker**| `attacker` | `attacker123` | `0987654321` | `654321` |

---

## 🧪 Skenario Serangan CSRF

### Skenario 1: Transfer Saldo Otomatis (Bypass PIN)
Dalam simulasi ini, penyerang memanfaatkan celah di mana server tidak memvalidasi token CSRF dan memiliki kelemahan logika (bypass PIN via parameter).

1.  **Login sebagai Victim**: Buka aplikasi di tab baru dan login sebagai `victim`.
2.  **Simulasi Pancingan**: Buka tab lain (di browser yang sama) dan akses halaman jebakan penyerang:
    `http://localhost:8000/attacker/transfer.html`
3.  **Halaman Jahat**: Halaman tersebut berisi form tersembunyi yang akan mengirimkan perintah transfer Rp 5.000.000 ke rekening Attacker secara otomatis saat halaman dibuka.
4.  **Hasil**: Kembali ke dashboard Victim dan **refresh**. Saldo akan berkurang secara ajaib tanpa Victim memasukkan PIN atau menekan tombol transfer!

### Skenario 2: Account Takeover (Ganti Password)
1.  Pastikan masih dalam kondisi login sebagai `victim`.
2.  Akses halaman jebakan kedua: `http://localhost:8000/attacker/profile.html`.
3.  Password akun `victim` akan berubah otomatis menjadi `hacked123`.

---

## 🛡️ Cara Mencegah CSRF
Untuk mengamankan aplikasi dari serangan ini, pengembang harus:
1.  **Anti-CSRF Tokens**: Menambahkan token unik pada setiap form.
2.  **SameSite Cookie**: Mengatur cookie ke `SameSite=Lax` atau `Strict`.
3.  **Validasi OTP/PIN yang Ketat**: Memastikan pengecekan otentikasi tambahan (seperti PIN) tidak bisa di-bypass lewat parameter apapun.

---
**⚠️ Peringatan:** Proyek ini hanya untuk edukasi. Jangan digunakan untuk kegiatan ilegal!
