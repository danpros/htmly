<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<div class="profile-wrapper" itemprop="accountablePerson" itemscope="itemscope">
    <div class="profile" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="Person">
        <h1 class="title-post" itemprop="name"><?php echo $author->name ?></h1>
        <div class="bio" itemprop="description"><?php echo $author->about ?></div>
    </div>
</div>
<h2 class="post-index"><?php echo i18n('Post_by_author');?></h2>
<?php if (!empty($posts)) { ?>
    <ul class="post-list">
        <?php foreach ($posts as $p): ?>
            <li class="item">
                <span><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></span> - 
                <span><?php echo format_date($p->date) ?></span> - <?php echo i18n('Posted_in');?> <span><?php echo $p->category; ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
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
<?php } else {
    echo i18n('No_posts_found') . '!';
} ?>