<?php if (!defined('HTMLY')) die('UCAGG'); ?>
<div class="card" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
  <?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
  <?php endif; ?>
  <?php if (login()) { echo tab($p); } ?>

  <div class="article">
    <div class="kicker">
      <a href="<?php echo site_url();?>"><?php echo i18n('Home'); ?></a>
      · <a href="<?php echo $p->categoryUrl; ?>"><?php echo $p->category; ?></a>
      · <?php echo format_date($p->date); ?>
      · <?php echo i18n('by'); ?> <a href="<?php echo $p->authorUrl; ?>"><?php echo $p->authorName; ?></a><?php if (function_exists('user') && !empty($p->author) && user('role', $p->author) === 'admin') : ?>
            <img class="admin-badge" src="<?php echo theme_path(); ?>images/admin_sage_pixel.png" alt="Admin" title="Admin" />
          <?php endif; ?>
    </div>

    <?php if (!empty($p->link)) { ?>
      <h1 itemprop="name"><a target="_blank" rel="noopener" href="<?php echo $p->link ?>"><?php echo $p->title ?> &rarr;</a></h1>
    <?php } else { ?>
      <h1 itemprop="name"><?php echo $p->title ?></h1>
    <?php } ?>

    <?php if (!empty($p->image)) { ?>
      <p><img src="<?php echo $p->image; ?>" alt="<?php echo $p->title ?>"></p>
    <?php } ?>

    <div class="post-body" itemprop="articleBody">
      <?php echo $p->body; ?>
    </div>

    <?php if (!empty($p->tag)) { ?>
      <div class="tags"><strong><?php echo i18n('Tags'); ?>:</strong> <?php echo $p->tag; ?></div>
    <?php } ?>
  </div>

  <?php if (disqus() || facebook()): ?>
  <div class="article" id="comments">
    <?php if (facebook()): ?>
      <div class="fb-comments" data-href="<?php echo $p->url; ?>" data-numposts="<?php echo config('fb.num'); ?>" data-colorscheme="<?php echo config('fb.color'); ?>"></div>
    <?php endif; ?>
    <?php if (disqus()): ?>
      <div id="disqus_thread"></div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($next) || !empty($prev)): ?>
  <div class="post-nav">
    <span>
      <?php if (!empty($next)): ?>
        <a class="readmore" href="<?php echo($next['url']); ?>" rel="next">← <?php echo i18n('next_post'); ?></a>
      <?php endif; ?>
    </span>
    <span class="spacer"></span>
    <span>
      <?php if (!empty($prev)): ?>
        <a class="readmore" href="<?php echo($prev['url']); ?>" rel="prev"><?php echo i18n('prev_post'); ?> →</a>
      <?php endif; ?>
    </span>
  </div>
  <?php endif; ?><?php if (disqus()): ?><?php echo disqus($p); ?><?php endif; ?>
</div>
