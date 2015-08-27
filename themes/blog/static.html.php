<?php if (!defined('HTMLY')) exit(); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<section class="inpage post section" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
    <div class="section-inner">
        <div class="content">
            <?php if (login()) { echo tab($p); } ?>
            <div class="item">
                <h2 class="title" itemprop="headline"><a href="<?php echo $p->url;?>"><?php echo $p->title;?></a></h2>
                <div class="desc text-left" itemprop="articleBody">
                    <?php echo $p->body; ?>
                </div><!--//desc-->
            </div><!--//item-->
        </div><!--//content-->
    </div><!--//section-inner-->
</section><!--//section-->