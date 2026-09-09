<?php if (!defined('HTMLY')) die('HTMLy'); ?>

<?php if (!empty($breadcrumb)): ?>
<nav class="breadcrumb" aria-label="Breadcrumb"><?php echo editorial_fix_breadcrumb($breadcrumb); ?></nav>
<?php endif; ?>

<div class="content-grid no-sidebar">
  <article class="main-content static-page">

    <?php if (function_exists('authorized') && isset($static) && authorized($static)): ?>
      <a class="edit-link" href="<?php echo $static->url; ?>/edit?destination=page">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
        Edit page
      </a>
    <?php endif; ?>

    <h1 class="post-title"><?php echo $static->title; ?></h1>

    <div class="post-content">
      <?php echo $static->body; ?>
    </div>

    <?php if (isset($is_page)): ?>
      <?php $editorial_subpages = find_subpage($static->url ? basename(parse_url($static->url, PHP_URL_PATH)) : ''); ?>
      <?php if (!empty($editorial_subpages)): ?>
      <div class="subpage-grid">
        <?php foreach ($editorial_subpages as $sp): ?>
          <?php $spThumb = function_exists('get_image') ? get_image($sp->body) : null; ?>
          <a class="subpage-card" href="<?php echo $sp->url; ?>">
            <?php if ($spThumb): ?>
              <div class="thumb"><img src="<?php echo $spThumb; ?>" alt="<?php echo htmlspecialchars(strip_tags($sp->title)); ?>" loading="lazy" decoding="async"></div>
            <?php endif; ?>
            <div class="body">
              <h2><?php echo $sp->title; ?></h2>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>

  </article>
</div>
