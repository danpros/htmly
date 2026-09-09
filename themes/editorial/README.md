# Editorial — an HTMLy theme

A fast, magazine-style theme built for `htmly.edil.pro`, following HTMLy's
official theming docs (structure, variables, advanced theming, widgets).

## Install

1. Upload the whole `editorial` folder to `themes/` on your server, so you get:
   `themes/editorial/layout.html.php`, etc.
2. In HTMLy admin → **Settings → Config**, set **Theme** to `editorial` and save.
3. Hard-refresh the site once (Ctrl/Cmd+Shift+R) so your browser picks up the
   new stylesheet.

That's it — no build step, no npm, no database. It's plain PHP + one CSS file
+ one tiny JS file, exactly like every other HTMLy theme.

## What's inside

```
editorial/
  css/style.css        one stylesheet, ~28KB (~6KB gzipped)
  js/main.js            ~4KB (<1KB gzipped) — dark mode toggle, back-to-top,
                         closes the mobile menu after a tap
  img/favicon.svg        placeholder monogram favicon — swap for your logo
  functions.php          theme helpers (excerpts, initials, cache-busting,
                          thumbnail fallback) — auto-loaded by HTMLy
  partials/
    post-card.html.php   the post card used on home/category/tag/profile
    sidebar.html.php     search + recent + popular + categories + tags + archive
  layout.html.php        header, nav, footer, dark-mode + all <head> tags
  main.html.php          post listing (home, category, tag, archive, search)
  post.html.php          single post
  static.html.php        static pages (+ sub-page grid for parent pages)
  substatic.html.php     nested static pages (e.g. /about/me)
  profile.html.php       author page
  no-posts.html.php / 404.html.php / 404-search.html.php
```

## Design notes

- **No web fonts, no icon fonts, no external CDN calls.** Every icon is
  inline SVG and typography uses the visitor's system font stack (with good
  Greek-glyph coverage). That means the browser makes exactly **one**
  request for CSS and **one** for JS — nothing else — which keeps things
  fast on top of HTMLy's own page cache.
- **Dark mode** follows the visitor's OS setting automatically, plus a
  manual toggle (top right) that's remembered via `localStorage`. A tiny
  inline script in `<head>` applies the saved preference before first paint,
  so there's no flash of the wrong theme — and because it runs entirely in
  the browser, the cached HTML HTMLy serves stays identical for every
  visitor either way.
- **Cache-friendly by design.** HTMLy caches whole rendered pages, so this
  theme never does anything that would make a cached page render
  differently per-visitor (no server-side sessions, no per-request
  randomness). The one place assets need to change without waiting for a
  cache purge — your CSS/JS files — uses `editorial_ver()` in
  `functions.php`, which appends the file's own last-modified time as a
  `?v=` query string. Edit `style.css`, reload, and visitors get the new
  styles immediately, while unrelated pages stay served straight from
  HTMLy's cache.
- **Mobile menu and search reveal are pure CSS** (checkbox toggles) — no
  JavaScript required for the site to be fully navigable. JS only adds the
  dark-mode toggle, the back-to-top button's show/hide, and closing the
  mobile drawer after tapping a link.
- **Images** use `loading="lazy"` (except the single-post cover, which is
  `eager`/`fetchpriority="high"` since it's above the fold) and posts
  without an image fall back to a CSS gradient monogram instead of a broken
  image or an external placeholder request.
- **Layout**: a two-column grid (content + sidebar) on desktop that
  collapses to one column on mobile; the very first post on the homepage's
  first page renders as a larger "featured" card.

## Customizing colors

Everything is driven by CSS variables at the top of `css/style.css`:

```css
:root {
  --accent: #a8452f;         /* your brand/accent color */
  --accent-strong: #8a3423;  /* hover/darker variant */
  --bg: #fbfaf8;             /* page background */
  ...
}
```

Change `--accent` and `--accent-strong` and the whole theme re-colors
(badges, links, buttons, drop-caps, hover states). Dark mode has its own
matching block right below, plus a `prefers-color-scheme` copy for visitors
who haven't toggled manually.

## Second round: accessibility 89 → fixed

A follow-up PageSpeed run (after the fixes above) came back 100/89/100/92 —
performance, best-practices and SEO all hit 100, but accessibility flagged
two concrete, confirmed issues:

**"Form elements do not have associated labels."**
HTMLy's own core `search()` function (in `system/includes/functions.php`,
not part of this theme) renders its `<input>` with a `class`, a `name`,
and no `id`, `<label>`, or `aria-label` — there's no theme-level hook to
add one at the source. Since every search box on the page (header,
sidebar, 404-search) is generated this same way, `js/main.js` now adds
`aria-label="Search"` to any `input.search-input` at runtime, satisfying
the audit without needing to touch core.

