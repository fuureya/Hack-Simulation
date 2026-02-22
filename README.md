# LabSec Manager - Security Lab Simulation 🛡️

Selamat datang di proyek simulasi keamanan OWASP Top 10! Proyek ini dirancang untuk simulasi serangan siber di lingkungan yang aman dan terisolasi menggunakan Docker.

## 🛠️ Persiapan Awal (Setup)

Gunakan panduan di bawah ini sesuai dengan Sistem Operasi (OS) yang Anda gunakan agar proyek berjalan tanpa kendala.

### 🐧 Linux (Ubuntu/Debian/Arch)

Jika Anda mengalami error `permission denied` saat menjalankan Docker, ikuti langkah ini:

1. **Jalankan Script Automasi**:
   ```bash
   chmod +x init-setup.sh
   ./init-setup.sh
   ```
2. **Login Ulang**: Anda **WAJIB** melakukan logout dan login kembali ke komputer Anda agar perubahan izin (group docker) berlaku.
3. **Mulai Proyek**:
   ```bash
   sudo docker-compose up -d --build
   ```

### 🪟 Windows

Sangat disarankan menggunakan **Docker Desktop** dengan backend **WSL2**.

1. Install [Docker Desktop for Windows](https://docs.docker.com/desktop/install/windows-install/).
2. Pastikan fitur **WSL2** sudah aktif di pengaturan Docker Desktop.
3. Buka terminal (PowerShell atau Git Bash) di folder ini.
4. Jalankan:
   ```bash
   docker-compose up -d --build
   ```
   _(Di Windows, Anda biasanya tidak perlu menggunakan 'sudo')_

### 🍎 macOS

1. Install [Docker Desktop for Mac](https://docs.docker.com/desktop/install/mac-install/).
2. Jalankan Docker Desktop.
3. Buka Terminal dan jalankan:
   ```bash
   docker-compose up -d --build
   ```

---

## 🚀 Cara Menjalankan Lab

Setelah container utama berjalan, Anda bisa mengakses dashboard:

- **Dashboard URL**: `http://localhost:9000`
- **Username/Password**: `admin` / `admin`

### Menjalankan Lab Spesifik

Dari dashboard, Anda dapat mengaktifkan lab satu per satu:

- **CSRF Lab**: Port 8888
- **XSS Lab**: Port 8001
- **SQL Injection Lab**: Port 8002
- ... (dan lab OWASP lainnya)

---

## 🏗️ Struktur Proyek

Proyek ini menggunakan arsitektur **Shared MariaDB**:

- **`labsec-db`**: Database pusat untuk semua lab.
- **`labsec-dashboard`**: Web portal untuk mengatur state lab.
- **Lab Folders**: Setiap folder (XSS, SQLInjection, dll) berisi kode aplikasi yang sengaja dibuat rentan.

---

## 🔧 Troubleshooting (Kendala Umum)

| Error                                        | Solusi                                                                     |
| -------------------------------------------- | -------------------------------------------------------------------------- |
| `permission denied ... /var/run/docker.sock` | Jalankan `./init-setup.sh` dan **logout-login** kembali (khusus Linux).    |
| `Port already in use`                        | Matikan aplikasi lain yang menggunakan port tersebut (9000 atau port lab). |
| `Database connection error`                  | Tunggu 15 detik setelah start agar database siap sepenuhnya.               |

---

## 📚 Dokumentasi Kerentanan

Daftar lab yang tersedia (Tingkat kesulitan tersembunyi):

- [CSRF Simulation](CSRF/README.md)
- [XSS Simulation](XSS/README.md)
- [SQL Injection Simulation](SqlInjection/README.md)
- [Auth Failure Simulation](AuthFail/README.md)
- ... dan 9 lab lainnya!

---

**⚠️ Disclaimer**: Proyek ini hanya untuk tujuan edukasi.
