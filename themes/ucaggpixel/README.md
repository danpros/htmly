# UCAGG Pixel (HTMLy Theme)

Retro pixel / space theme designed for the UCAGG network (blogs/files/cloud/games/projects). Built for readability, speed, and a consistent “signal over noise” aesthetic.

## Highlights

- Pixel + space UI with lightweight animated starfield
- Optional “petals” overlay (pink pixel-dots) toggle
- HUB sidebar buttons with per-button **label / icon / link** configurable from Theme Settings
- Mobile header fix (social icons never slide off-screen)
- Post lists show: **thumbnail + title + ~3 lines** (no full-post dumps)
- Author/profile pages match the home post-card style
- Prev/Next navigation on single posts
- Admin author badge (pixel “Master Sage” icon) next to admin authors

## Included Files

```
themes/ucaggpixel/
  css/style.css
  js/effects.js
  images/
    ucagg_blog_logo_1024.png
    ucagg_cloud_logo_1024.png
    ucagg_files_logo_1024.png
    ucagg_games_logo_1024.png
    ucagg_projects_logo_1024.png
    admin_sage_pixel.png
    screenshot.png
  404.html.php
  404-search.html.php
  layout.html.php
  main.html.php
  no-posts.html.php
  post.html.php
  profile.html.php
  static.html.php
  theme.json
  README.md
  LICENSE.txt
```

## Requirements

- HTMLy (PHP 7.2+).  
- Theme settings via `theme.json` are supported in newer HTMLy versions. (HTMLy documents theme settings and `theme_config()` usage.) 

## Install (end user)

1. Upload theme folder into:
   `themes/ucaggpixel/`
2. In HTMLy Admin → Themes → select **UCAGG Pixel**
3. Clear cache by deleting contents of `cache/`

## Theme Settings

Open: Admin → Themes → **UCAGG Pixel** → Settings

### Social links
- `linkedin_url` — URL for LinkedIn icon
- `github_url` — URL for GitHub icon

### Footer
- `footer_text` — fallback footer text if global blog copyright isn’t set

### Effects
- `petals` — enable/disable petals overlay

### HUB menu
- `show_hub` — show/hide HUB card entirely
- `hub_title` — title above HUB buttons

For each HUB button (Blog/Cloud/Files/Games/Projects):
- `hub_<name>` — URL
- `hub_<name>_label` — visible label text
- `hub_<name>_icon` — icon filename under `themes/ucaggpixel/images/`

## Notes for maintainers

- Screenshot: HTMLy will display a theme screenshot if `screenshot.*` exists. This theme includes `screenshot.png`.  
- No external dependencies required.

## License

GPL-2.0-or-later (same compatibility direction as HTMLy). See `LICENSE.txt`.
