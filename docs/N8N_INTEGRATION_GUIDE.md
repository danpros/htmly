# n8n Automation & Integration Guide for HTMLy

Panduan ini mendokumentasikan cara mengintegrasikan **HTMLy** dengan workflow automation platform **n8n**.

---

## 1. Setup Credential di n8n

Di n8n dashboard:
1. Buat Header Auth Credential baru.
2. Set Name: `Header Auth`
3. Set Header Name: `Authorization`
4. Set Header Value: `Bearer <HTMLY_API_KEY_KAMU>`

---

## 2. Contoh Workflow n8n

### Scenario 1: Auto-Publish Artikel dari AI (OpenAI / Claude / Gemini) ke HTMLy
```
[Schedule Trigger] ---> [OpenAI Node (Generate Article)] ---> [HTTP Request Node (Post to HTMLy API)]
```

**Konfigurasi HTTP Request Node**:
- **Method**: `POST`
- **URL**: `https://your-htmly-site.com/api/v1/posts`
- **Authentication**: Pre-configured Header Auth
- **Body Content Type**: JSON
- **JSON Body**:
```json
{
  "title": "={{ $json.article_title }}",
  "content": "={{ $json.article_content }}",
  "category": "ai-generated",
  "tags": "automation, n8n",
  "status": "published"
}
```

---

### Scenario 2: Synchronize Drafts & Content Backups
Mengambil draft dari HTMLy dan menyimpannya ke Google Drive / Notion secara berkala:
```
[Cron Trigger] ---> [HTTP Request Node (GET /api/v1/posts?status=draft)] ---> [Notion / GDrive Node]
```