**"Lists do not contain only `<li>` and script-supporting elements
(`<script>` and `<template>`)."**
Two confirmed causes:
  - This theme's own sidebar/footer previously wrapped `category_list()`
    and `archive_list()` in an extra `<ul class="simple-list">` —
    but those two core functions already return a complete `<ul><li>...`
    block by default, so the result was an invalid `<ul>` directly
    inside another `<ul>`. **Fixed** by wrapping them in a plain `<div>`
    instead everywhere they're used (sidebar + footer); no CSS changes
    were needed since the existing rules already targeted `.simple-list
    li` / `.simple-list a` as descendants, and the theme's own reset
    already strips default list styling from any nested `<ul>`.
  - HTMLy core's breadcrumb output (`$breadcrumb`, an `<ol
    class="breadcrumb-list" itemscope itemtype="…BreadcrumbList">`) can
    place non-`<li>` elements directly inside that `<ol>` — again,
    core-generated markup the theme has no template-level access to.
    `js/main.js` now includes a small generic repair: it walks every
    `<ul>`/`<ol>` on the page and wraps any direct child that isn't
    `<li>`/`<script>`/`<template>` in an `<li>`. This is a no-op on
    markup that was already valid (verified with a standalone test),
    and fixes both the breadcrumb and category/archive cases uniformly
    without guessing at core's exact internal HTML.

If you run PageSpeed again and still see something under 100, check
whether it's again coming from a core-generated element (view-source
and look for anything not written by this theme's `.html.php` files) —
those need this same "fix the rendered DOM" approach since the theme
can't edit `system/` files directly, whereas anything inside the theme
folder itself should be reported so it can be fixed at the source.

## Lighthouse fixes (performance / accessibility)

If you ran this theme through PageSpeed Insights / Lighthouse, here's
exactly what was found and what changed:

**Accessibility — failing color contrast.**
`--text-faint` (used for dates, read-time, view counts, footer credit)
was `#948d86`, which only hits a 3.1–3.3:1 contrast ratio against the
background — below the WCAG AA minimum of 4.5:1 for normal-size text.
The dark-mode value had the same problem (4.45:1, just under). Both are
now darkened/lightened to `#726a62` (light) and `#968c80` (dark), giving
a safe 5.0–5.5:1 ratio. The accent color was also darkened slightly
(`#a8452f` → `#903a26`) so category pills and links clear AA with margin
too — verified with an actual WCAG contrast calculation, not eyeballing.

**Performance — render-blocking request.**
The theme previously linked `css/style.css` and `js/main.js` as normal
`<link>`/`<script>` tags, each costing one extra network round trip
before the page could render. Both are now inlined directly into
`layout.html.php` (`file_get_contents()` into a `<style>`/`<script>`
block), with an automatic fallback to the old `<link>`/`<script src>`
tags if the file can't be read for any reason. A fully cached HTMLy page
now needs exactly one HTTP request total for CSS+JS+HTML combined.

  *Trade-off to know about:* because the CSS/JS are baked into HTMLy's
  cached HTML output, editing `style.css` or `main.js` won't show up on
  already-cached pages until you clear HTMLy's page cache (Settings →
  clear cache, or just wait for it to expire). Content edits (new posts,
  etc.) are unaffected — this only applies to theme file edits.

