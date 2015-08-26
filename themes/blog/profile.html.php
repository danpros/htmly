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
                        <?php $i = 0; $len = count($posts); ?>
                        <?php foreach ($posts as $p): ?>
                            <?php if ($i == 0) {
                                $class = 'item first';
                            } elseif ($i == $len - 1) {
                                $class = 'item last';
                            } else {
                                $class = 'item';
                            }
                            $i++; ?>
                            <li class="<?php echo $class; ?>">
                                <span><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></span> on
                                <span><?php echo date('d F Y', $p->date) ?></span> - Posted in <span><?php echo $p->category ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
                        <div class="pager">
                            <?php if (!empty($pagination['prev'])): ?>
                                <span class="newer pull-left"><a class="btn btn-cta-secondary" href="?page=<?php echo $page - 1 ?>" rel="prev">Newer</a></span>
                            <?php endif; ?>
                            <?php if (!empty($pagination['next'])): ?>
                                <span class="older pull-right"><a class="btn btn-cta-secondary" href="?page=<?php echo $page + 1 ?>" rel="next">Older</a></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php } else {
                    echo 'No posts found!';
                } ?>
            </div><!--//item-->
        </div><!--//content--> 
    </div><!--//section-inner-->
</section><!--//section-->