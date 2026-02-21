# 🛡️ IntegrityFail Lab — ObjectRelay Service

**OWASP A08: Software and Data Integrity Failures**

## Port

`http://localhost:8012`

## Skenario

Aplikasi ini menyimpan data profil pengguna di dalam Cookie `session`. Data tersebut berupa objek PHP yang di-serialize lalu di-encode Base64. Karena tidak ada tanda tangan digital (HMAC), integritas data tidak terjamin.

## Cara Eksploitasi

Attacker dapat memanipulasi cookie untuk mengubah role mereka sendiri menjadi `admin`.

### Langkah:

1. Ambil nilai cookie `session`.
2. Decode Base64.
3. Ubah string serialize-nya (misal ganti `user` jadi `admin` atau `is_admin;b:0` jadi `is_admin;b:1`).
4. Encode kembali ke Base64.
5. Ganti cookie di browser dan refresh halaman.

## Cara Start

```bash
cd IntegrityFail && docker-compose up -d
```
