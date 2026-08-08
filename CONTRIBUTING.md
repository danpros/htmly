# Contributing to HTMLy

First off, thank you for considering contributing to **HTMLy**! 🎉

HTMLy is a fast, light, and databaseless flat-file CMS. Whether you are fixing a bug, adding new REST API endpoints, designing modern UI themes, expanding translations, or building AI Agent integration tools, your contributions are warmly welcomed.

---

## 📑 Table of Contents

- [Code of Conduct](#-code-of-conduct)
- [How Can I Contribute?](#-how-can-i-contribute)
- [Development Setup](#-development-setup)
- [Git Workflow & Pull Requests](#-git-workflow--pull-requests)
- [Coding & Security Guidelines](#-coding--security-guidelines)
- [AI Agent Contribution Guidelines](#-ai-agent-contribution-guidelines)

---

## 🤝 Code of Conduct

We are committed to providing a welcoming, inclusive, and harassment-free environment for everyone. Please be respectful, constructive, and collaborative in all discussions, issues, and pull requests.

---

## 💡 How Can I Contribute?

### 1. Bug Fixes & Code Improvements
Check out open [GitHub Issues](https://github.com/danpros/htmly/issues) with labels like `bug` or `good first issue`.

### 2. REST API & Automation Integrations
Contribute new endpoints to `system/api/v1/` or improve n8n webhooks (`system/core/Webhook.php`) and Model Context Protocol Tools (`mcp-server/`).

### 3. Translations (`/lang/`)
HTMLy supports internationalization via `.ini` files in the `/lang/` directory. You can add a new language or update existing translation keys.

### 4. Documentation (`/docs/`)
Improve existing guides or add workflow tutorials in the [`docs/`](./docs) directory.

---

## 🛠️ Development Setup

### Requirements
- **PHP**: 8.1+ (compatible with 7.4+)
- **Extensions**: `php-mbstring`, `php-xml`, `php-gd`, `php-zip`, `php-curl`
- **Node.js**: 18+ (optional, for testing `mcp-server/`)

### Local Environment Setup
1. **Fork the Repository**: Click the **Fork** button at the top right of [danpros/htmly](https://github.com/danpros/htmly).
2. **Clone your Fork**:
   ```bash
   git clone https://github.com/YOUR_USERNAME/htmly.git
   cd htmly
   ```
3. **Configure Upstream Remote**:
   ```bash
   git remote add upstream https://github.com/danpros/htmly.git
   ```
4. **Run Local Server**:
   Point your local web server (XAMPP, Laragon, or Built-in PHP server) to the project root:
   ```bash
   php -S localhost:8000
   ```
   Open `http://localhost:8000/install.php` to set up local test data.

---

## 🔄 Git Workflow & Pull Requests

### Branch Naming Conventions
Create a topic branch from `master`:
- `fix/1058-stale-draft-delete` (for bug fixes)
- `feat/api-endpoint-comments` (for new features)
- `docs/update-readme` (for documentation)

### Commit Message Format
We follow clean, descriptive commit messages:
```bash
git commit -m "fix(security): sanitize file path in delete_post() (#1058)"
git commit -m "feat(api): add GET /api/v1/pages endpoint"
```

### Pull Request Checklist
Before submitting your PR:
- [ ] Run PHP syntax linting: `php -l path/to/file.php`.
- [ ] Ensure no trailing whitespace or unnecessary file modifications.
- [ ] Reference related issue numbers in the PR description (e.g. `Closes #1058`).
- [ ] Keep PRs focused on a single concern or fix.

---

## 🛡️ Coding & Security Guidelines

### 1. No SQL / Database Assumption
HTMLy is **100% databaseless**. All data resides in Markdown (`.md`), `.json`, or `.ini` files inside `content/` and `config/`. Do not introduce SQL dependencies.

### 2. Path Traversal & Arbitrary File Security
Always canonicalize and validate file paths using `realpath()` to ensure requests stay strictly inside `getcwd() . '/content/'`.

### 3. File Concurrency & Locking
Use `LOCK_EX` when performing `file_put_contents()` on content or JSON index files to prevent race conditions.

---

## 🤖 AI Agent Contribution Guidelines

If you are an **AI Coding Agent** (e.g., Antigravity, Cursor, Windsurf, Hermes-Agent, OpenClaw) contributing code or documentation to this repository:

- Read [docs/AI_AGENT_GUIDELINES.md](./docs/AI_AGENT_GUIDELINES.md) and [docs/AGENTS.md](./docs/AGENTS.md) before making code edits.
- Respect existing procedural PHP routing patterns in `system/htmly.php` and modular controllers in `system/api/v1/`.
- Ensure all created/modified PHP files pass `php -l` linting checks.

---

Thank you for helping make HTMLy better for everyone! 🚀
