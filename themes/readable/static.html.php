<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<?php if (login()) { echo tab($p); } ?>
<div class="post" itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">
    <div class="main">
        <h1 class="title-post" itemprop="name"><?php echo $p->title ?></h1>
        <div class="post-body" itemprop="articleBody">
            <?php echo $p->body; ?>
        </div>
    </div>
    <div class="postnav">
        <?php if (!empty($next)): ?>
            <span><a href="<?php echo($next['url']); ?>" class="pagination-arrow newer" rel="next"><?php echo($next['title']); ?></a></span>
        <?php endif; ?>
        <?php if (!empty($prev)): ?>
            <span><a href="<?php echo($prev['url']); ?>" class="pagination-arrow older" rel="prev"><?php echo($prev['title']); ?></a></span>
        <?php endif; ?>
    </div>
</div>