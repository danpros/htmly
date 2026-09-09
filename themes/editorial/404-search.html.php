<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<div class="content-grid">
  <div class="main-content">
    <div class="empty-state">
      <span class="emoji-mark" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </span>
      <h1>No results found</h1>
      <p>Try a different keyword, or browse recent posts from the sidebar instead.</p>
      <div style="max-width:360px;margin-inline:auto;">
        <?php echo editorial_search('Search again'); ?>
      </div>
    </div>
  </div>
  <?php include __DIR__ . '/partials/sidebar.html.php'; ?>
</div>