**Performance — Cumulative Layout Shift.**
Two images didn't reserve their box size before loading, so the page
shifted around as they loaded in:
  - the single-post cover image now has a fixed 16:9 box
    (`.post-cover--img`) with `object-fit: cover`, plus matching
    `width`/`height` attributes;
  - the homepage's featured (first) post card already had matching
    `width`/`height` attributes tied to its CSS `aspect-ratio`, and the
    featured image is loaded `eager`/`fetchpriority="high"` (it's the
    Largest Contentful Paint candidate) while every other card image
    stays `loading="lazy"`.

**What's outside the theme's control.**
Two Lighthouse notes can't be fixed from a theme alone:
  - *"Improve image delivery"* — this is about the resolution/weight of
    the actual photos you upload through HTMLy, not the theme markup.
    Compressing images before upload (e.g. with Squoosh) or keeping
    them close to their display size helps here.
  - *"Use efficient cache lifetimes"* — this is an HTTP response-header
    setting on your web server, not something PHP/CSS can set from
    inside the theme. See `htaccess-caching-snippet.txt` in this folder
    for a ready-to-paste Apache snippet that sets long `Cache-Control`
    headers for images/fonts on `htmly.edil.pro`.

## Round 2 fixes (accessibility 89 → SEO 92 regressions)

A follow-up PageSpeed Insights run showed Performance at a clean **100**,
but Accessibility and SEO had dipped below the default theme's baseline.
Root causes, found by inspecting the actual rendered markup rather than
guessing:

- **SEO — generic "Read more" link text.** Lighthouse's link-text audit
  blocklists phrases like "read more", "click here", "learn more" as
  non-descriptive, regardless of visual design intent. Every post card
  used exactly that phrase. Fixed by adding a visually-hidden span with
  the post's title right after the visible "Read more" text, so the
  link's accessible name becomes "Read more about {title}" — visually
  unchanged, but descriptive and unique per link.

- **Accessibility/SEO — thumbnail links with zero accessible name.**
  The image-only links wrapping post thumbnails were marked
  `aria-hidden="true"` (to avoid a screen reader announcing the same
  title twice — once for the image, once for the text link below it).
  Combined with an empty `alt`, that left the link with *no* accessible
  name at all, which fails both the accessibility "links need a
  discernible name" check and the SEO "links need descriptive text"
  check. Fixed by keeping the image decorative (`alt=""`) but moving a
  real `aria-label` (the post title) onto the anchor itself, and
  dropping `aria-hidden` — the link still isn't part of the keyboard
  tab order (`tabindex="-1"`, since the title link below covers that),
  but it now has a genuine, descriptive name for anyone browsing by
  screen reader.

- **Accessibility — unlabeled search input (HTMLy core).** HTMLy's
  built-in `search()` function renders its own `<form>`/`<input>`
  markup with no `<label>`/`aria-label` of its own, and there's no
  theme hook to add one at the source. `main.js` now runs a small,
  generic fix at the end of the page: any text/search input inside a
  `<form>` that has no accessible name yet gets `aria-label="Search"`
  added — a safe no-op anywhere a label already exists, and it doesn't
  depend on guessing HTMLy's internal class/id names.

- **Defensive: list-structure repair.** Some HTMLy core widget/breadcrumb
  functions can place a non-`<li>` element directly inside a `<ul>`/`<ol>`,
  which is invalid HTML. `main.js` also does a harmless DOM pass that
  wraps any stray non-`<li>` child of a list in an `<li>`, in case that
  ever happens on this install.

## Round 3 fixes (accessibility 95, SEO 92, + two functional bugs)

A third PageSpeed run showed two remaining automated findings, plus you
reported the search box and the back-to-top arrow not working. Here's
what was actually wrong and what changed:

- **Accessibility — invalid list structure in the breadcrumb.** HTMLy
  core's own breadcrumb output apparently places the current page's
  title as bare text directly inside the `<ol>`, not wrapped in an
  `<li>` (a list may only directly contain `<li>`, `<script>`, or
  `<template>`). The theme's list-repair script only checked
  `list.children`, which — importantly — excludes text nodes entirely,
  so it silently missed exactly this case. Rewritten to walk
  `list.childNodes` instead, wrapping any non-whitespace text found
  directly inside a list in its own `<li>`.

