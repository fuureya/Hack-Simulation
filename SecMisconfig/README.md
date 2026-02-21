# ⚙️ SecMisconfig Lab — DevOps Portal

**OWASP A05: Security Misconfiguration**

## Skenario

DevOps Portal adalah dashboard internal dengan konfigurasi yang buruk — berbagai file sensitif dapat diakses publik, default credentials tidak diubah, dan verbose error bocorkan informasi server.

## Port

`http://localhost:8005`

## Default Credentials

| Username | Password |
| -------- | -------- |
| admin    | admin    |
| devops   | devops   |
| root     | root     |

## Vulnerabilities yang Ada

### 1. Default Credentials

Login dengan `admin/admin` langsung berhasil.

### 2. Sensitive Files Publik

| File                       | Konten                                       |
| -------------------------- | -------------------------------------------- |
| `/.env`                    | AWS keys, DB password, payment gateway key   |
| `/config.bak`              | Admin credentials, API keys, webhooks        |
| `/database_backup.sql.bak` | SSH & MySQL credentials                      |
| `/info.php`                | phpinfo() — versi PHP, path server, env vars |

### 3. Directory Listing

Semua file di folder `/` tampil karena `Options +Indexes` aktif.

### 4. Verbose Error Messages

Visit `/?trigger_error=1` → PHP error bocorkan path server penuh.

### 5. User Enumeration via Error Message

Pesan error berbeda untuk "user tidak ada" vs "password salah" → bisa enumerate usernames.

## Cara Start Lab

```bash
cd SecMisconfig && docker-compose up -d
```

---

⚠️ **Disclaimer**: Lab ini untuk edukasi keamanan siber saja.
