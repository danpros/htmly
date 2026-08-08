# HTMLy REST API Specification (v1 - Full Coverage)

API ini dirancang stateless dengan format respon JSON standar untuk mempermudah konsumsi oleh **n8n**, **Custom Webhooks**, dan **MCP Servers**.

---

## 🔒 Authentication
Kirim header pada setiap request:
```http
Authorization: Bearer YOUR_HTMLY_API_KEY
Content-Type: application/json
```

---

## 📌 Complete Endpoints Matrix

### 1. Posts & Drafts

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/v1/posts` | Mengambil daftar postingan/draft (`?status=published\|draft&page=1&limit=10`). |
| `POST` | `/api/v1/posts` | Membuat postingan atau draft baru. |
| `DELETE` | `/api/v1/posts/{slug}` | Menghapus postingan/draft berdasarkan slug. |

### 2. Static Pages

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/v1/pages` | Mengambil seluruh halaman statis. |
| `POST` | `/api/v1/pages` | Membuat halaman statis baru. |
| `DELETE` | `/api/v1/pages/{slug}` | Menghapus halaman statis berdasarkan slug. |

### 3. Categories & Tags Taxonomy

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/v1/categories` | Mengambil daftar seluruh kategori. |
| `POST` | `/api/v1/categories` | Membuat kategori baru. |
| `GET` | `/api/v1/tags` | Mengambil daftar tag cloud. |

### 4. Media Upload

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/v1/media/upload` | Upload gambar/file media via Multipart Request (`file` field). |

### 5. System Health & Telemetry

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/v1/system/health` | Telemetri kesehatan sistem (PHP version, disk space, total posts/drafts/pages, cache size). |
