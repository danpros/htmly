# Developer Activity & Decision Log (DEVLOG)

Dokumen ini mencatat keputusan arsitektur, log aktivitas pengembangan harian, dan pertimbangan teknis selama pengerjaan proyek **HTMLy**.

---

## 📅 2026-08-08 — Final Hardening, Config Templates & Pull Request #1062 Submission
- **Pull Request Submission**: Created & submitted official PR **#1062** on upstream `danpros/htmly` (`feat(core): HTMLy 2026 Modernization Release v3.2.0 - Security, REST API v1, MCP Server, Webhooks, Admin UI & Fixes`).
- **`install.php` & `upload.php` Security Hardening**: Migrated user setup to BCRYPT `password_hash()`, sanitized upload filenames against path traversal, and added timeout-protected HTTPS IP/timezone requests.
- **Apache 2.4 `.htaccess` Hardening**: Updated root, `config/`, and `system/includes/` `.htaccess` files with dual Apache 2.4 (`Require all denied`) and 2.2 support, session cookie hardening (`HttpOnly`, `SameSite=Strict`, `Secure`), and `Authorization` Bearer token preservation.
- **Config Templates & IDE Clean-up**: Added `config/api_keys.ini.example` and `config/webhooks.ini.example`, updated `config/users/username.ini.example`, and fixed core function warnings in `system/htmly.php`.

---

## 📅 2026-08-08 — Root `package.json` Orchestrator Addition


### Root `package.json`
- Menambahkan file **`package.json`** di akar repositori untuk menyediakan script orchestrator (`npm run start:mcp`).
- Mempermudah pengembang dan AI Agent menjalankan MCP Server langsung dari root repositori tanpa merusak sifat utama HTMLy sebagai aplikasi berbasis PHP/Composer.
- Memperbarui `robots.txt` untuk menyembunyikan `/package.json` dari crawler web publik.

---

## 📅 2026-08-08 — Post-Release Security Hardening (API Slug Path Traversal Guard)
- Path Traversal Guard pada parameter `$slug` di endpoint `DELETE /api/v1/pages/{slug}` dan `DELETE /api/v1/posts/{slug}`.

---

## 📅 2026-08-08 — Version Bump to v3.2.0 (2026 Modern Edition Release)
- Memperbarui `HTMLY_VERSION` di `index.php` dan `cache/installedVersion.json` menjadi **`v3.2.0`**.

---

## 📅 2026-08-08 — Contributing Guide & Local Documentation Portal
- `CONTRIBUTING.md`: Panduan kontribusi resmi di akar repositori.

---

## 📅 2026-08-08 — Bug Audit & Additional Fixes (#1045, #1059)
- Fix Issue #1045 (Session Cookie PATH Scope) & Issue #1059 (RSS Item GUID Standard).
- Metadata update: `COPYRIGHT.txt` (2026), `humans.txt`, `robots.txt`.

---

## 📅 2026-08-08 — Phase 4: Modern Admin UI/UX & Command Palette (Ctrl+K)
- `system/resources/css/admin-2026.css`: HSL Design System.
- `system/resources/js/command-palette.js`: Keyboard shortcut `Ctrl+K`.

---

## 📅 2026-08-08 — Phase 3: Automation Webhooks & MCP Server Package
- `system/core/Webhook.php` & `mcp-server/`: Official Node.js MCP Server package.

---

## 📅 2026-08-08 — Phase 2 Ext: Full Coverage REST API v1 Suite
- Full API Controllers: `posts.php`, `pages.php`, `taxonomy.php`, `media.php`, `system.php`, `router.php`.

---

## 📅 2026-08-08 — Phase 1: Security Fix & Initial 2026 Modernization Planning
- Fix Issue #1058 (Draft Delete Safety Guard) & Seluruh Dokumentasi Perencanaan (`docs/`).