- **Accessibility — heading levels skipped.** Two separate spots jumped
  levels: the sidebar's "recent/popular posts" widget used `<h4>` with
  no `<h3>` before it on most page types (skipping straight from
  `<h1>` or `<h2>`), and the site footer used `<h5>` on every single
  page regardless of what heading level came before it. Neither of
  these widget/footer labels needs to be a real document heading, so
  both are now plain `<p>` elements styled identically via CSS — this
  removes the skip risk entirely rather than trying to keep re-tuning
  heading numbers as the sidebar's position varies by page type. A
  third spot (`static.html.php`'s sub-page cards) legitimately did need
  a heading, and was fixed by correcting `<h3>` to `<h2>` since only
  `<h1>` precedes it on that page.

- **SEO — archive widget's toggle links aren't crawlable.** HTMLy
  core's `archive_list()` widget renders its expand/collapse controls
  as `<a href="javascript:void(0)" onclick="...">`, which Lighthouse
  correctly flags since that href isn't a real destination — and a
  `<button>` would have been the semantically correct element for a
  JS-only action in the first place. `main.js` now converts any such
  anchor into a real `<button type="button">` at runtime, carrying
  over its existing `onclick` behavior unchanged (it only ever
  references `this.parentNode`, which resolves the same way regardless
  of tag name). Buttons aren't evaluated by the "crawlable links" check
  at all, so this is a full fix, not a workaround.

- **Bug — search box not working.** The header's search reveal used a
  guessed `max-height: 90px` with `overflow: hidden`. If HTMLy core's
  actual search form (input + button) ever needed slightly more height
  than that on some screen — wrapping to two lines on a narrow phone,
  larger tap targets, different padding — the submit button would be
  silently clipped outside the visible box and become unclickable,
  even though it looked present. Replaced with a `grid-template-rows:
  0fr → 1fr` technique, which sizes itself to whatever height the real
  content needs instead of a guessed pixel value, so nothing can ever
  be cropped.

- **Bug — back-to-top arrow not working.** This button's visibility is
  gated entirely by a small JS scroll listener (`.visible` class on
  scroll). Previously, every feature lived in one script with no error
  isolation between them — if *any* single block hit real-world HTMLy
  markup it didn't expect and threw an exception (most likely one of
  the core-quirk repair scripts, which this theme has no way to test
  against your actual live HTML), every feature declared *after* it in
  the file would silently stop running too, including this one.
  `main.js` is now restructured so each feature runs inside its own
  isolated `try/catch` — one failing repair can no longer take the
  others down with it.

  *If the arrow or search still don't respond after re-uploading*:
  please hard-refresh the page (Ctrl/Cmd+Shift+R) and clear HTMLy's
  page cache once from the admin panel. Since this theme inlines its
  CSS/JS into the cached HTML itself, a page cached from before this
  update won't pick up any of these fixes until that cache entry is
  regenerated.

## Round 4 fixes (a real markup-corruption bug, found from your live site)

This round's screenshots included something the previous rounds
didn't: your actual rendered HTML. That made it possible to find a
real bug directly instead of guessing.

- **Bug — the "﹥" glyph next to the category badge, and duplicated
  category text.** Confirmed straight from your live page's own
  output: `$p->category` and `$p->tag` are **not** plain text in
  HTMLy core — they're already complete `<a href="...">Name</a>`
  links. This theme was wrapping that *again* in its own `<a
  class="tax-pill" href="...">`, producing a broken nested anchor: the
  `href` attribute value got cut short by the embedded quote from the
  inner link, the browser recovered by rendering a stray `>`, and then
  displayed the real (but unstyled) inner link a second time — exactly
  what you circled. This affected **every post card sitewide**
  (homepage, category, tag, archive, search, profile pages all use the
  same card partial) plus the single-post header and its "Filed
  under / Tagged" line. Fixed by no longer rebuilding those links at
  all — the theme now wraps whatever core already renders in a
  styled `<span>` instead of a second, conflicting `<a>`.

