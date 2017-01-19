<h2 class="post-index"><?php echo $heading ?></h2>
<?php if (!empty($posts)) { ?>
    <table class="post-list">
        <tr class="head">
            <th>Title</th>
            <th>Created</th>
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
                <td><?php echo $p->title ?></td>
                <td><?php echo date('d F Y', $p->date) ?></td>
                <td><?php echo strip_tags($p->tag) ?></td>
                <td><a href="<?php echo $p->url ?>/edit?destination=admin/draft">Edit</a> <a href="<?php echo $p->url ?>/delete?destination=admin/draft">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php } else {
    echo 'No draft found!';
} ?>