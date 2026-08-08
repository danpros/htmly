# HTMLy Security Policy & Cyber Security Standards (2026 Edition)

Dokumen ini mendefinisikan kebijakan keamanan, pelaporan kerentanan, dan perlindungan ancaman keamanan siber terkini untuk proyek **HTMLy CMS**.

---

## 🔒 1. Core Security Architecture & Guards

### 1.1 Path Traversal & Arbitrary File Protection (CWE-22)
Sebagai Flat-File CMS, ancaman utama adalah manipulasi path file. HTMLy menerapkan kriteria validasi ketat pada setiap fungsi manipulasi file (`delete_post`, `edit_content`, `read_file`):
1. **Canonicalization**: Memvalidasi `realpath($file)` dan memastikan hasil path diawali oleh lokasi resmi `getcwd() . '/content/'`.
2. **Extension Restriction**: Hanya file dengan ekstensi `.md` yang diperbolehkan untuk dibaca/dihapus pada operasi konten.
3. **Context Validation (Issue #1058)**: Operasi penghapusan draft mewajibkan pengecekan eksplisit bahwa file berada di dalam direktori `/draft/`.

### 1.2 Authentication & API Protection
- **Session Security**: Cookies dikonfigurasi dengan atribut `SameSite=Strict`, `HttpOnly`, dan `Secure` (saat berjalan di HTTPS). Session Cookie `path` dikunci ke direktori instalasi HTMLy.
- **REST API Authorization**: API Key menggunakan format token acak ber-entropi tinggi (`htmly_live_...`) yang divalidasi via `hash_equals()` untuk mencegah timing attacks.
- **Brute Force Protection**: Pembatasan percobaan login (rate limiting) dengan delay eksponensial.

### 1.3 HTTP Security Headers (OWASP Recommended)
HTMLy secara otomatis menyertakan header keamanan berikut pada seluruh response:
```http
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;
```

---

## 🛡️ 2. Reporting Vulnerabilities

Jika Anda menemukan kerentanan keamanan pada HTMLy, **JANGAN** membuat public issue di GitHub.

Silakan laporkan kerentanan secara privat melalui:
- **Security Email**: `security@htmly.com` (atau buat Security Advisory privat di GitHub repository).
- **Format Laporan**:
  - Jenis kerentanan (misal: RCE, CSRF, Path Traversal, XSS).
  - Langkah-langkah untuk mereproduksi (Proof of Concept / PoC).
  - Dampak potensial terhadap sistem.

Tim pengembang akan merespons laporan dalam waktu **48 jam** dan mengeluarkan patch resmi secepatnya.
