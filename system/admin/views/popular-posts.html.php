<h2 class="post-index"><?php echo $heading ?></h2>
<?php if (!empty($posts)) { ?>
    <table class="post-list">
        <tr class="head">
            <th>Title</th>
            <th>Published</th><?php if (config("views.counter") == "true"): ?>
                <th>Views</th><?php endif; ?>
            <th>Author</th>
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
                    <td><?php echo $p->views ?></td><?php endif; ?>
                <td><a target="_blank" href="<?php echo $p->authorUrl ?>"><?php echo $p->author ?></a></td>
                <td><?php echo $p->tag ?></td>
                <td><a href="<?php echo $p->url ?>/edit?destination=admin/posts">Edit</a> <a
                        href="<?php echo $p->url ?>/delete?destination=admin/posts">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php } else {
    echo 'No posts found!';
} ?>