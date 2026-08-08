# Model Context Protocol (MCP) Server Specification for HTMLy

Spesifikasi ini memungkinkan AI Agent seperti **Hermes-Agent**, **OpenClaw**, **Antigravity**, **Cursor**, atau AI Agentic Framework lainnya untuk terhubung langsung ke HTMLy via protocol MCP standard.

---

## 🛠️ Defined MCP Tools

Setiap MCP Server HTMLy harus mengekspos daftar tool berikut ke AI Agent:

### Tool 1: `htmly_publish_post`
Publikasi artikel baru langsung ke blog.
- **Arguments**:
  - `title` (string, required): Judul artikel.
  - `content` (string, required): Isi artikel dalam Markdown format.
  - `category` (string, optional): Kategori (misal: "technology").
  - `tags` (string, optional): Tag dipisah koma (misal: "ai,mcp,2026").
  - `status` (string, optional): `published` atau `draft` (default: `published`).

### Tool 2: `htmly_list_posts`
Mengambil daftar artikel yang tersimpan di CMS.
- **Arguments**:
  - `status` (string, optional): Filter status (`published` / `draft`).
  - `limit` (number, optional): Jumlah item (default: 10).

### Tool 3: `htmly_delete_post`
Menghapus postingan/draft berdasarkan slug.
- **Arguments**:
  - `slug` (string, required): Slug artikel yang akan dihapus.

---

## 💻 Contoh Implementasi MCP Tool Definition (JSON-RPC)

```json
{
  "name": "htmly_publish_post",
  "description": "Publish a new blog post or draft directly to HTMLy Flat-File CMS",
  "inputSchema": {
    "type": "object",
    "properties": {
      "title": { "type": "string", "description": "Title of the post" },
      "content": { "type": "string", "description": "Markdown body content" },
      "category": { "type": "string", "description": "Category name" },
      "tags": { "type": "string", "description": "Comma separated tags" },
      "status": { "type": "string", "enum": ["published", "draft"], "default": "published" }
    },
    "required": ["title", "content"]
  }
}
```
