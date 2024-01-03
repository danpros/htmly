<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="page type-page hentry">
    <header class="entry-header">
        <h1 class="entry-title"><?php echo $author->name ?></h1><span class="social-navigation" style="float:right;"><a href="<?php echo $author->url;?>/feed"><span class="screen-reader-text">RSS</span></a></span>
    </header>
    <div class="entry-content">
        <?php echo $author->about ?>
        <h2 class="post-index"><?php echo i18n('Post_by_author');?></h2>
        <?php if ($posts) { ?>
            <ul class="post-list">
                <?php foreach ($posts as $p): ?>
                    <li class="item">
                        <span><a href="<?php echo $p->url ?>"><?php echo $p->title ?></a></span> -
                        <span><?php echo format_date($p->date) ?></span> - <?php echo i18n('Posted_in');?> <span class="tags-links"><?php echo $p->category; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php } else {
            echo i18n('No_posts_found') . '!';
        } ?>
    </div>
</article>
<?php if ($posts) { ?>
<?php if ($pagination['prev'] || $pagination['next']): ?>
<div class="navigation pagination">
    <div class="nav-links">
        <?php if ($pagination['prev']): ?>
            <a class="prev page-numbers" href="?page=<?php echo $page - 1 ?>">«</a>
        <?php endif; ?>
        <span class="page-numbers"><?php echo $pagination['pagenum'];?></span>
        <?php if ($pagination['next']): ?>
            <a class="next page-numbers" href="?page=<?php echo $page + 1 ?>">»</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php } ?>