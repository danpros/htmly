<?php if (!defined('HTMLY')) die('UCAGG'); ?>
<?php theme_settings(); ?>

<div class="card">
  <?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
  <?php endif; ?>

  <div class="profile-hero">
    <h1><?php echo $author->name; ?></h1>
    <?php if (!empty($author->about)): ?>
      <p><?php echo $author->about; ?></p>
    <?php endif; ?>
  </div>

  <div class="post-list-wrap">
    <?php if (!empty($posts)) { ?>
      <?php foreach ($posts as $p): ?>
        <article class="post" data-post data-title="<?php echo $p->title; ?>" data-excerpt="<?php echo strip_tags(get_teaser($p->body, $p->url, 220)); ?>">
          <div class="thumb">
        <?php $img = !empty($p->image) ? $p->image : get_image($p->body); ?>
        <?php if (!empty($img)) { ?>
          <img loading="lazy" src="<?php echo $img; ?>" alt="<?php echo $p->title; ?>">
        <?php } else { ?>
          <img loading="lazy" src="<?php echo theme_path(); ?>images/ucagg_blog_logo_1024.png" alt="">
        <?php } ?>
      </div>

          <div>
            <h3><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></h3>
            <p class="clamp-3"><?php echo safe_html(strip_tags(get_teaser($p->body, $p->url, 220))); ?></p>

            <div class="meta">
              <span><?php echo format_date($p->date) ?></span>
              <?php if (!empty($p->category)): ?>
                <a href="<?php echo site_url(); ?>category/<?php echo $p->category; ?>"><?php echo $p->category; ?></a>
              <?php endif; ?>
            </div>

            <a class="readmore" href="<?php echo $p->url ?>"><?php echo i18n('Read_more'); ?></a>
          </div>
        </article>
      <?php endforeach; ?>

      <?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
        <?php $pg = isset($page) ? (int)$page : 1; ?>
        <div class="pager">
          <?php if (!empty($pagination['prev'])): ?>
            <a href="?page=<?php echo $pg - 1 ?>" rel="prev"><?php echo i18n('Newer'); ?></a>
          <?php else: ?><span></span><?php endif; ?>

          <span><?php echo $pagination['pagenum']; ?></span>

          <?php if (!empty($pagination['next'])): ?>
            <a href="?page=<?php echo $pg + 1 ?>" rel="next"><?php echo i18n('Older'); ?></a>
          <?php else: ?><span></span><?php endif; ?>
        </div>
      <?php endif; ?>

    <?php } else { ?>
      <div class="article">
        <p><?php echo i18n('No_posts_found'); ?></p>
      </div>
    <?php } ?>
  </div>
</div>
