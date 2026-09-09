<?php if (!defined('HTMLY')) die('HTMLy'); ?>

<?php if (!empty($breadcrumb) && !is_index()): ?>
<nav class="breadcrumb" aria-label="Breadcrumb"><?php echo editorial_fix_breadcrumb($breadcrumb); ?></nav>
<?php endif; ?>

<?php if (!is_index() && isset($taxonomy) && (!empty($taxonomy->title))): ?>
<header class="archive-head">
  <span class="eyebrow">Browsing</span>
  <h1><?php echo $taxonomy->title; ?></h1>
  <?php if (!empty($taxonomy->body)): ?><div class="archive-desc"><?php echo $taxonomy->body; ?></div><?php endif; ?>
</header>
<?php endif; ?>

<?php if (is_index()): ?>
<h1 class="visually-hidden"><?php echo blog_title(); ?><?php if (blog_tagline()): ?> — <?php echo blog_tagline(); ?><?php endif; ?></h1>
<?php endif; ?>

<div class="content-grid">
  <div class="main-content">

    <?php if (empty($posts)): ?>
      <?php include __DIR__ . '/no-posts.html.php'; ?>
    <?php else: ?>
      <div class="post-grid">
        <?php
        $editorial_i = 0;
        $editorial_showFeature = is_index() && (empty($pagination) || empty($pagination['pagenum']) || (int) $pagination['pagenum'] === 1);
        ?>
        <?php foreach ($posts as $p): ?>
          <?php $featured = $editorial_showFeature && $editorial_i === 0; ?>
          <?php include __DIR__ . '/partials/post-card.html.php'; ?>
          <?php $editorial_i++; ?>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($pagination)): ?>
      <nav class="pagination-nav" aria-label="Pagination">
        <?php echo $pagination['html']; ?>
      </nav>
      <?php endif; ?>
    <?php endif; ?>

  </div>

  <?php include __DIR__ . '/partials/sidebar.html.php'; ?>
</div>
