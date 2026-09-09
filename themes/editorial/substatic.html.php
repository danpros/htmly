<?php if (!defined('HTMLY')) die('HTMLy'); ?>

<?php if (!empty($breadcrumb)): ?>
<nav class="breadcrumb" aria-label="Breadcrumb"><?php echo editorial_fix_breadcrumb($breadcrumb); ?></nav>
<?php endif; ?>

<div class="content-grid no-sidebar">
  <article class="main-content static-page">

    <?php if (!empty($static->parent)): ?>
      <a class="tax-pill" href="<?php echo site_url() . $static->parentSlug; ?>">← <?php echo $static->parent; ?></a>
    <?php endif; ?>

    <h1 class="post-title" style="margin-top:.8rem;"><?php echo $static->title; ?></h1>

    <div class="post-content">
      <?php echo $static->body; ?>
    </div>

  </article>
</div>
