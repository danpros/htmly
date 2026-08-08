# AI Agent Guidelines & System Instructions for HTMLy Development

Dokumen ini berisi instruksi khusus bagi **AI Coding Agent** (seperti Antigravity, Cursor, Windsurf, Copilot, dll.) ketika berkontribusi atau mengedit kode di dalam repositori **HTMLy**.

---

## 🎯 Aturan Utama Pengembangan (Core Rules)

1. **NO SQL / DATABASE ASSUMPTION**:
   - HTMLy **TIDAK** menggunakan database SQL (MySQL, PostgreSQL, SQLite, dll.).
   - Jangan pernah menyarankan atau membuat tabel SQL/ORM script.
   - Semua manipulasi data harus beroperasi pada file Markdown (`.md`), JSON index (`.json`), atau file `.ini` di direktori `content/` dan `config/`.

2. **KEAMANAN FILE & PATH TRAVERSAL**:
   - Selalu gunakan `realpath()` dan validasi bahwa file yang dimodifikasi/dihapus berada di dalam direktori proyek (terutama direktori `content/`).
   - Setiap operasi penghapusan file wajib memvalidasi ekstensi file (`.md`) dan konteks path (misal: memvalidasi folder `/draft/` saat menghapus draft).

3. **CONCURRENCY & FILE LOCKING**:
   - Saat menulis file konten atau cache, gunakan flag `LOCK_EX` pada `file_put_contents()`.
   - Hindari menulis langsung ke file index tanpa proses decoding dan encoding JSON yang valid.

4. **STYLE & COMPATIBILITY**:
   - HTMLy menggunakan gaya PHP sederhana (gabungan procedural & micro-routing).
   - Pastikan kode yang ditulis kompatibel dengan PHP 8.1+ tanpa memutus fungsionalitas legacy PHP 7.4/8.0.
   - Gunakan komentar kode yang jelas untuk setiap fungsi helper baru.

5. **STRUKTUR MODULAR BARU**:
   - Fitur baru terkait API dimasukkan ke `system/api/`.
   - Fitur UI/Theme baru menggunakan struktur CSS variabel modern tanpa menambahkan library/framework berat eksternal jika tidak diperlukan.
