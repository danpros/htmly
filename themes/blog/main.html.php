<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<?php if (config('category.info') === 'true'):?>
    <?php if (!empty($category)): ?>
        <div class="section">
            <div class="section-inner">
                <div class="content">
                    <div class="item">
                    <h2 class="title"><?php echo $category->title;?></h2>
                    <div class="text-left">                                   
                        <?php echo $category->body; ?>
                    </div><!--//desc-->
                    </div><!--//item-->                       
                </div><!--//content-->  
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
<section class="post section <?php echo $class ?>" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
    <div class="section-inner">
        <div class="content">
            <div class="item">
                <?php if (!empty($p->image)) { ?>
                    <div class="featured featured-image">
                        <a href="<?php echo $p->url ?>"><img  itemprop="image" src="<?php echo $p->image; ?>" alt="<?php echo $p->title ?>"/></a>
                    </div>
                <?php } ?>
                <?php if (!empty($p->video)) { ?>
                    <div class="featured featured-video embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $p->video; ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php } ?>
                <?php if (!empty($p->audio)) { ?>
                    <div class="featured featured-audio embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=<?php echo $p->audio;?>&amp;auto_play=false&amp;visual=true"></iframe>
                    </div>
                <?php } ?>
                <?php if (!empty($p->quote)) { ?>
                    <div class="featured featured-quote">
                        <blockquote class="quote"><i class="fa fa-quote-left"></i> <?php echo $p->quote ?> <i class="fa fa-quote-right"></i></blockquote>
                    </div>
                <?php } ?>
                <div class="info text-left">
                    <?php if (!empty($p->link)) { ?>
                        <h2 class="title" itemprop="headline"><a target="_blank" href="<?php echo $p->link ?>"><?php echo $p->title;?> <i class="fa fa-external-link"></i></a></h2>
                    <?php } else {?>
                        <h2 class="title" itemprop="headline"><a href="<?php echo $p->url;?>"><?php echo $p->title;?></a></h2>
                    <?php } ?>
                    <p class="meta">
                        <span class="date" itemprop="datePublished"><?php echo date('d F Y', $p->date) ?></span> - Posted in 
                        <span itemprop="articleSection"><?php echo $p->category;?></span> by 
                        <span itemprop="author"><a href="<?php echo $p->authorUrl;?>"><?php echo $p->author;?></a></span>
                        <?php if (disqus_count()) { ?> 
                            with <span><i class="fa fa-comments"></i> <a href="<?php echo $p->url ?>#disqus_thread"> comments</a></span>
                        <?php } elseif (facebook()) { ?> 
                            with <i class="fa fa-comments"></i> <a href="<?php echo $p->url ?>#comments"><span><fb:comments-count href=<?php echo $p->url ?>></fb:comments-count> comments</span></a>
                        <?php } ?>
                    </p>
                </div>
                <div class="desc text-left" itemprop="articleBody">                                    
                    <?php echo get_teaser($p->body, $p->url) ?>
                </div><!--//desc-->
                <div style="position:relative;">
                   <?php if (config('teaser.type') === 'trimmed'):?>
                       <span class="more"><a class="btn btn-cta-secondary" href="<?php echo $p->url;?>">Read more</a></span>
                   <?php endif;?>
                    <span class="share pull-right">
                        <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo $p->url ?>&t=<?php echo $p->title ?>"><i class="fa fa-facebook"></i></a> 
                        <a target="_blank" href="https://twitter.com/share?url=<?php echo $p->url ?>&text=<?php echo $p->title ?>"><i class="fa fa-twitter"></i></a> 
                        <a target="_blank" class="last" href="https://plus.google.com/share?url=<?php echo $p->url ?>"><i class="fa fa-google-plus"></i></a> 
                    </span>
                <div style="clear:both;"></div>
                </div>
            </div><!--//item-->                       
        </div><!--//content-->  
    </div><!--//section-inner-->                 
</section><!--//section-->
<?php endforeach; ?>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
    <div class="pagination"><?php echo $pagination['html'];?></div>
<?php endif; ?>
<?php if (disqus_count()): ?>
    <?php echo disqus_count() ?>
<?php endif; ?>