# LabSec Manager - Security Lab Dashboard 🛡️

Dashboard terpusat untuk mengelola semua lab simulasi keamanan (CSRF, XSS, SQL Injection) dengan fitur start/stop container dan shared MariaDB.

## 🚀 Quick Start

### 1. Start Dashboard & Shared Database
```bash
cd /home/linux/Documents/Projects/hacker-simulation
sudo docker-compose up -d --build
```

Tunggu sekitar 15-20 detik untuk MariaDB fully ready.

### 2. Access Dashboard
- **URL**: `http://localhost:9000`
- **Username**: `admin`
- **Password**: `admin`

### 3. Start Individual Labs
Dari dashboard, klik tombol **Start** pada lab yang ingin dijalankan:
- **CSRF Lab** (NeoBank) - Port 8888
- **XSS Lab** (CinemaX) - Port 8001
- **SQL Injection Lab** (NeoHMS) - Port 8002

Setelah status berubah menjadi "Running", klik tombol **Open Lab** untuk akses.

---

## 🏗️ Architecture

### Shared Infrastructure
- **MariaDB** (`labsec-db`): Database terpusat untuk semua lab
  - Port: 3307 (external)
  - Root Password: `labsec_root_2026`
  - Databases:
    - `labsec_dashboard` - Dashboard sessions
    - `neobank_csrf` - CSRF Lab
    - `cinemax_xss` - XSS Lab
    - `neohms_sqli` - SQL Injection Lab

- **Network** (`labsec-network`): Bridge network untuk komunikasi antar container

### Lab Containers
Setiap lab berjalan di container terpisah dan connect ke shared MariaDB:

| Lab | Container Name | Port | Database |
|-----|----------------|------|----------|
| CSRF | `neobank-csrf-container` | 8888 | `neobank_csrf` |
| XSS | `cinemax-xss-container` | 8001 | `cinemax_xss` |
| SQLi | `neohms-sqli-container` | 8002 | `neohms_sqli` |

---

## 📋 Management Commands

### Start All Labs at Once
```bash
cd CSRF && sudo docker-compose up -d
cd ../XSS && sudo docker-compose up -d
cd ../SqlInjection && sudo docker-compose up -d
```

### Stop All Labs
```bash
sudo docker stop neobank-csrf-container cinemax-xss-container neohms-sqli-container
```

### View Logs
```bash
# Dashboard logs
sudo docker logs -f labsec-dashboard

# Specific lab logs
sudo docker logs -f neobank-csrf-container
```

### Reset Database
```bash
# Access specific lab setup
http://localhost:8888/setup.php  # CSRF
http://localhost:8001/setup.php  # XSS
http://localhost:8002/setup.php  # SQLi
```

---

## 🔧 Troubleshooting

### Lab tidak bisa start dari dashboard
1. Pastikan Docker daemon running
2. Check container logs: `sudo docker logs <container-name>`
3. Restart dashboard: `sudo docker-compose restart labsec-dashboard`

### Database connection error
1. Pastikan MariaDB container running: `sudo docker ps | grep labsec-mariadb`
2. Check MariaDB logs: `sudo docker logs labsec-mariadb`
3. Tunggu beberapa detik untuk healthcheck pass

### Port sudah digunakan
Jika ada conflict port, edit `docker-compose.yml` di folder masing-masing lab dan ganti port mapping.

---

## 📚 Lab Documentation
Setiap lab memiliki README.md sendiri dengan detail skenario serangan:
- [CSRF/README.md](CSRF/README.md) - NeoBank CSRF Simulation
- [XSS/README.md](XSS/README.md) - CinemaX XSS Simulation
- [SqlInjection/README.md](SqlInjection/README.md) - NeoHMS SQL Injection Simulation

---

**⚠️ Disclaimer**: Semua lab ini untuk tujuan edukasi keamanan siber. Jangan gunakan teknik ini pada sistem yang bukan milik Anda tanpa izin.
