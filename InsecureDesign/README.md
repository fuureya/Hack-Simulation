# 🏗️ InsecureDesign Lab — QuickLoan App

**OWASP A04: Insecure Design**

## Skenario

QuickLoan adalah aplikasi pinjaman online dengan desain yang tidak aman secara fundamental — bukan karena bug implementasi, tapi karena alur bisnis dirancang tanpa mempertimbangkan keamanan.

## Port

`http://localhost:8004`

## Credentials

| Username | Password  | Security Answer |
| -------- | --------- | --------------- |
| budi     | Budi1234! | kucing          |
| sari     | Sari5678! | jakarta         |
| admin    | admin     | merah           |

## Vulnerabilities yang Ada

### 1. Tidak Ada Rate Limiting (Brute Force)

Login tidak dibatasi — attacker bisa mencoba ribuan password tanpa terkunci.

### 2. Security Question yang Lemah

Pertanyaan keamanan (nama hewan peliharaan, kota lahir) mudah ditebak dari media sosial.

```
Username: budi → jawaban: kucing
Username: sari → jawaban: jakarta
```

### 3. Business Logic Bypass — Over Credit Limit

Field `max` pada input amount hanya ada di client-side HTML:

```html
<input type="number" name="amount" max="5000000" />
```

Hapus atribut `max` via DevTools → bisa input 9999999999.

### 4. Interest Rate Manipulation

Interest rate dikirim sebagai hidden field dari client:

```html
<input type="hidden" name="interest_rate" value="2.50" />
```

Ubah value ke `0` via DevTools → pinjam tanpa bunga!

## Cara Start Lab

```bash
cd InsecureDesign && docker-compose up -d
```

## Setup Database

Visit `http://localhost:8004/setup.php`

---

⚠️ **Disclaimer**: Lab ini untuk edukasi keamanan siber saja.
