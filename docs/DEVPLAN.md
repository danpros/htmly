# Development Roadmap & Implementation Plan (DEVPLAN)

Dokumen ini berisi tahapan eksekusi dan roadmap pengembangan **HTMLy 2026 Modernization & AI Integration**.

---

## 🗺️ Roadmap & Phases

```mermaid
gantt
    title HTMLy 2026 Roadmap
    dateFormat  YYYY-MM-DD
    section Phase 1: Security & Bug Fixes
    Fix Bug #1058 (Draft Delete)        :done, p1a, 2026-08-08, 1d
    Update Security Policy & Headers    :active, p1b, 2026-08-08, 2d
    section Phase 2: REST API Engine
    API Router & Auth Middleware        :p2a, 2026-08-10, 3d
    Posts & Drafts Endpoints            :p2b, 2026-08-13, 3d
    Media Upload Endpoint               :p2c, 2026-08-16, 2d
    section Phase 3: n8n & MCP Server
    n8n Webhook Integration             :p3a, 2026-08-18, 2d
    MCP Server Implementation           :p3b, 2026-08-20, 3d
    section Phase 4: UI/UX Modernization
    Admin Panel Redesign (CSS3/Modern)  :p4a, 2026-08-23, 5d
    Command Palette (Ctrl+K)            :p4b, 2026-08-28, 2d
```

---

## 📋 Actionable Tasks

### Phase 1: Security & Core Stabilization
- [x] Fix Bug #1058: Mencegah draft stale menghapus post published.
- [ ] Perbarui `SECURITY.md` dengan standar OWASP 2026 & kebijakan penanganan kerentanan.
- [ ] Terapkan Security Response Headers (CSP, HSTS, X-Content-Type-Options) di `.htaccess` dan `system/htmly.php`.

### Phase 2: REST API Core (`system/api/v1/`)
- [ ] Buat `system/api/v1/router.php` untuk menangani endpoint API terpisah dari HTML view router.
- [ ] Implementasikan `system/api/v1/auth.php` (Bearer token validation via `config/api_keys.ini`).
- [ ] Implementasikan CRUD Controller untuk `/api/v1/posts` dan `/api/v1/drafts`.
- [ ] Implementasikan file upload handler di `/api/v1/media/upload`.

### Phase 3: Automation & Agentic MCP Integration
- [ ] Tambahkan webhook event dispatcher saat post dipublish/dihapus (`system/core/Webhook.php`).
- [ ] Buat package MCP Server (`mcp-server/index.js` atau CLI wrapper) yang membungkus REST API ke format MCP JSON-RPC.

### Phase 4: Modern Admin UI & Themes
- [ ] Refactor CSS admin di `system/admin/resources/` menggunakan Vanilla CSS Variables (Dark/Light mode).
- [ ] Implementasikan komponen Command Palette (`Ctrl+K`) menggunakan Javascript murni tanpa framework berat.
