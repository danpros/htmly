<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="post <?php if ($p->type == 'post') {echo 'format-standard';} else { echo 'format-' . $p->type;} ?> hentry single">

    <header class="entry-header">
        <?php if (login()) { echo tab($p); } ?>
        <?php if (!empty($p->link)) {?>
            <div class="post-link"><h1 class="entry-title"><a target="_blank" href="<?php echo $p->link;?>"><?php echo $p->title;?></a></h1></div>
        <?php } else { ?>
            <h1 class="entry-title"><?php echo $p->title;?></h1>
        <?php } ?>
    </header><!-- .entry-header -->

    <?php if (!empty($p->image)):?>
        <a class="post-thumbnail" href="<?php echo $p->url;?>"><img alt="<?php echo $p->title;?>" src="<?php echo $p->image;?>" width="100%"/></a>
    <?php endif;?>

    <div class="entry-content">
        <div class="content">
            <div class="clearfix text-formatted field field--name-body">
                <div class="content post-<?php echo $p->date;?>">
                    <?php if (!empty($p->quote)):?>
                        <blockquote><?php echo $p->quote;?></blockquote>
                    <?php endif;?>
                    <?php if (!empty($p->video)):?>
                        <span class="embed-youtube"><iframe width="100%" height="315px" class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo get_video_id($p->video); ?>" frameborder="0" allowfullscreen></iframe></span>
                    <?php endif; ?>
                    <?php if (!empty($p->audio)):?>
                        <span class="embed-soundcloud"><iframe width="100%" height="200px" class="embed-responsive-item" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $p->audio;?>&amp;auto_play=false&amp;visual=true"></iframe></span>
                    <?php endif; ?>
                    <?php echo $p->body;?>
                    <hr>
                    <style>.related p {margin-top:0;margin-bottom:0.5em;} .related ul {margin-left:1em;}</style>
                    <div class="related">
                        <p><strong><?php echo i18n("Related_posts");?></strong></p>
                        <?php echo get_related($p->related);?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="author-info">
            <div class="author-avatar">
                <a href="<?php echo $p->authorUrl;?>"><img alt="<?php $author->name;?>" src="<?php echo $p->authorAvatar;?>" class="avatar avatar-42" height="42" width="42" /></a>
            </div><!-- .author-avatar -->

            <div class="author-description">
                <h2 class="author-title"><span class="author-heading"><?php echo i18n('Author');?>:</span> <?php echo $author->name;?></h2>
                <?php echo $author->about;?>
            </div><!-- .author-description -->
            
        </div><!-- .author-info -->
        
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <span class="byline"><span class="author vcard"><a href="<?php echo $p->authorUrl;?>"><img alt="<?php echo $p->authorName;?>" title="<?php echo $p->authorName;?>" src="<?php echo $p->authorAvatar;?>" class="avatar avatar-49 grav-hashed grav-hijack" height="49" width="49"/></a><span class="screen-reader-text">Author </span> <a class="url fn n" href="<?php echo $p->authorUrl;?>"><?php echo $p->authorName;?></a></span></span>

        <span class="posted-on"><span class="screen-reader-text">Posted on </span><a href="<?php echo $p->url;?>" rel="bookmark"><time class="entry-date published"><?php echo format_date($p->date) ?></time></a></span>

        <span class="cat-links"><span class="screen-reader-text"><?php echo i18n('Category');?> </span><?php echo $p->category;?></span>

        <span class="tags-links"><span class="screen-reader-text">Tags </span><?php echo $p->tag;?></span>

        <?php if (disqus_count()) { ?>
            <span class="comments-link"><a href="<?php echo $p->url ?>#disqus_thread"> <?php echo i18n('Comments');?></a></span>
        <?php } elseif (facebook()) { ?>
            <span class="comments-link"><a href="<?php echo $p->url ?>#comments"><span><fb:comments-count href=<?php echo $p->url ?>></fb:comments-count> <?php echo i18n('Comments');?></span></a></span>
        <?php } ?>

    </footer><!-- .entry-footer -->

</article><!-- #post-## -->

    <?php if (disqus()): ?>
        <?php echo disqus($p->title, $p->url) ?>
    <?php endif; ?>
    
    <?php if (disqus_count()): ?>
        <?php echo disqus_count() ?>
    <?php endif; ?>
    
    <?php if (facebook() || disqus()): ?>
        <div class="comments-area" id="comments">
        
            <h2 class="comments-title"><?php echo i18n('Comments');?> “<?php echo $p->title;?>”</h2>
            
            <?php if (facebook()): ?>
                <div class="fb-comments" data-href="<?php echo $p->url ?>" data-numposts="<?php echo config('fb.num') ?>" data-colorscheme="<?php echo config('fb.color') ?>"></div>
            <?php endif; ?>
            
            <?php if (disqus()): ?>
                <div id="disqus_thread"></div>
            <?php endif; ?>
            
        </div>
    <?php endif; ?>

<nav role="navigation" class="navigation post-navigation">
    <h2 class="screen-reader-text">Post navigation</h2>
    <div class="nav-links">
        <?php if (!empty($prev)): ?>
            <div class="nav-previous"><a rel="prev" href="<?php echo($prev['url']); ?>"><span aria-hidden="true" class="meta-nav"><?php echo i18n('Prev');?></span> <span class="screen-reader-text">Previous post:</span> <span class="post-title"><?php echo($prev['title']); ?></span></a></div>
        <?php endif;?>
        <?php if (!empty($next)): ?>
            <div class="nav-next"><a rel="next" href="<?php echo($next['url']); ?>"><span aria-hidden="true" class="meta-nav"><?php echo i18n('Next');?></span> <span class="screen-reader-text">Next post:</span> <span class="post-title"><?php echo($next['title']); ?></span></a></div>
        <?php endif;?>
    </div>
</nav>