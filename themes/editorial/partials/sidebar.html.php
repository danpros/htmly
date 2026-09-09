<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<aside class="sidebar" aria-label="Sidebar">

  <div class="widget">
    <div class="widget-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Search
    </div>
    <?php echo editorial_search('Go'); ?>
  </div>

  <?php if (editorial_visible('hide_widget_recent')): ?>
  <div class="widget">
    <div class="widget-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
      Recent Posts
    </div>
    <div class="mini-list">
      <?php $editorial_recent = recent_posts(true, 5); ?>
      <?php foreach ($editorial_recent as $rc): ?>
        <?php $rcThumb = editorial_thumb($rc); ?>
        <div class="mini-item">
          <a class="mini-thumb" href="<?php echo $rc->url; ?>" tabindex="-1" aria-label="<?php echo htmlspecialchars(strip_tags($rc->title)); ?>">
            <?php if ($rcThumb): ?>
              <img src="<?php echo $rcThumb; ?>" alt="" loading="lazy" decoding="async" width="54" height="54">
            <?php else: ?>
              <span class="thumb-fallback" aria-hidden="true"><?php echo editorial_initials($rc->title); ?></span>
            <?php endif; ?>
          </a>
          <div>
            <p class="mini-item-title"><a href="<?php echo $rc->url; ?>"><?php echo shorten($rc->title, 58); ?></a></p>
            <span class="mini-date"><?php echo format_date($rc->date); ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php $editorial_popular = editorial_visible('hide_widget_popular') ? popular_posts(true, 5) : []; ?>
  <?php if (!empty($editorial_popular)): ?>
  <div class="widget">
    <div class="widget-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      Popular
    </div>
    <div class="mini-list">
      <?php foreach ($editorial_popular as $pc): ?>
        <?php $pcThumb = editorial_thumb($pc); ?>
        <div class="mini-item">
          <a class="mini-thumb" href="<?php echo $pc->url; ?>" tabindex="-1" aria-label="<?php echo htmlspecialchars(strip_tags($pc->title)); ?>">
            <?php if ($pcThumb): ?>
              <img src="<?php echo $pcThumb; ?>" alt="" loading="lazy" decoding="async" width="54" height="54">
            <?php else: ?>
              <span class="thumb-fallback" aria-hidden="true"><?php echo editorial_initials($pc->title); ?></span>
            <?php endif; ?>
          </a>
          <div>
            <p class="mini-item-title"><a href="<?php echo $pc->url; ?>"><?php echo shorten($pc->title, 58); ?></a></p>
            <span class="mini-date"><?php echo isset($pc->views) ? $pc->views . ' views' : format_date($pc->date); ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php $editorial_sidebar_ad = editorial_ad_code('ads_sidebar_code'); ?>
  <?php if ($editorial_sidebar_ad !== ''): ?>
  <div class="widget ad-slot ad-slot--sidebar">
    <span class="ad-label">Advertisement</span>
    <?php echo $editorial_sidebar_ad; ?>
  </div>
  <?php endif; ?>

  <?php if (editorial_visible('hide_widget_categories')): ?>
  <div class="widget">
    <div class="widget-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>
      Categories
    </div>
    <div class="simple-list">
      <?php echo category_list(); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (editorial_visible('hide_widget_tags')): ?>
  <div class="widget">
    <div class="widget-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 11 3.83A2 2 0 0 0 9.59 3.24H4a1 1 0 0 0-1 1v5.59a2 2 0 0 0 .59 1.41l9.58 9.59a2 2 0 0 0 2.82 0l4.6-4.6a2 2 0 0 0 0-2.82Z"/><circle cx="7.5" cy="7.5" r="1.2"/></svg>
      Tags
    </div>
    <div class="tag-cloud">
      <?php echo tag_cloud(); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php $editorial_sponsors = editorial_sponsors(); ?>
  <?php if (!empty($editorial_sponsors)): ?>
  <div class="widget sponsor-widget">
    <div class="widget-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c1.9 0 3.4 1 4 2.2C11.1 6 12.6 5 14.5 5 18 5 20.5 8.5 18.5 12.5 16 16.65 12 21 12 21Z"/></svg>
      Sponsors
    </div>
    <div class="sponsor-list">
      <?php foreach ($editorial_sponsors as $sponsor): ?>
        <a class="sponsor-item" href="<?php echo htmlspecialchars($sponsor['link']); ?>" target="_blank" rel="noopener sponsored" title="<?php echo htmlspecialchars($sponsor['name']); ?>">
          <img src="<?php echo htmlspecialchars($sponsor['logo']); ?>" alt="<?php echo htmlspecialchars($sponsor['name']); ?>" loading="lazy" decoding="async">
          <span class="sponsor-badge">Sponsored</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if (editorial_visible('hide_widget_archive')): ?>
  <div class="widget">
    <details>
      <summary class="widget-title widget-title--flush">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Archive
      </summary>
      <div class="simple-list simple-list--spaced">
        <?php echo archive_list(); ?>
      </div>
    </details>
  </div>
  <?php endif; ?>

</aside>
