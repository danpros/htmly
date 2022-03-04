<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<section class="inprofile post section">
    <div class="section-inner">
        <div class="content"> 
            <div class="item" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="Person">
                <h2 class="title" itemprop="name"><?php echo $name ?></h2>
                <div class="desc text-left" itemprop="description">                                    
                    <?php echo $about; ?>
                </div><!--//desc-->
                <h3>Posts by this author</h3>
                <?php if (!empty($posts)) { ?>
                    <ul class="post-list">
                        <?php foreach ($posts as $p): ?>
                            <li class="item">
                                <span><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></span> on
                                <span><?php echo format_date($p->date) ?></span> - Posted in <span><?php echo $p->category ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php } else {
                    echo i18n('No_posts_found') . '!';
                } ?>
            </div><!--//item-->
        </div><!--//content--> 
    </div><!--//section-inner-->
</section><!--//section-->
<?php if (!empty($posts)) { ?>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
	<div class="pagination"><?php echo $pagination['html'];?></div>
<?php endif; ?>
<?php } ?>