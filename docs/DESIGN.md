# UI/UX Design System & Theme Modernization Guidelines (DESIGN.md)

Dokumen ini mendefinisikan panduan estetika dan sistem desain modern 2026 untuk Admin Panel dan Frontend Theme **HTMLy**.

---

## 🎨 Design Principles (2026 Edition)

1. **Clean & Uncluttered**: Tampilan minimalis berfokus pada pengalaman menulis Markdown tanpa gangguan (distraction-free writing).
2. **Native Dark Mode Support**: Skema warna HSL yang adaptif terhadap preferensi sistem operasi pengguna (`prefers-color-scheme`).
3. **Subtle Glassmorphism & Elevation**: Menggunakan CSS `backdrop-filter: blur()` dan elevasi bayangan halus (`box-shadow`) untuk komponen modal & navbar.
4. **Fluid & Dynamic Layouts**: Menggunakan CSS Grid & Flexbox modern dengan `clamp()` untuk tipografi yang responsif di semua ukuran layar.

---

## 🎨 Color Palette & CSS Variables

```css
:root {
  /* Primary Spectrum */
  --primary-hsl: 220, 90%, 56%;
  --primary: hsl(var(--primary-hsl));
  --primary-hover: hsl(220, 90%, 48%);
  
  /* Surfaces & Backgrounds (Light Mode) */
  --bg-main: hsl(210, 20%, 98%);
  --bg-surface: hsl(0, 0%, 100%);
  --bg-glass: rgba(255, 255, 255, 0.75);
  --border-color: hsl(210, 14%, 90%);
  
  /* Text & Typography */
  --text-main: hsl(215, 25%, 15%);
  --text-muted: hsl(215, 12%, 48%);
  
  /* Status Colors */
  --success: hsl(145, 65%, 42%);
  --warning: hsl(38, 92%, 50%);
  --danger: hsl(354, 70%, 54%);
}

@media (prefers-color-scheme: dark) {
  :root {
    /* Surfaces & Backgrounds (Dark Mode) */
    --bg-main: hsl(220, 18%, 10%);
    --bg-surface: hsl(220, 16%, 14%);
    --bg-glass: rgba(22, 27, 34, 0.75);
    --border-color: hsl(220, 14%, 22%);
    
    /* Text & Typography */
    --text-main: hsl(210, 20%, 94%);
    --text-muted: hsl(215, 14%, 65%);
  }
}
```

---

## 🖥️ Key UI Components & Innovations

### 1. Command Palette (`Ctrl+K` / `Cmd+K`)
Modal pencarian dan navigasi cepat yang melayang di atas layar dengan efek glassmorphism:
```css
.command-palette-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
```

### 2. Modern Split Markdown Editor
- Panel kiri: Markdown Input dengan syntax highlighting halus & auto-indent.
- Panel kanan: Live HTML Preview dengan rendering instan via JavaScript Markdown parser.

### 3. Responsive Admin Sidebar & Micro-animations
- Transition halus `cubic-bezier(0.4, 0, 0.2, 1)` untuk tombol dan elemen interaktif.
- Indikator status (Published/Draft/Scheduled) menggunakan badge berwarna cerah dengan sudut membulat (`border-radius: 9999px`).
