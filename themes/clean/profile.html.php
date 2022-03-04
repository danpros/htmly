<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<div class="profile-wrapper" itemprop="accountablePerson" itemscope="itemscope">
    <div class="profile" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="Person">
        <h1 class="title-post" itemprop="name"><?php echo $name ?></h1>
        <div class="bio" itemprop="description"><?php echo $about ?></div>
    </div>
</div>
<h2 class="post-index">Posts by this author</h2>
<?php if (!empty($posts)) { ?>
    <ul class="post-list">
        <?php foreach ($posts as $p): ?>
            <li class="item">
                <span><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></span> on
                <span><?php echo format_date($p->date) ?></span> - Posted in <span><?php echo $p->category; ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
        <div class="pager">
            <?php if (!empty($pagination['prev'])): ?>
                <span><a href="?page=<?php echo $page - 1 ?>" class="pagination-arrow newer" rel="prev">Newer</a></span>
            <?php endif; ?>
             <span class="page-number"><?php echo $pagination['pagenum']; ?></span>
            <?php if (!empty($pagination['next'])): ?>
                <span><a href="?page=<?php echo $page + 1 ?>" class="pagination-arrow older" rel="next">Older</a></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php } else {
    echo i18n('No_posts_found') . '!';
} ?>