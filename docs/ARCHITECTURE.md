# System Architecture & Refactoring Plan

## 1. Current vs Proposed Architecture

### Legacy Architecture
```
[User Browser] ---> index.php ---> system/htmly.php (Monolithic 230KB)
                                        |
                                        +---> Flat Files (/content/)
                                        +---> Admin Views (/system/admin/)
```

### Proposed Modern Architecture (2026)
```
[User Browser]  ---------> [ Frontend Router ] ----> Theme Views
[n8n / AI Agent / MCP] --> [ API Router ] ---------> REST API Controllers (`system/api/`)
                                                          |
                                           +--------------+--------------+
                                           |                             |
                                  [ Content Manager ]           [ Auth & Token Guard ]
                                           |                             |
                                  +--------+--------+                    |
                                  |                 |                    v
                             /content/          /cache/         config/api_keys.ini
```

---

## 2. Refactoring Strategy

1. **Modularizing `system/htmly.php`**:
   - `system/api/v1/router.php`: Routing khusus untuk endpoint `/api/v1/*`.
   - `system/api/v1/controllers/`: Handler terpisah untuk PostController, MediaController, dan AuthController.
   - `system/core/Security.php`: Keamanan path traversal, CSRF token, dan API key validation.

2. **API Authentication Mechanism**:
   - Pengguna dapat membuat API Key di panel Admin.
   - Header HTTP: `Authorization: Bearer <HTMLY_API_KEY>`.
   - API Key diverifikasi terhadap `config/api_keys.ini`.

3. **Flat-File Lock Guarding**:
   - Menggunakan `LOCK_EX` pada operasi penulisan file Markdown & JSON index untuk mencegah race condition pada akses simultan dari n8n/AI Agent.
