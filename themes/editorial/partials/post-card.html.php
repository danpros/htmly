<?php if (!defined('HTMLY')) die('HTMLy');
/**
 * Expects: $p (post object), optional $featured (bool)
 */
$featured = !empty($featured);
$thumb = editorial_thumb($p);
$cardClasses = 'card post-card' . ($featured ? ' featured-card' : '') . ($thumb ? '' : ' no-image');
// The featured card is the Largest Contentful Paint candidate on the homepage,
// so it must never be lazy-loaded — lazy-loading the LCP element delays LCP
// and tanks the performance score. Cards further down the page stay lazy.
$imgLoading   = $featured ? 'eager' : 'lazy';
$imgFetchPrio = $featured ? ' fetchpriority="high"' : '';
$imgW = $featured ? 700 : 600;
$imgH = $featured ? 560 : 450; // matches each card type's CSS aspect-ratio, prevents layout shift
?>
<article class="<?php echo $cardClasses; ?>">
  <?php if ($thumb): ?>
  <a class="thumb" href="<?php echo $p->url; ?>" tabindex="-1" aria-label="<?php echo htmlspecialchars(strip_tags($p->title)); ?>">
    <img src="<?php echo $thumb; ?>" alt="" loading="<?php echo $imgLoading; ?>" decoding="async"<?php echo $imgFetchPrio; ?> width="<?php echo $imgW; ?>" height="<?php echo $imgH; ?>">
  </a>
  <?php endif; ?>

  <div class="body">
    <div class="meta-row">
      <?php if (!empty($p->category)): ?><span class="tax-pill"><?php echo $p->category; ?></span><?php endif; ?>
      <span class="meta-item dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><?php echo format_date($p->date); ?></span>
      <?php if (!empty($p->readTime)): ?><span class="meta-item dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg><?php echo $p->readTime; ?></span><?php endif; ?>
    </div>

    <h2><a href="<?php echo $p->url; ?>"><?php echo $p->title; ?></a></h2>

    <p class="excerpt"><?php echo editorial_excerpt($p->description, $p->body, $p->url, $featured ? 220 : 150); ?></p>

    <a class="read-more" href="<?php echo $p->url; ?>">
      Read more<span class="visually-hidden"> about <?php echo strip_tags($p->title); ?></span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
  </div>
</article>
