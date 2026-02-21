# 🔐 AuthFail Lab — SecureLogin Corp

**OWASP A07: Identification & Authentication Failures**

## Skenario

SecureLogin Corp adalah sistem SSO perusahaan yang memiliki kelemahan autentikasi fundamental — session tidak dikelola dengan benar, tidak ada lockout, dan mekanisme "ingat saya" yang lemah.

## Port

`http://localhost:8006`

## Credentials

| Username | Password |
| -------- | -------- |
| alice    | pass     |
| bob      | 123456   |
| admin    | admin    |

## Vulnerabilities yang Ada

### 1. Session Fixation

```
GET /?PHPSESSID=hacker_controlled_id
```

Session ID tidak berubah setelah login (`session_regenerate_id()` tidak dipanggil).

### 2. Session Tidak Invalidasi saat Logout

```php
// VULN: session_destroy() tidak dipanggil!
setcookie(session_name(), '', time() - 3600, '/');
```

Session di server tetap hidup walaupun sudah logout dari browser.

**Exploit**: Copy session ID sebelum logout → gunakan di request baru setelah logout → masih bisa akses dashboard.

### 3. Brute Force Tanpa Lockout

Tidak ada rate limiting, CAPTCHA, atau account lockout sama sekali.

### 4. Remember-Me Token Predictable

```
Token = base64(user_id + ":" + timestamp_hari_ini)
```

Mudah diprediksi — cukup enumerate user_id + timestamp hari ini.

### 5. Password Lemah Diterima

"pass", "123456", "admin" diterima tanpa validasi kompleksitas.

## Cara Start Lab

```bash
cd AuthFail && docker-compose up -d
```

## Setup Database

Visit `http://localhost:8006/setup.php`

---

⚠️ **Disclaimer**: Lab ini untuk edukasi keamanan siber saja.
