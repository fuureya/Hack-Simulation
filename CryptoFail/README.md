# 🔐 CryptoFail Lab — SafeVault Bank

**OWASP A02: Cryptographic Failures**

## Skenario

SafeVault Bank adalah aplikasi perbankan yang gagal mengimplementasikan kriptografi yang benar. Data sensitif pengguna tersimpan dan ditransmisikan tanpa enkripsi yang memadai.

## Port

`http://localhost:8003`

## Credentials

| Username | Password    |
| -------- | ----------- |
| alice    | password123 |
| bob      | qwerty      |
| admin    | admin       |

## Vulnerabilities yang Ada

### 1. Password Hashing Lemah (MD5 tanpa salt)

```php
// VULN: MD5 tanpa salt
$hashed = md5($password);
```

**Eksploitasi**: Copy hash dari halaman dashboard → paste ke [crackstation.net](https://crackstation.net)

### 2. Data Sensitif di Cookie Plaintext

```
Cookie: user_info={"id":1,"username":"alice","credit_card":"4111111111111111","pin":"1234","balance":"5000000"}
```

**Eksploitasi**: DevTools → Application → Cookies → lihat `user_info`

### 3. Data Sensitif di URL (GET Parameters)

```
/dashboard.php?show=true&card=4111111111111111&pin=1234&balance=5000000
```

**Eksploitasi**: Klik tombol "Lihat Detail Sensitif" dan perhatikan URL

### 4. Remember-Me Token Predictable

```php
setcookie('remember_token', md5($user['username'] . 'safevault'), ...);
```

**Eksploitasi**: Token = `md5("alice" + "safevault")` → bisa dihitung tanpa login

### 5. PIN & Nomor Kartu Disimpan Plaintext di Database

PIN dan nomor kartu kredit tidak dienkripsi sama sekali di database.

## Cara Start Lab

```bash
cd CryptoFail && docker-compose up -d
```

## Setup Database

Visit `http://localhost:8003/setup.php`

---

⚠️ **Disclaimer**: Lab ini untuk edukasi keamanan siber saja.
