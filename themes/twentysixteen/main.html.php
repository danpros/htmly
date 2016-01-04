<?php if (isset($is_category)):?>
    <header class="page-header"><h1 class="page-title">Category: <?php echo $category->title;?></h1><div class="taxonomy-description"><?php echo $category->body;?></div></header>
<?php endif;?>
<?php if (isset($is_tag)):?>
    <header class="page-header"><h1 class="page-title">Tag: <?php echo $tag->title;?></h1></header>
<?php endif;?>
<?php if (isset($is_archive)):?>
    <header class="page-header"><h1 class="page-title">Archive: <?php echo $archive->title;?></h1></header>
<?php endif;?>
<?php if (isset($is_search)):?>
    <header class="page-header"><h1 class="page-title">Search: <?php echo $search->title;?></h1></header>
<?php endif;?>
<?php if (isset($is_type)):?>
    <header class="page-header"><h1 class="page-title">Type: <?php echo ucfirst($type->title);?></h1></header>
<?php endif;?>
<?php foreach ($posts as $p):?>
<article class="post <?php if ($p->type == 'post') {echo 'format-standard';} else { echo 'format-' . $p->type;} ?> hentry single">

    <header class="entry-header">
        <?php if (!empty($p->link)) {?>
            <div class="post-link"><h2 class="entry-title"><a target="_blank" href="<?php echo $p->link;?>"><?php echo $p->title;?></a></h2></div>
        <?php } else { ?>
            <h2 class="entry-title"><a href="<?php echo $p->url;?>"><?php echo $p->title;?></a></h2>
        <?php } ?>
    </header><!-- .entry-header -->

    <?php if (!empty($p->image)):?>
    <a class="post-thumbnail" href="<?php echo $p->url;?>"><img alt="<?php echo $p->title;?>" src="<?php echo $p->image;?>" width="100%"/></a>
    <?php endif;?>
        
    <div class="entry-content">
        <div class="content">
            <div class="clearfix text-formatted field field--name-body">
                <div class="content">
                    <?php if (!empty($p->quote)):?>
                        <blockquote><?php echo $p->quote;?></blockquote>
                    <?php endif;?>
                    <?php if (!empty($p->video)):?>
                        <span class="embed-youtube"><iframe width="100%" height="315px" class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $p->video; ?>" frameborder="0" allowfullscreen></iframe></span>
                    <?php endif; ?>
                    <?php if (!empty($p->audio)):?>
                        <span class="embed-soundcloud"><iframe width="100%" height="200px" class="embed-responsive-item" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $p->audio;?>&amp;auto_play=false&amp;visual=true"></iframe></span>
                    <?php endif; ?>
                    <?php echo get_teaser($p->body, $p->url);?>
                    <?php if (config('teaser.type') === 'trimmed'):?><a class="more-link" href="<?php echo $p->url; ?>">Continue reading</a><?php endif;?>
                </div>
            </div>
        </div>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <span class="byline"><span class="author vcard"><a href="<?php echo $p->authorUrl;?>"><img alt="<?php echo $p->author;?>" src="<?php echo site_url();?>themes/twentysixteen/img/avatar.png" class="avatar avatar-49 grav-hashed grav-hijack" height="49" width="49"></a><span class="screen-reader-text">Author </span> <a class="url fn n" href="<?php echo $p->authorUrl;?>"><?php echo $p->author;?></a></span></span>
        <span class="posted-on"><span class="screen-reader-text">Posted on </span><a href="<?php echo $p->url;?>" rel="bookmark"><time class="entry-date published"><?php echo date('F d, Y', $p->date) ?></time></a></span>
        <span class="cat-links"><span class="screen-reader-text">Category </span><?php echo $p->category;?></span>
        <span class="tags-links"><span class="screen-reader-text">Tags </span><?php echo $p->tag;?></span>
        <?php if (disqus_count()) { ?>
            <span class="comments-link"><a href="<?php echo $p->url ?>#disqus_thread"> comments</a></span>
        <?php } elseif (facebook()) { ?>
            <span class="comments-link"><a href="<?php echo $p->url ?>#comments"><span><fb:comments-count href=<?php echo $p->url ?>></fb:comments-count> comments</span></a></span>
        <?php } ?>        
    </footer><!-- .entry-footer -->
</article><!-- #post-## -->
<?php endforeach;?>

<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
<div class="navigation pagination">
    <div class="nav-links">
        <?php if (!empty($pagination['prev'])): ?>
            <a class="prev page-numbers" href="?page=<?php echo $page - 1 ?>">«</a>
        <?php endif; ?>
        <span class="page-numbers"><?php echo $pagination['pagenum'];?></span>
        <?php if (!empty($pagination['next'])): ?>
            <a class="next page-numbers" href="?page=<?php echo $page + 1 ?>">»</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if (disqus_count()): ?>
    <?php echo disqus_count() ?>
<?php endif; ?>