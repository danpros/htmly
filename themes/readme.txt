HTMLy Themes Directory (v3.2.0)
==============================


Default Included Themes:
- /blog          : Classic blogging theme with sidebar widgets and category navigation.
- /clean         : Minimalist, fast, and distraction-free theme layout.
- /doks          : Documentation and knowledge base theme structure.
- /logs          : Logbook & personal diary layout.
- /readable      : High-readability typography theme.
- /tailwind      : Modern utility-first Tailwind CSS theme setup.
- /twentyfifteen  : Responsive WordPress-style legacy theme port.
- /twentysixteen : Clean magazine-style legacy theme port.

Custom Theme Guidelines:
1. Create a new directory inside `/themes/<your-theme-name>/`.
2. Include at least `layout.html.php`, `main.html.php`, `post.html.php`, and `static.html.php`.
3. Set your active theme in `config/config.ini` under `views.root = "themes/<your-theme-name>"`.