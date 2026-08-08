<div align="center">

# [![HTMLy logo](https://raw.githubusercontent.com/danpros/htmly/master/system/resources/images/logo.png)](https://www.htmly.com/)

### **Fast, Light, Databaseless & AI-Native Flat-File CMS**

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](LICENSE.txt)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B%20%7C%207.4-777BB4.svg?logo=php)](https://www.php.net/)
[![API Status](https://img.shields.io/badge/REST%20API-v1%20Full%20Coverage-success.svg)](#-rest-api--automation)
[![MCP Ready](https://img.shields.io/badge/MCP-Server%20Ready-brightgreen.svg)](#-model-context-protocol-mcp)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

[Website](https://www.htmly.com/) • [Documentation](https://docs.htmly.com/) • [Live Demo](https://www.htmly.com/demo) • [Contributing](CONTRIBUTING.md)

---

</div>

## 🌟 Overview

**HTMLy** is an open-source, databaseless blogging platform written in PHP that prioritizes **simplicity, extreme speed, and modern automation**. 

It uses an optimized flat-file index algorithm to find or list any content based on date, category, tag, or author. Its performance remains blazingly fast even with **10,000+ posts and hundreds of tags** — without requiring any SQL database setup or maintenance!

> 🚀 **2026 Modern Edition**: HTMLy now features a **Full-Coverage REST API**, **n8n Automation Webhooks**, an official **MCP (Model Context Protocol) Server**, and a sleek **Glassmorphism Admin UI with a `Ctrl+K` Command Palette**!

---

## ✨ Key Features

- ⚡ **Zero Database (Flat-File)**: All posts, pages, and drafts are stored as clean Markdown (`.md`) files.
- 🤖 **AI Agent Friendly**: Built-in **Model Context Protocol (MCP)** server for AI Agents (**Hermes-Agent**, **OpenClaw**, **Antigravity**, **Cursor**).
- 🔗 **Full REST API (v1)**: Headless CMS capabilities out of the box with Bearer Token authentication (`/api/v1/posts`, `/api/v1/pages`, `/api/v1/media`).
- 🔄 **Automation Ready**: Native Webhook event dispatcher for seamless **n8n** workflows.
- 🎨 **Modern Admin UI**: HSL adaptive dark/light mode, Glassmorphic surfaces, and **Command Palette (`Ctrl+K` / `Cmd+K`)**.
- 🔐 **Hardened Security**: Session cookie scope protection, CSRF guards, 2FA (Google Authenticator), Cloudflare Turnstile & reCAPTCHA support.
- 📁 **Multi-User & Role System**: Supports Admin, Editor, and Author roles.
- 🏷️ **Taxonomy & Search**: Tag clouds, custom categories, and full-text search capability.

---

## ⚡ Quick Start & Installation

### Option 1: Web Installer (Recommended)
1. Download the latest release from the [GitHub Releases](https://github.com/danpros/htmly/releases/latest).
2. Extract and upload the files to your web server (root directory or sub-folder).
3. Open your browser and navigate to `https://your-domain.com/install.php`.
4. Follow the step-by-step installer. The installer will auto-delete upon completion.

### Option 2: Git Clone
```bash
git clone https://github.com/danpros/htmly.git
cd htmly
composer install
```

---

## 🔌 REST API & Automation

HTMLy exposes a complete, stateless REST API secured via Bearer Token (`config/api_keys.ini`).

```http
Authorization: Bearer YOUR_API_KEY
Content-Type: application/json
```

### Endpoints Overview

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/posts` | List published posts or drafts (`?status=published\|draft&page=1&limit=10`) |
| `POST` | `/api/v1/posts` | Create a new blog post or draft |
| `DELETE` | `/api/v1/posts/{slug}` | Safely delete a post by slug |
| `GET` | `/api/v1/pages` | List all static pages |
| `POST` | `/api/v1/pages` | Create a static page |
| `POST` | `/api/v1/media/upload` | Upload media files (returns markdown snippet) |
| `GET` | `/api/v1/system/health` | Server health, disk free space, and telemetry |

*See full API documentation in [docs/API_SPECIFICATION.md](./docs/API_SPECIFICATION.md).*

---

## 🤖 Model Context Protocol (MCP)

HTMLy includes an official Node.js **MCP Server** (`mcp-server/`) allowing AI Agents to manage your blog autonomously.

### Starting the MCP Server
```bash
cd mcp-server
export HTMLY_SITE_URL="https://yourblog.com"
export HTMLY_API_KEY="your_api_key"
node index.js
```

### `mcp.json` Configuration for AI Agents (Antigravity / Cursor / Claude)
```json
{
  "mcpServers": {
    "htmly": {
      "command": "node",
      "args": ["/path/to/htmly/mcp-server/index.js"],
      "env": {
        "HTMLY_SITE_URL": "http://localhost",
        "HTMLY_API_KEY": "your_api_key"
      }
    }
  }
}
```

---

## 📚 Documentation Portal

Comprehensive guide and architectural documentation are available in the [`docs/`](./docs) directory:

- 📋 [Product Requirement Document (PRD.md)](./docs/PRD.md)
- 🏗️ [System Architecture & Refactoring Plan (ARCHITECTURE.md)](./docs/ARCHITECTURE.md)
- 📡 [REST API OpenAPI Specification (API_SPECIFICATION.md)](./docs/API_SPECIFICATION.md)
- 🔄 [n8n Automation Guide (N8N_INTEGRATION_GUIDE.md)](./docs/N8N_INTEGRATION_GUIDE.md)
- 🛠️ [Model Context Protocol (MCP) Server Spec (MCP_SERVER_SPEC.md)](./docs/MCP_SERVER_SPEC.md)
- 🤖 [AI Agent Guidelines & System Instructions (AI_AGENT_GUIDELINES.md)](./docs/AI_AGENT_GUIDELINES.md)
- 🗺️ [Development Roadmap & Plan (DEVPLAN.md)](./docs/DEVPLAN.md)
- 🎨 [UI/UX Design System Guidelines (DESIGN.md)](./docs/DESIGN.md)

---

## 🛡️ Security & Bug Reports

Security vulnerabilities are taken very seriously. Please review our [SECURITY.md](SECURITY.md) policy for reporting security issues confidentially.

---

## 📄 License

HTMLy is open-source software licensed under the [GNU General Public License v2.0](LICENSE.txt).
Copyright (c) 2014-2026 Danang Probo Sayekti and [Contributors](humans.txt).
