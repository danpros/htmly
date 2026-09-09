<?php if (!defined('HTMLY')) die('HTMLy'); ?>

<?php if (!empty($breadcrumb)): ?>
<nav class="breadcrumb" aria-label="Breadcrumb"><?php echo editorial_fix_breadcrumb($breadcrumb); ?></nav>
<?php endif; ?>

<div class="content-grid">
  <article class="main-content single-post">

    <?php if (function_exists('authorized') && authorized($p)): ?>
      <a class="edit-link" href="<?php echo $p->url; ?>/edit?destination=post">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
        Edit post
      </a>
    <?php endif; ?>

    <header class="post-header">
      <div class="meta-row">
        <?php if (!empty($p->category)): ?><span class="tax-pill"><?php echo $p->category; ?></span><?php endif; ?>
        <?php if (!empty($p->readTime)): ?><span class="meta-item dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg><?php echo $p->readTime; ?></span><?php endif; ?>
        <?php if (!empty($p->views)): ?><span class="meta-item dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg><?php echo $p->views; ?> views</span><?php endif; ?>
      </div>

      <h1 class="post-title"><?php echo $p->title; ?></h1>
      <?php if (!empty($p->description)): ?><p class="post-lede"><?php echo $p->description; ?></p><?php endif; ?>

      <div class="author-row">
        <span class="author-avatar" aria-hidden="true"><?php echo editorial_initials($p->author); ?></span>
        <div>
          <div class="who"><a href="<?php echo $p->authorUrl; ?>"><?php echo $p->author; ?></a></div>
          <div class="when"><?php echo format_date($p->date); ?><?php if (!empty($p->lastMod) && $p->lastMod != $p->date): ?> · updated <?php echo format_date($p->lastMod); ?><?php endif; ?></div>
        </div>
      </div>
    </header>

    <?php if (!empty($p->image)): ?>
    <figure class="post-cover post-cover--img">
      <img src="<?php echo $p->image; ?>" alt="<?php echo htmlspecialchars(strip_tags($p->title)); ?>" width="1280" height="720" loading="eager" decoding="async" fetchpriority="high">
    </figure>
    <?php endif; ?>

    <?php if (!empty($p->video)): ?>
      <div class="post-cover"><?php echo $p->video; ?></div>
    <?php endif; ?>
    <?php if (!empty($p->audio)): ?>
      <div class="post-cover"><?php echo $p->audio; ?></div>
    <?php endif; ?>
    <?php if (!empty($p->quote)): ?>
      <blockquote class="post-content post-quote-block"><?php echo $p->quote; ?></blockquote>
    <?php endif; ?>
    <?php if (!empty($p->link)): ?>
      <p><a class="btn" href="<?php echo $p->link; ?>" rel="nofollow noopener" target="_blank">Visit link ↗</a></p>
    <?php endif; ?>

    <?php $editorial_ad_top = editorial_ad_code('ads_post_top_code'); ?>
    <?php if ($editorial_ad_top !== ''): ?>
    <div class="ad-slot ad-slot--in-post">
      <span class="ad-label">Advertisement</span>
      <?php echo $editorial_ad_top; ?>
    </div>
    <?php endif; ?>

    <div class="post-content">
      <?php echo $p->body; ?>
    </div>

    <?php if (!empty($p->category) || (!empty($p->tag) && editorial_visible('hide_post_tags'))): ?>
    <div class="post-taxonomy">
      <?php if (!empty($p->category)): ?>
        <span class="label">Filed under</span>
        <span class="tax-pill"><?php echo $p->category; ?></span>
      <?php endif; ?>
      <?php if (!empty($p->tag) && editorial_visible('hide_post_tags')): ?>
        <span class="label">Tagged</span>
        <span class="tax-pill tax-pill-tags"><?php echo $p->tag; ?></span>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="share-row" role="group" aria-label="Share this post">
      <a class="icon-btn" target="_blank" rel="noopener nofollow" aria-label="Share on X" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($p->url); ?>&text=<?php echo urlencode(strip_tags($p->title)); ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l16 16M20 4L4 20"/></svg>
      </a>
      <a class="icon-btn" target="_blank" rel="noopener nofollow" aria-label="Share on Facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($p->url); ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3Z"/></svg>
      </a>
      <a class="icon-btn" aria-label="Share via email" href="mailto:?subject=<?php echo urlencode(strip_tags($p->title)); ?>&body=<?php echo urlencode($p->url); ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>
      </a>
    </div>

    <?php $editorial_ad_bottom = editorial_ad_code('ads_post_bottom_code'); ?>
    <?php if ($editorial_ad_bottom !== ''): ?>
    <div class="ad-slot ad-slot--in-post">
      <span class="ad-label">Advertisement</span>
      <?php echo $editorial_ad_bottom; ?>
    </div>
    <?php endif; ?>

    <?php $editorial_related = get_related($p->related, true); ?>
    <?php if (!empty($editorial_related) && editorial_visible('hide_related_posts')): ?>
    <section class="related-section" aria-label="Related posts">
      <h2 class="section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
        Related posts
      </h2>
      <div class="related-grid">
        <?php foreach ($editorial_related as $r): ?>
          <?php $rThumb = editorial_thumb($r); ?>
          <article class="card post-card no-image">
            <div class="body">
              <div class="meta-row"><span class="meta-item dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><?php echo format_date($r->date); ?></span></div>
              <h3><a href="<?php echo $r->url; ?>"><?php echo $r->title; ?></a></h3>
              <a class="read-more" href="<?php echo $r->url; ?>">Read more<span class="visually-hidden"> about <?php echo strip_tags($r->title); ?></span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <?php if ((!empty($prev) || !empty($next)) && editorial_visible('hide_post_nav')): ?>
    <nav class="post-nav" aria-label="Post navigation">
      <?php if (!empty($prev)): ?>
        <a class="prev" href="<?php echo $prev['url']; ?>">
          <span class="nav-dir">← Previous</span>
          <span class="nav-title"><?php echo $prev['title']; ?></span>
        </a>
      <?php else: ?><span></span><?php endif; ?>
      <?php if (!empty($next)): ?>
        <a class="next" href="<?php echo $next['url']; ?>">
          <span class="nav-dir">Next →</span>
          <span class="nav-title"><?php echo $next['title']; ?></span>
        </a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>

    <?php if (function_exists('disqus_count') && disqus_count()): ?>
    <section style="margin-top:3rem;">
      <?php echo disqus($p->title, $p->url); ?>
    </section>
    <?php endif; ?>

  </article>

  <?php include __DIR__ . '/partials/sidebar.html.php'; ?>
</div>
