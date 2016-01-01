<article class="page type-page hentry">
    <header class="entry-header">
        <h1 class="entry-title"><?php echo $name ?></h1>
    </header>
    <div class="entry-content">
        <?php echo $about ?>
        <h2 class="post-index">Posts by this author</h2>
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
                        <span><?php echo date('d F Y', $p->date) ?></span> - Posted in <span><?php echo $p->tag ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php } else {
            echo 'No posts found!';
        } ?>
    </div>
</article>
<?php if (!empty($posts)) { ?>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
<div class="navigation pagination">
    <div class="nav-links">
        <?php if (!empty($pagination['prev'])): ?>
            <a class="prev page-numbers" href="?page=<?php echo $page - 1 ?>">«</a>
        <?php endif; ?>
        <span class="page-numbers"><?php echo $pagination['pagenum'];?></span>
        <?php if (!empty($pagination['next'])): ?>
            <a class="next page-numbers" href="?page=<?php echo $page + 1 ?>">»</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php } ?>