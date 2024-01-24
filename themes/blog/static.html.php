<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<section class="inpage post section" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
    <div class="section-inner">
        <div class="content">
            <?php if (login()) { echo tab($p); } ?>
            <div class="item">
                <h1 class="title" itemprop="headline"><?php echo $p->title;?></h1>
                <div class="desc text-left" itemprop="articleBody">
                    <?php echo $p->body; ?>
                </div><!--//desc-->
                <div style="margin-top:30px;position:relative;">
                    <hr>
                    <?php if (!empty($next)): ?>
                        <span class="newer"><a href="<?php echo($next['url']); ?>" rel="next"><i class="fa fa-long-arrow-left"></i> <?php echo($next['title']); ?></a></span>
                    <?php endif; ?>
                    <?php if (!empty($prev)): ?>
                        <span class="older pull-right"><a href="<?php echo($prev['url']); ?>" rel="prev"><?php echo($prev['title']); ?> <i class="fa fa-long-arrow-right"></i></a></span>
                    <?php endif; ?>
                    <div style="clear:both;"></div>
                </div>
            </div><!--//item-->
        </div><!--//content-->
    </div><!--//section-inner-->
</section><!--//section-->