- **Accessibility — desktop-only: unlabeled `#nav-toggle` checkbox.**
  The mobile hamburger menu's checkbox has a `<label for="nav-toggle">`
  that correctly becomes `display:none` on desktop (there's no
  hamburger menu there). But `display:none` also removes that label
  from the accessibility tree, which left the checkbox itself
  unlabeled specifically on desktop — exactly what your desktop
  PageSpeed run flagged (this didn't show up on mobile, where the
  label stays visible). Fixed with a permanent `aria-label` directly
  on the checkbox, independent of whatever its visual label happens to
  be doing at a given screen width.

- **Bug — back-to-top button visible but click does nothing.** The
  button's *visibility* was confirmed working (your screenshot shows
  it fully opaque), so the JS was running fine up to that point — the
  failure was specifically that clicking a plain `href="#top"` anchor
  wasn't reliably scrolling. Rather than keep trying to diagnose why
  in a browser I can't drive interactively, `main.js` now handles the
  click directly with `window.scrollTo({top:0, behavior:'smooth'})`,
  which can't depend on how a given browser resolves an anchor jump
  against a sticky header. The plain `href="#top"` stays in the markup
  as a no-JS fallback.

- **Not a theme bug — sidebar widgets need enabling in HTMLy's admin.**
  Your screenshot of Settings shows **Recent Posts, Popular Posts,
  Archive, Category List,** and **TagCloud** widgets all unchecked.
  HTMLy's `recent_posts()`, `popular_posts()`, `archive_list()`,
  `category_list()`, and `tag_cloud()` functions only return content
  when their matching checkbox is enabled there — this is a core
  on/off switch the theme calls into, not something a theme can
  override. If the sidebar looks sparse, check the ones you want under
  **Settings → Config**, save, and they'll populate. (Popular Posts
  additionally needs the views counter enabled first, as the settings
  page itself notes.)

## Round 5 fixes (W3C HTML validator — full markup validity)

Lighthouse and a static HTML validator check different things — a page
can score 100/100/100/100 on Lighthouse while still containing invalid
HTML, because several of the previous fixes (the search-input label,
the list-nesting repair) ran as client-side JavaScript, which
Lighthouse executes but a static validator does not. This round moves
the real fixes to the server side, so the raw HTML is valid before any
JavaScript ever runs, and cleans up everything else the validator
flagged:

