# Product Requirement Document (PRD): HTMLy 2026 Modernization & AI-Native Architecture

## 1. Vision & Executive Summary
HTMLy adalah Flat-File CMS berbasis PHP yang sangat cepat, ringan, dan berjalan tanpa database. Dalam modernisasi tahun 2026 ini, HTMLy akan ditransformasi menjadi **AI-Native & Automation-Ready Flat-File CMS**. 

Perubahan ini mempertahankan performa tinggi tanpa DB, namun membawa:
1. **Modern UI/UX**: Admin Panel & Frontend theme dengan tren desain 2026 (Dark/Light mode, Glassmorphism, Command Palette).
2. **First-Class REST API**: Mendukung headless CMS workflow & integrasi penuh dengan platform otomasi seperti **n8n**.
3. **MCP Server Integration**: Mendukung protocol **Model Context Protocol (MCP)** agar AI Agent (Hermes-Agent, OpenClaw, Antigravity, dll.) dapat mengelola konten secara otonom.

---

## 2. Core Pillars & Specifications

### 2.1 Modernization of UI & Architecture
- **Admin Interface**: Redesain total panel admin menggunakan Vanilla CSS3 modern (CSS Custom Properties, Flexbox/Grid, Dark Mode auto-toggle, Clean Micro-interactions).
- **Command Palette (`Ctrl+K` / `Cmd+K`)**: Quick navigation untuk mencari post, membuat draft baru, atau masuk ke pengaturan.
- **Code Refactoring**: Ekstraksi logic API & Helper keluar dari monolithic `system/htmly.php` ke struktur yang lebih rapi (`system/src/` atau `system/api/`).

### 2.2 REST API Specification
- **Authentication**: Stateful session (browser) & Stateless API Key Bearer Token (untuk otomasi/external script).
- **Core Endpoints**:
  - `GET /api/v1/posts`: Mengambil daftar postingan dengan filter & paginasi.
  - `POST /api/v1/posts`: Membuat postingan atau draft baru.
  - `GET /api/v1/posts/{slug}`: Mengambil detail postingan spesifik.
  - `PUT /api/v1/posts/{slug}`: Memperbarui postingan/draft.
  - `DELETE /api/v1/posts/{slug}`: Menghapus postingan/draft dengan aman.
  - `POST /api/v1/media/upload`: Upload file/gambar via Multipart Request.

### 2.3 Automation & n8n Integration
- **Webhooks**: Notifikasi event otomatis saat post dipublish, diupdate, atau dihapus ke webhook URL pihak ketiga (n8n workflow).
- **n8n Compatibility**: Format JSON standar yang mudah di-parse oleh n8n HTTP Request Node.

### 2.4 MCP (Model Context Protocol) Readiness
- Menyediakan arsitektur MCP Server sehingga AI Agent dapat mengakses fungsi HTMLy sebagai "Tools" baku:
  - `htmly_publish_post`
  - `htmly_create_draft`
  - `htmly_list_content`
  - `htmly_get_system_health`

---

## 3. Technical Constraints & Compatibility
- **Backward Compatibility**: Tetap berjalan di server PHP standard tanpa memerlukan database SQL.
- **PHP Version**: Mengoptimalkan dukungan PHP 8.1+ dengan sintaks modern tanpa merusak struktur legacy.
