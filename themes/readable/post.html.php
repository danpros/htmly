<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<?php if (login()) { echo tab($p); } ?>
<div class="post" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
    <div class="main">
        <a name="more"></a>
        <?php if (!empty($p->link)) { ?>
            <h1 class="title-post" itemprop="name"><a target="_blank" href="<?php echo $p->link ?>"><?php echo $p->title ?> &rarr;</a></h1>
        <?php } else { ?>
            <h1 class="title-post" itemprop="name"><?php echo $p->title ?></h1>
        <?php } ?>
        <div class="date">
            <span itemprop="datePublished"><a href="<?php echo $p->archive ?>" title="Show all posts made on this month"><?php echo format_date($p->date) ?></a></span>
            - <?php echo i18n('Posted_in');?>
            <span itemprop="articleSection"><?php echo $p->category ?></span> <?php echo i18n('by');?>
            <span itemprop="author"><a href="<?php echo $p->authorUrl ?>"><?php echo $p->authorName; ?></a></span> -
            <span><a href="<?php echo $p->url ?>" rel="permalink">Permalink</a></span>
        </div>
        <?php if (!empty($p->image)) { ?>
            <div class="featured-image">
                <a href="<?php echo $p->url ?>"><img src="<?php echo $p->image; ?>" alt="<?php echo $p->title ?>"/></a>
            </div>
        <?php } ?>
        <?php if (!empty($p->video)) { ?>
            <div class="featured-video">
                <iframe src="https://www.youtube.com/embed/<?php echo get_video_id($p->video); ?>" width="560" height="315" frameborder="0" allowfullscreen></iframe>
            </div>
        <?php } ?>
        <?php if (!empty($p->audio)) { ?>
            <div class="featured-audio">
                <iframe width="560" height="315" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $p->audio;?>&amp;auto_play=false&amp;visual=true"></iframe>
            </div>
        <?php } ?>
        <?php if (!empty($p->quote)) { ?>
            <div class="featured-quote">
                <blockquote><?php echo $p->quote ?></blockquote>
            </div>
        <?php } ?>
        <div class="post-body post-<?php echo $p->date;?>" itemprop="articleBody">
            <?php echo $p->body; ?>
        </div>
        <div class="tags"><strong><?php echo i18n('Tags');?>:</strong> <?php echo $p->tag;?></div>
    </div>
    <div class="separator">&rarr;</div>
    <div class="share-box">
        <div class="author-info">
            <h4><?php echo i18n('Posts_by');?> <strong><?php echo $author->name ?></strong></h4>
            <?php echo $author->about ?>
        </div>
        <div class="share">
            <a class="twitter" target="_blank" href="https://twitter.com/share?url=<?php echo $p->url ?>&text=<?php echo $p->title ?>">Twitter</a>
            <a class="facebook" target="_blank" href="https://www.facebook.com/sharer.php?u=<?php echo $p->url ?>&t=<?php echo $p->title ?>">Facebook</a>
        </div>
    </div>
    <div class="related">
        <h4><?php echo i18n('Related_posts');?></h4>
        <?php echo get_related($p->related)?>
    </div>
    <div id="comments" class="comments border">
        <?php if (facebook()): ?>
            <div class="fb-comments" data-href="<?php echo $p->url ?>" data-numposts="<?php echo config('fb.num') ?>" data-colorscheme="<?php echo config('fb.color') ?>"></div>
        <?php endif; ?>
        <?php if (disqus()): ?>
            <div id="disqus_thread"></div>
        <?php endif; ?>
    </div>
    <div class="postnav">
        <?php if (!empty($next)): ?>
            <span><a href="<?php echo($next['url']); ?>" class="pagination-arrow newer" rel="next"><?php echo($next['title']); ?></a></span>
        <?php endif; ?>
        <?php if (!empty($prev)): ?>
            <span><a href="<?php echo($prev['url']); ?>" class="pagination-arrow older" rel="prev"><?php echo($prev['title']); ?></a></span>
        <?php endif; ?>
    </div>
    <?php if (disqus()): ?>
        <?php echo disqus($p->title, $p->url) ?>
    <?php endif; ?>
</div>