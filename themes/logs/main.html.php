<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<?php if (!empty($category)): ?>
    <div class="category">
        <h2 class="category-title"><?php echo $category->title;?></h2>
        <div class="category-content">                                   
            <?php echo $category->body; ?>
        </div>
    </div>
<?php endif; ?>
<?php $teaserType = config('teaser.type'); $readMore = config('read.more');?>
<?php foreach ($posts as $p): ?>
    <div class="post" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
        <div class="main">
            <?php if (!empty($p->link)) { ?>
                <h2 class="title-index" itemprop="name"><a target="_blank" href="<?php echo $p->link ?>"><?php echo $p->title ?> &rarr;</a></h2>
            <?php } else { ?>
                <h2 class="title-index" itemprop="name"><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></h2>
            <?php } ?>
            <div class="date">
                <span itemprop="datePublished"><?php echo format_date($p->date) ?></span> - <?php echo i18n('Posted_in');?>
                <span itemprop="articleSection"><?php echo $p->category ?></span> <?php echo i18n('by');?>
                <span itemprop="author"><a href="<?php echo $p->authorUrl ?>"><?php echo $p->authorName; ?></a></span>
                <?php if (disqus_count()) { ?> - 
                    <span><a href="<?php echo $p->url ?>#disqus_thread"><?php echo i18n('Comments');?></a></span>
                <?php } elseif (facebook()) { ?> - 
                    <a href="<?php echo $p->url ?>#comments"><span><fb:comments-count href=<?php echo $p->url ?>></fb:comments-count> <?php echo i18n('Comments');?></span></a>
                <?php } ?>
                <?php if (authorized($p)) { echo ' - <span><a href="'. $p->url .'/edit?destination=post">Edit</a></span>'; } ?>
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
            <div class="teaser-body post-<?php echo $p->date;?>" itemprop="articleBody">
                <?php echo get_teaser($p->body, $p->url) ?>
                <?php if ($teaserType === 'trimmed'):?>[...] <a href="<?php echo $p->url;?>"><?php echo $readMore; ?></a><?php endif;?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
    <div class="pager">
        <?php if (!empty($pagination['prev'])): ?>
            <span class="newer" ><a href="?page=<?php echo $page - 1 ?>" rel="prev">&laquo; <?php echo i18n('Newer');?></a></span>
        <?php endif; ?>
        <span class="page-number"><?php echo $pagination['pagenum'];?></span>
        <?php if (!empty($pagination['next'])): ?>
            <span class="older"><a href="?page=<?php echo $page + 1 ?>" rel="next"><?php echo i18n('Older');?>  &raquo;</a></span>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php if (disqus_count()): ?>
    <?php echo disqus_count() ?>
<?php endif; ?>