- **`<style>` not allowed inside `<nav>`, and text not allowed directly
  inside `<ol>`.** Both came from HTMLy core's own breadcrumb output —
  it injects a `<style>` block, and renders the "»" separators and the
  final (current-page) breadcrumb segment as bare content sitting
  directly inside the `<ol>` rather than wrapped in `<li>`. The
  previous round's fix for this ran as a client-side JS repair, which
  works for real visitors and for Lighthouse (which runs JS) but
  doesn't touch what a static validator sees. `functions.php` now has
  `editorial_fix_breadcrumb()`, which parses core's breadcrumb HTML
  server-side with PHP's DOMDocument, strips the stray `<style>`, and
  merges each stray separator/title into the `<li>` it belongs with —
  so the HTML sent to the browser is valid from the start. (Falls back
  to returning the original untouched if the DOM extension isn't
  available, though it's a near-universal PHP extension.)

- **Duplicate `id="search-form"`.** HTMLy core's `search()` function
  hardcodes that id on every form it renders, and this theme calls it
  more than once per page (header + sidebar widget, and again on the
  404-search page). `functions.php` now has `editorial_search()`, a
  thin wrapper that leaves the first call on any page untouched and
  renames the id on every subsequent call so they stay unique.

- **`aria-label` on a `<label>` already associated with its checkbox,
  and `aria-hidden` on the same pattern.** Per the ARIA/HTML spec, a
  `<label for="...">` that's already tied to a form control isn't
  allowed to carry its own `aria-label` (or be `aria-hidden`) —
  that's specifically for the *checkbox itself* to carry, which it
  already did after the round-4 fix for the desktop unlabeled-checkbox
  finding. Removed the redundant/invalid attributes from the two
  `<label>` elements; the checkboxes' own `aria-label` still provides
  the accessible name, so nothing about the actual accessibility fix
  is lost.

- **`aria-label` on a plain `<div>`.** The share-buttons row was a bare
  `<div aria-label="Share this post">` — a `<div>`'s default role
  doesn't support naming. Added `role="group"`, which does, and also
  gives assistive tech a nicer way to announce the buttons as a set.

- **`lang="en-US"` on Greek content.** `blog_language()` is driven by
  a site-wide admin setting, and it's returning the wrong value for
  this install. Since this theme is built specifically for a
  Greek-language blog, `<html lang>` is now hardcoded to `el` rather
  than depending on that setting.

- **Trailing `/>` on `<meta>`/`<link>` tags.** Purely cosmetic — HTML5
  ignores the trailing slash on void elements, so this changed nothing
  about how the page renders or scores. Removed anyway for a clean
  validator pass. (Any remaining `/>` you'll see in the source are on
  SVG icons, where it's correct and required — those aren't touched.)

None of the above affects Lighthouse's four scores — they were
already accounted for by the client-side patches in earlier rounds.
This round makes the *underlying HTML* correct too, not just what a
browser ends up with after JavaScript runs.

## Round 6: theme settings panel (visibility toggles, ads, sponsors)

This theme now has its own settings page — go to **Themes → Editorial**
in the admin panel to find it. It uses HTMLy 3.1.1's built-in theme
settings feature (`theme.json` + `theme_config()`), the same mechanism
other themes like Tailwind use, so there's nothing extra to install.

### Visibility toggles

Checkboxes to hide, per page:
- Post tags (single posts)
- Post navigation (prev/next links)
- Related posts section
- Each sidebar widget individually: Recent Posts, Popular Posts,
  Categories, Tags, Archive

These are phrased as **"Hide X"**, not "Show X" — on purpose. A
checkbox that's never been touched reads as empty/off in HTMLy, so if
these were phrased as opt-in "Show X" boxes, everything would vanish
the moment this update goes live, until every box was manually
re-checked. With "Hide X", leaving everything unchecked (the default,
including right after upload) means the site looks exactly like it did
before — you only turn things off if you actually want to.

(The Search widget in the sidebar isn't in this list — it stays on
always, same as the header search.)

### Google Ads

Three independent ad slots, each with its own code box, plus one
master switch:
- **Enable Ads** — turns every slot on/off at once, so you can pause
  all ads without clearing out the pasted codes
- **Sidebar Ad Code** — its own widget in the sidebar
- **In-Post Ad Code (top)** — shown once near the top of every single
  post, after any cover image/video and before the article text
- **In-Post Ad Code (bottom)** — shown once after the article text and
  share buttons, before related posts

Paste the *full* snippet Google (or any other ad network) gives you
into each box — script tags and all. It's rendered as trusted HTML
(same as HTMLy's own `disqus()`/`analytics()` output), with a small
"Advertisement" label above it for transparency. An empty box simply
shows nothing, so you can fill in only the placements you want.

### Sponsors (private/paid placements)

Three sponsor slots in the sidebar, each with its own enable checkbox,
name, logo URL, and link URL. Enabled sponsors appear together in a
single "Sponsors" widget, positioned right after the Tags widget as
requested — each with a small "Sponsored" badge on the logo itself for
transparency. A slot only appears once it has a logo URL *and* a link
URL saved, in addition to being checked on.

For the logo, upload the image through HTMLy's own media library
first (Posts → any post → image uploader, or wherever your install
exposes it), then paste the resulting URL into the sponsor's "Logo
URL" field — theme settings only take a URL, not a file upload.

## Notes

- Sidebar widgets (`recent_posts()`, `popular_posts()`, `category_list()`,
  `tag_cloud()`, `archive_list()`, `search()`) are all official HTMLy
  widget functions — nothing custom or fragile.
- `functions.php` only defines `editorial_*`-prefixed functions, so it
  won't collide with core HTMLy or any other plugin/theme.
- If you'd like a child-theme approach instead (e.g. only overriding
  colors), HTMLy supports that too — see "Child Theme" in the docs.
