<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="page type-page hentry">

    <header class="entry-header">
        <?php if (login()) { echo tab($p); } ?>
        <h1 class="entry-title"><?php echo $p->title;?></h1>    
    </header><!-- .entry-header -->

    <div class="entry-content">
        <div class="content">
            <div class="clearfix text-formatted field field--name-body">
                <div class="content">
                    <?php echo $p->body;?>
                </div>
            </div>
        </div>
    </div><!-- .entry-content -->
    
</article><!-- #post-## -->

<nav role="navigation" class="navigation post-navigation">
    <h2 class="screen-reader-text">Post navigation</h2>
    <div class="nav-links">
        <?php if (!empty($prev)): ?>
            <div class="nav-previous"><a rel="prev" href="<?php echo($prev['url']); ?>"><span aria-hidden="true" class="meta-nav"><?php echo i18n('Next');?></span> <span class="screen-reader-text">Previous post:</span> <span class="post-title"><?php echo($prev['title']); ?></span></a></div>
        <?php endif;?>
        <?php if (!empty($next)): ?>
            <div class="nav-next"><a rel="next" href="<?php echo($next['url']); ?>"><span aria-hidden="true" class="meta-nav"><?php echo i18n('Prev');?></span> <span class="screen-reader-text">Next post:</span> <span class="post-title"><?php echo($next['title']); ?></span></a></div>
        <?php endif;?>
    </div>
</nav>