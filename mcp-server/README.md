# HTMLy MCP (Model Context Protocol) Server

Paket ini menyediakan **MCP Server** resmi berbasis Stdio JSON-RPC yang memungkinkan AI Agent (seperti **Hermes-Agent**, **OpenClaw**, **Antigravity**, **Cursor**, dll.) untuk terhubung dan mengelola **HTMLy CMS** secara mandiri via Tool Calls.

---

## 🛠️ Exposed MCP Tools

1. `htmly_publish_post`: Publikasi artikel baru atau simpan draft ke HTMLy.
2. `htmly_list_posts`: Ambil daftar postingan published / draft dengan filter & paginasi.
3. `htmly_delete_post`: Hapus artikel/draft berdasarkan slug secara aman.
4. `htmly_get_system_health`: Ambil data telemetri sistem & kesehatan server.

---

## ⚙️ Configuration & Run

Set environment variables:
- `HTMLY_SITE_URL`: Base URL dari situs HTMLy kamu (misal: `http://localhost` atau `https://myblog.com`)
- `HTMLY_API_KEY`: API Key valid yang dikonfigurasi di `config/api_keys.ini`.

### Menjalankan MCP Server:
```bash
cd mcp-server
export HTMLY_SITE_URL="https://myblog.com"
export HTMLY_API_KEY="htmly_live_key_xxx"
node index.js
```

### Konfigurasi di Antigravity / Cursor / Claude Desktop (`mcp.json`):
```json
{
  "mcpServers": {
    "htmly": {
      "command": "node",
      "args": ["c:/Projects/htmly/mcp-server/index.js"],
      "env": {
        "HTMLY_SITE_URL": "http://localhost",
        "HTMLY_API_KEY": "htmly_live_key_xxx"
      }
    }
  }
}
```
