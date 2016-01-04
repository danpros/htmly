<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<?php if (config('category.info') === 'true'):?>
    <?php if (!empty($category)): ?>
        <div class="category">
            <h2 class="category-title"><?php echo $category->title;?></h2>
            <div class="category-content">                                   
                <?php echo $category->body; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php $i = 0; $len = count($posts); ?>
<?php foreach ($posts as $p): ?>
    <?php if ($i == 0) {
        $class = 'post first';
    } elseif ($i == $len - 1) {
        $class = 'post last';
    } else {
        $class = 'post';
    }
    $i++; ?>
    <div class="<?php echo $class ?>" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
        <div class="main">
            <?php if (!empty($p->link)) { ?>
                <h2 class="title-index" itemprop="name"><a target="_blank" href="<?php echo $p->link ?>"><?php echo $p->title ?> &rarr;</a></h2>
            <?php } else { ?>
                <h2 class="title-index" itemprop="name"><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></h2>
            <?php } ?>
            <div class="date">
                <span itemprop="datePublished"><?php echo date('d F Y', $p->date) ?></span> - Posted in
                <span itemprop="articleSection"><?php echo $p->category ?></span> by
                <span itemprop="author"><a href="<?php echo $p->authorUrl ?>"><?php echo $p->author ?></a></span>
                <?php if (disqus_count()) { ?> - 
                    <span><a href="<?php echo $p->url ?>#disqus_thread">Comments</a></span>
                <?php } elseif (facebook()) { ?> - 
                    <a href="<?php echo $p->url ?>#comments"><span><fb:comments-count href=<?php echo $p->url ?>></fb:comments-count> Comments</span></a>
                <?php } ?>
            </div>
            <?php if (!empty($p->image)) { ?>
                <div class="featured-image">
                    <a href="<?php echo $p->url ?>"><img src="<?php echo $p->image; ?>" alt="<?php echo $p->title ?>"/></a>
                </div>
            <?php } ?>
            <?php if (!empty($p->video)) { ?>
                <div class="featured-video">
                    <iframe src="https://www.youtube.com/embed/<?php echo $p->video; ?>" width="560" height="315" frameborder="0" allowfullscreen></iframe>
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
            <div class="teaser-body" itemprop="articleBody">
                <?php echo get_thumbnail($p->body) ?>
                <?php echo get_teaser($p->body, $p->url) ?>
                <?php if (config('teaser.type') === 'trimmed'):?><a href="<?php echo $p->url;?>">Read more</a><?php endif;?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
    <div class="pager">
        <?php if (!empty($pagination['prev'])): ?>
            <span class="newer" ><a href="?page=<?php echo $page - 1 ?>" rel="prev">&laquo; Newer</a></span>
        <?php endif; ?>
        <span class="page-number"><?php echo $pagination['pagenum'];?></span>
        <?php if (!empty($pagination['next'])): ?>
            <span class="older"><a href="?page=<?php echo $page + 1 ?>" rel="next">Older  &raquo;</a></span>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php if (disqus_count()): ?>
    <?php echo disqus_count() ?>
<?php endif; ?>
