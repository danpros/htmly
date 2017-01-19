<h2 class="post-index"><?php echo $heading ?></h2>
<?php if (!empty($posts)) { ?>
    <table class="post-list">
        <tr class="head">
            <th>Title</th>
            <th>Published</th>
			<?php if (config("views.counter") == "true"): ?>
                <th>Views</th>
            <?php endif; ?>
            <th>Tag</th>
            <th>Operations</th>
        </tr>
        <?php $i = 0;
        $len = count($posts); ?>
        <?php foreach ($posts as $p): ?>
            <?php
            if ($i == 0) {
                $class = 'item first';
            } elseif ($i == $len - 1) {
                $class = 'item last';
            } else {
                $class = 'item';
            }
            $i++;
            ?>
            <tr class="<?php echo $class ?>">
                <td><a target="_blank" href="<?php echo $p->url ?>"><?php echo $p->title ?></a></td>
                <td><?php echo date('d F Y', $p->date) ?></td>
                <?php if (config("views.counter") == "true"): ?>
                    <td><?php echo $p->views ?></td>
                <?php endif; ?>
                <td><?php echo $p->tag ?></td>
                <td><a href="<?php echo $p->url ?>/edit?destination=admin/mine">Edit</a> <a href="<?php echo $p->url ?>/delete?destination=admin/mine">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
        <div class="pager">
            <?php if (!empty($pagination['prev'])): ?>
                <span><a href="?page=<?php echo $page - 1 ?>" class="pagination-arrow newer" rel="prev">Newer</a></span>
            <?php endif; ?>
            <?php if (!empty($pagination['next'])): ?>
                <span><a href="?page=<?php echo $page + 1 ?>" class="pagination-arrow older" rel="next">Older</a></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php } else {
    echo 'No posts found!';
} ?>