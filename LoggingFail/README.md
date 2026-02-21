# 📋 LoggingFail Lab — AuditTrail System

**OWASP A09: Security Logging & Monitoring Failures**

## Port

`http://localhost:8007`

## Credentials

| Username | Password |
| -------- | -------- |
| alice    | alice123 |
| bob      | bob456   |
| admin    | admin    |

## Vulnerabilities yang Ada

1. **Login gagal tidak di-log** — brute force tidak terdeteksi
2. **Log file publik** — `/audit.log` bisa diakses browser tanpa auth
3. **Log injection** — username dengan `%0A` bisa inject baris log palsu
4. **Staff bisa hapus log** — role staff bisa clear semua audit trail
5. **Privilege escalation tidak di-log** — klik "Escalate ke Admin" tanpa jejak

## Cara Start

```bash
cd LoggingFail && docker-compose up -d
# Setup: http://localhost:8007/setup.php
```
