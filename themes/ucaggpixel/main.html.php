<?php if (!defined('HTMLY')) die('UCAGG'); ?>
<div class="card">
  <?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
  <?php endif; ?>

  <div class="hero">
    <?php if (is_index()): ?>
      <h1><?php echo blog_title(); ?></h1>
      <p><?php echo safe_html(strip_tags(blog_description())); ?></p>
      <p class="quote">“A delayed game is eventually good, but a rushed game is forever bad.” — Shigeru Miyamoto</p>
    <?php elseif (!empty($category)): ?>
      <h1><?php echo $category->title; ?></h1>
      <p><?php echo $category->body; ?></p>
    <?php else: ?>
      <h1><?php echo i18n('Posts'); ?></h1>
      <p><?php echo i18n('Latest_posts'); ?></p>
    <?php endif; ?>
  </div>

  <?php $teaserType = config('teaser.type'); $readMore = config('read.more'); ?>
  <?php foreach ($posts as $p): ?>
    <article class="post-item" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
      <div class="thumb">
        <?php if (!empty($p->image)) { ?>
          <img src="<?php echo $p->image; ?>" alt="<?php echo $p->title; ?>">
        <?php } else { ?>
          <img src="<?php echo theme_path(); ?>images/ucagg_blog_logo_1024.png" alt="">
        <?php } ?>
      </div>

      <div>
        <?php if (!empty($p->link)) { ?>
          <h2 class="post-title" itemprop="name"><a target="_blank" rel="noopener" href="<?php echo $p->link ?>"><?php echo $p->title ?> &rarr;</a></h2>
        <?php } else { ?>
          <h2 class="post-title" itemprop="name"><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></h2>
        <?php } ?>

        <div class="meta">
          <span itemprop="datePublished"><?php echo format_date($p->date) ?></span>
          <span><?php echo i18n('Posted_in'); ?> <?php echo $p->category ?></span>
          <span><?php echo i18n('by'); ?> <a href="<?php echo $p->authorUrl ?>"><?php echo $p->authorName; ?></a><?php if (function_exists('user') && !empty($p->author) && user('role', $p->author) === 'admin') : ?>
            <img class="admin-badge" src="<?php echo theme_path(); ?>images/admin_sage_pixel.png" alt="Admin" title="Admin" />
          <?php endif; ?></span>
        </div>

        <div class="post-excerpt clamp-3" itemprop="articleBody"><?php echo safe_html(strip_tags(get_teaser($p->body, $p->url, 220))); ?></div>

        <?php if (!empty($readMore)) { ?>
          <a class="readmore" href="<?php echo $p->url ?>"><?php echo i18n('Read_more'); ?></a>
        <?php } ?>
      </div>
    </article>
  <?php endforeach; ?>

  <?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
    <div class="pager">
      <?php if (!empty($pagination['prev'])): ?>
        <a href="?page=<?php echo $page - 1 ?>" rel="prev"><?php echo i18n('Newer'); ?></a>
      <?php else: ?><span></span><?php endif; ?>
      <span><?php echo $pagination['pagenum']; ?></span>
      <?php if (!empty($pagination['next'])): ?>
        <a href="?page=<?php echo $page + 1 ?>" rel="next"><?php echo i18n('Older'); ?></a>
      <?php else: ?><span></span><?php endif; ?>
    </div>
  <?php endif; ?>
</div>
