# CHANGELOG

Semua perubahan penting pada proyek HTMLy akan didokumentasikan di file ini.

Format changelog ini mengikuti panduan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) dan menggunakan [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [v3.2.0] - 2026-08-08

### Added
- **2026 Modernization Documentation Suite**:
  - `docs/PRD.md`, `docs/ARCHITECTURE.md`, `docs/API_SPECIFICATION.md`, `docs/N8N_INTEGRATION_GUIDE.md`, `docs/MCP_SERVER_SPEC.md`, `docs/AI_AGENT_GUIDELINES.md`, `docs/AGENTS.md`, `docs/DEVPLAN.md`, `docs/DESIGN.md`, `docs/DEVLOG.md`.
- **Full Coverage REST API v1 Suite (`system/api/v1/`)**:
  - Endpoints lengkap untuk Posts, Drafts, Pages, Categories, Tags, Media Upload, dan System Telemetry.
  - Bearer Token Auth Guard (`config/api_keys.ini`) yang aman dari timing attack (`hash_equals()`).
- **Automation & MCP Integration (`mcp-server/` & `system/core/Webhook.php`)**:
  - Asynchronous Webhook Event Dispatcher (`config/webhooks.ini`).
  - Standalone Executable Node.js MCP Server Package (`index.js`) untuk AI Agent (Hermes, OpenClaw, Antigravity, Cursor).
- **Modern Admin UI/UX (2026 Edition)**:
  - Design system HSL adaptif Auto Dark Mode, Glassmorphism, dan badge status modern (`system/resources/css/admin-2026.css`).
  - Command Palette modal navigasi cepat dipicu oleh `Ctrl+K` / `Cmd+K` (`system/resources/js/command-palette.js`).

### Fixed
- **Critical Fix (Issue #1058)**: Menambahkan validasi path `/draft/` dan pengecekan ekstensi file `.md` pada fungsi `delete_post()` untuk mencegah draft stale menghapus artikel published.
- **Session Cookie Scope Fix (Issue #1045)**: Menambahkan helper `htmly_session_start()` yang mengonfigurasi `session_set_cookie_params()` secara terpusat dengan `path` lokasi instalasi HTMLy, `HttpOnly`, `SameSite=Strict`, dan `Secure` flag untuk mencegah konflik cookie `PHPSESSID`.
- **RSS Feed Standard Fix (Issue #1059)**: Menambahkan tag `<guid permalink="true">` pada pembentukan item RSS 2.0 di `generate_rss()`.

### Security
- **SECURITY.md Update**: Memperbarui kebijakan keamanan sesuai standar OWASP 2026.

---

## [v3.1.1] - 2024-05-10
### Fixed
- Arbitrary file deletion vulnerability patch (CVE-2024-34191).
- Minor bug fixes & translation updates.
