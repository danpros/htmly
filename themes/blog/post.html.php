<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<section class="inpost post section" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
    <div class="section-inner">
        <div class="content">    
            <?php if (login()) { echo tab($p); } ?>   
            <div class="item">
                <?php if (!empty($p->image)) { ?>
                    <div class="featured featured-image">
                        <a href="<?php echo $p->url ?>"><img itemprop="image" src="<?php echo $p->image; ?>" alt="<?php echo $p->title ?>"/></a>
                    </div>
                <?php } ?>
                <?php if (!empty($p->video)) { ?>
                    <div class="featured featured-video embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo get_video_id($p->video); ?>" frameborder="0" allowfullscreen></iframe>
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
                        <h1 class="title" itemprop="headline"><a target="_blank" href="<?php echo $p->link ?>"><?php echo $p->title;?> <i class="fa fa-external-link"></i></a></h1>
					<?php } else { ?>
						<h1 class="title" itemprop="headline"><?php echo $p->title;?></h1>
					<?php } ?>
                    <p class="meta">
                        <span class="date" itemprop="datePublished"><?php echo format_date($p->date) ?></span> - <?php echo i18n("Posted_in");?> 
                        <span itemprop="articleSection"><?php echo $p->category;?></span> <?php echo i18n("by");?> 
                        <span itemprop="author"><a href="<?php echo $p->authorUrl;?>"><?php echo $p->authorName;?></a></span>
                    </p>
                </div>
                <div class="desc text-left post-<?php echo $p->date;?>" itemprop="articleBody">                                   
                    <?php echo $p->body; ?>
                </div><!--//desc-->
                <div style="margin-top:30px;position:relative;">
                    <span class="tags"><i class="fa fa-tags"></i> <?php echo $p->tag;?></span> 
                    <?php if (disqus_count()) { ?>
                        <span><i class="fa fa-comments"></i> <a href="<?php echo $p->url ?>#disqus_thread"> <?php echo i18n("Comments");?></a></span>
                    <?php } elseif (facebook()) { ?>
                        <i class="fa fa-comments"></i> <a href="<?php echo $p->url ?>#comments"><span><fb:comments-count href=<?php echo $p->url ?>></fb:comments-count> <?php echo i18n("Comments");?></span></a>
                    <?php } ?>
                    <span class="share pull-right">
                        <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo $p->url ?>&t=<?php echo $p->title ?>"><i class="fa fa-facebook"></i></a> 
                        <a target="_blank" href="https://twitter.com/share?url=<?php echo $p->url ?>&text=<?php echo $p->title ?>"><i class="fa fa-twitter"></i></a>
                    </span>
                <div style="clear:both;"></div>
                </div>
                <div style="margin-top:30px;position:relative;">
                    <hr>
                    <?php if (!empty($next)): ?>
                        <span class="newer"><a href="<?php echo($next['url']); ?>" rel="next"><i class="fa fa-long-arrow-left"></i> <?php echo i18n("Next");?></a></span>
                    <?php endif; ?>
                    <?php if (!empty($prev)): ?>
                        <span class="older pull-right"><a href="<?php echo($prev['url']); ?>" rel="prev"><?php echo i18n("Prev");?> <i class="fa fa-long-arrow-right"></i></a></span>
                    <?php endif; ?>
                    <div style="clear:both;"></div>
                </div>
                <?php if (disqus()): ?>
                    <?php echo disqus($p->title, $p->url) ?>
                <?php endif; ?>
                <?php if (disqus_count()): ?>
                    <?php echo disqus_count() ?>
                <?php endif; ?>
                <?php $related = get_related($p->related, true, config('related.count'));?>
                <?php if (!empty($related)): ?>
                    <div class="related related-posts" style="margin-top:30px;position:relative;">
                        <hr>
                        <h2 class="heading"><?php echo i18n("Related_posts");?></h2>
                        <?php foreach ($related as $rl):?>
                            <div class="item col-md-4">
                                <?php $relatedTitle = (strlen(strip_tags($rl->title)) > 30) ? shorten($rl->title, 30) . '...' : $rl->title; ?>
                                <h3 class="title"><a href="<?php echo $rl->url;?>"><?php echo $relatedTitle;?></a></h3>
                                <div class="content">
                                    <p><?php echo shorten($rl->body, 60); ?>... <a class="more-link" href="<?php echo $rl->url;?>"><?php echo i18n("read_more");?></a></p>
                                </div><!--//content-->
                            </div>
                        <?php endforeach;?>
                        <div style="clear:both;"></div>
                    </div>
                <?php endif; ?>
            </div><!--//item-->                       
        </div><!--//content-->  
    </div><!--//section-inner-->                 
</section><!--//section-->
<?php if (facebook() || disqus()): ?>
    <section class="comment-wrapper post section">
        <div class="section-inner">
            <div class="content">
               <div id="comments">
                    <h2 class="heading">Comments</h2>
                    <?php if (facebook()): ?>
                        <div class="fb-comments" data-href="<?php echo $p->url ?>" data-numposts="<?php echo config('fb.num') ?>" data-colorscheme="<?php echo config('fb.color') ?>"></div>
                    <?php endif; ?>
                    <?php if (disqus()): ?>
                        <div id="disqus_thread"></div>
                    <?php endif; ?>
                </div>
            </div><!--//content-->  
        </div><!--//section-inner-->                 
    </section><!--//section-->
<?php endif; ?>