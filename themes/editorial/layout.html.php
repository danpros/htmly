<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="el">
<head>
<?php echo head_contents(); ?>
<title><?php echo $title; ?></title>
<meta name="description" content="<?php echo $description; ?>">
<link rel="canonical" href="<?php echo $canonical; ?>">
<meta name="theme-color" content="#a8452f" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#17150f" media="(prefers-color-scheme: dark)">

<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo $title; ?>">
<meta property="og:description" content="<?php echo $description; ?>">
<meta property="og:url" content="<?php echo $canonical; ?>">
<meta name="twitter:card" content="summary_large_image">

<link rel="icon" type="image/svg+xml" href="<?php echo theme_path(); ?>img/favicon.svg">
<?php
/**
 * The CSS is inlined rather than linked. HTMLy caches the whole rendered
 * page, so once this page is cached, inlining costs nothing extra per
 * visit — but it removes the one render-blocking network request that
 * Lighthouse otherwise flags, which is worth more to First Contentful
 * Paint than a theoretically-cacheable separate .css file. If you edit
 * style.css, clear HTMLy's page cache (admin panel) so already-cached
 * pages pick up the change — content pages don't need this, only theme
 * asset edits do.
 */
$editorial_css_file = __DIR__ . '/css/style.css';
$editorial_css = is_readable($editorial_css_file) ? file_get_contents($editorial_css_file) : false;
?>
<?php if ($editorial_css !== false): ?>
<style><?php echo $editorial_css; ?></style>
<?php else: ?>
<link rel="stylesheet" href="<?php echo theme_path(); ?>css/style.css?v=<?php echo editorial_ver('css/style.css'); ?>">
<?php endif; ?>

<script>
/* Runs before first paint to avoid a light/dark flash. Tiny and blocking on purpose. */
(function(){try{var t=localStorage.getItem('editorial-theme');if(t){document.documentElement.setAttribute('data-theme',t);}}catch(e){}})();
</script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { echo toolbar(); } ?>

<input type="checkbox" id="nav-toggle" class="nav-toggle-input" aria-label="Toggle menu">

<header class="site-header" id="top">
  <div class="container header-inner">

    <a class="brand" href="<?php echo site_url(); ?>">
      <span class="brand-mark" aria-hidden="true"><?php echo editorial_initials(blog_title()); ?></span>
      <span class="brand-text">
        <span><?php echo blog_title(); ?></span>
        <?php if (blog_tagline()): ?><span class="brand-tagline"><?php echo blog_tagline(); ?></span><?php endif; ?>
      </span>
    </a>

    <nav class="main-nav" aria-label="Primary">
      <?php echo menu(); ?>
    </nav>

    <div class="header-actions">
      <label class="icon-btn search-toggle-label" for="search-toggle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </label>

      <button type="button" class="icon-btn" id="theme-toggle" aria-label="Toggle dark mode" aria-pressed="false">
        <svg class="theme-icon-light" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2.2M12 19.8V22M4.2 4.2l1.55 1.55M18.25 18.25l1.55 1.55M2 12h2.2M19.8 12H22M4.2 19.8l1.55-1.55M18.25 5.75l1.55-1.55"/></svg>
        <svg class="theme-icon-dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 14.5A8.5 8.5 0 1 1 9.5 4a6.8 6.8 0 0 0 10.5 10.5Z"/></svg>
      </button>

      <label class="icon-btn nav-toggle-label" for="nav-toggle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </label>
    </div>
  </div>

  <input type="checkbox" id="search-toggle" class="search-toggle-input" aria-label="Toggle search">
  <div class="header-search-panel">
    <div class="container">
      <?php echo editorial_search(i18n('Search') ?: 'Search'); ?>
    </div>
  </div>
</header>

<label for="nav-toggle" class="nav-scrim"></label>

<div class="page-wrap">
  <div class="container">
    <main id="main">
      <?php echo content(); ?>
    </main>
  </div>
</div>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a class="brand" href="<?php echo site_url(); ?>">
          <span class="brand-mark" aria-hidden="true"><?php echo editorial_initials(blog_title()); ?></span>
          <span class="brand-text"><span><?php echo blog_title(); ?></span></span>
        </a>
        <p><?php echo blog_description(); ?></p>
      </div>

      <div class="footer-col">
        <p class="footer-col-title">Explore</p>
        <ul>
          <li><a href="<?php echo site_url(); ?>">Home</a></li>
          <li><a href="<?php echo site_url(); ?>feed/rss">RSS Feed</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <p class="footer-col-title">Categories</p>
        <div><?php echo category_list(); ?></div>
      </div>
    </div>

    <div class="footer-bottom">
      <span><?php echo copyright(); ?></span>
      <span>Theme: <strong>Editorial</strong></span>
    </div>
  </div>
</footer>

<a href="#top" class="to-top" aria-label="Back to top">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
</a>

<?php if (analytics()): ?><?php echo analytics(); ?><?php endif; ?>
<?php
$editorial_js_file = __DIR__ . '/js/main.js';
$editorial_js = is_readable($editorial_js_file) ? file_get_contents($editorial_js_file) : false;
?>
<?php if ($editorial_js !== false): ?>
<script><?php echo $editorial_js; ?></script>
<?php else: ?>
<script src="<?php echo theme_path(); ?>js/main.js?v=<?php echo editorial_ver('js/main.js'); ?>" defer></script>
<?php endif; ?>
</body>
</html>
