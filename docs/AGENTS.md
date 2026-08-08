# Agentic AI Integration Guide & System Prompt (AGENTS.md)

Dokumen ini adalah instruksi tingkat tinggi dan panduan perilaku (behavioral guidelines) bagi **AI Agentic Frameworks** (seperti **Hermes-Agent**, **OpenClaw**, **Antigravity**, **Cursor**, **Windsurf**, dll.) saat berinteraksi dengan codebase maupun instance aktif **HTMLy CMS**.

---

## 🤖 System Context for AI Agents

HTMLy adalah **Flat-File CMS**. AI Agent harus memahami bahwa:
- **State Data**: Disimpan sebagai file `.md` (Markdown) dan file `.json` (Index/Search/Views) di direktori `content/`.
- **System Config**: Disimpan di file `.ini` di direktori `config/`.
- **No Database**: Jangan lakukan query SQL. Semua akses data dilakukan via file system atau via HTMLy REST API.

---

## 🛡️ Operational Safety Rules for Autonomous Agents

1. **Atomic File Writes**:
   - Selalu pastikan penulisan file menggunakan `LOCK_EX` atau via REST API resmi agar tidak merusak file index JSON yang dibaca bersamaan.
2. **Strict Path Sanitation**:
   - Jangan pernah mengakses file di luar folder `content/` dan `config/`.
   - Gunakan `realpath()` dan pastikan path berawalan dengan `content/` untuk mencegah **Arbitrary File Read/Write (CWE-22)**.
3. **Draft vs Published Isolation**:
   - Artikel draft tersimpan di path `content/{user}/blog/{category}/draft/`.
   - Artikel published tersimpan di path `content/{user}/blog/{category}/post/`.
   - Pastikan agent tidak pernah tertukar antara status draft dan published saat melakukan operasi update/delete.

---

## 🛠️ MCP Tool Schema for Agents

Ketika AI Agent terhubung melalui **Model Context Protocol (MCP)**, gunakan tools berikut:

```typescript
// Tool: Publish Post
use_tool("htmly_publish_post", {
  title: "Judul Artikel",
  content: "Isi Markdown...",
  category: "technology",
  tags: "ai,mcp",
  status: "published" // or "draft"
});

// Tool: List Content
use_tool("htmly_list_posts", {
  status: "published",
  limit: 10
});

// Tool: Delete Content safely
use_tool("htmly_delete_post", {
  slug: "post-slug-target"
});
```

---

## 💡 Prompt Guidelines for Humans Prompting Agents

Saat meminta AI Agent mengelola HTMLy:
- **Good Prompt**: *"Buatkan draft artikel Markdown tentang topik X di kategori tech dengan tag ai, lalu tampilkan preview-nya sebelum dipublish."*
- **Bad Prompt**: *"Hapus semua artikel lama di database."* (Salah, karena HTMLy tidak pakai database).
