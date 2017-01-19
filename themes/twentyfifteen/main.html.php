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
<?php foreach ($posts as $p): ?>
<article class="post type-post hentry <?php if (!empty($p->image) || !empty ($p->audio) || !empty ($p->video)):?>has-post-thumbnail<?php endif;?>">
    <?php if (!empty($p->image)):?>
    <div class="post-thumbnail">
        <img style="width:100%;" title="<?php echo $p->title; ?>" alt="<?php echo $p->title; ?>" class="attachment-post-thumbnail wp-post-image" src="<?php echo $p->image; ?>">
    </div>
    <?php endif; ?>
    <?php if (!empty($p->audio)):?>
    <div class="post-thumbnail">
        <iframe width="100%" height="200px" class="embed-responsive-item" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $p->audio;?>&amp;auto_play=false&amp;visual=true"></iframe>
    </div>
    <?php endif; ?>
    <?php if (!empty($p->video)):?>
    <div class="post-thumbnail">
        <iframe width="100%" height="315px" class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $p->video; ?>" frameborder="0" allowfullscreen></iframe>
    </div>
    <?php endif; ?>
    <?php if (!empty($p->quote)):?>
    <div class="post-blockquote">
        <blockquote class="quote"><?php echo $p->quote ?></blockquote>
    </div>
    <?php endif; ?>
    <?php if (!empty($p->link)) { ?>
    <header class="entry-header">
        <div class="post-link"><h2 class="entry-title"><a target="_blank" href="<?php echo $p->url; ?>"><?php echo $p->title; ?></a></h2></div>
    </header>
    <?php } else { ?>
    <header class="entry-header">
        <h2 class="entry-title"><a href="<?php echo $p->url; ?>"><?php echo $p->title; ?></a></h2>
    </header>
	<?php } ?>
    <div class="entry-content">
        <?php echo get_teaser($p->body, $p->url); ?>
        <?php if (config('teaser.type') === 'trimmed'):?><a class="more-link" href="<?php echo $p->url; ?>">Continue reading</a><?php endif;?>
    </div>
    <footer class="entry-footer">
        <span class="posted-on">
            <a href="<?php echo $p->url;?>" rel="permalink"><time class="entry-date published updated"><?php echo date('F d, Y', $p->date) ?></time></a> 
        </span>
        <span class="byline">
            <span class="author vcard">
                <a href="<?php echo $p->authorUrl; ?>"><?php echo $p->author; ?></a>
            </span>
        </span>
        <span class="cat-links">
            <?php echo $p->category; ?>
        </span>
        <span class="tags-links">
            <?php echo $p->tag; ?>
        </span>
        <?php if (disqus_count()) { ?>
            <span class="comments-link"><a href="<?php echo $p->url ?>#disqus_thread"> comments</a></span>
        <?php } elseif (facebook()) { ?>
            <span class="comments-link"><a href="<?php echo $p->url ?>#comments"><span><fb:comments-count href=<?php echo $p->url ?>></fb:comments-count> comments</span></a></span>
        <?php } ?>
    </footer>
</article>
<?php endforeach; ?>
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