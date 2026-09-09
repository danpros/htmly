<?php if (!defined('HTMLY')) die('HTMLy'); ?>

<div class="content-grid">
  <div class="main-content">

    <div class="profile-hero">
      <span class="profile-avatar" aria-hidden="true"><?php echo editorial_initials($author->name); ?></span>
      <div>
        <h1><?php echo !empty($author->title) ? $author->title : $author->name; ?></h1>
        <?php if (!empty($author->about)): ?><p><?php echo $author->about; ?></p><?php endif; ?>
      </div>
    </div>

    <?php if (empty($posts)): ?>
      <?php include __DIR__ . '/no-posts.html.php'; ?>
    <?php else: ?>
      <div class="post-grid">
        <?php foreach ($posts as $p): ?>
          <?php $featured = false; ?>
          <?php include __DIR__ . '/partials/post-card.html.php'; ?>
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
