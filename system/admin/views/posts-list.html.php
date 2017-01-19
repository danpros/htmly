<h1 class="page-header"><?php echo $heading ?></h1>
<?php if (!empty($posts)) { ?>
<div class="table-responsive">
    <table id="post-list" class="table table-striped">
        <thead>
            <tr class="head">
                <th>Title</th>
                <th>Published</th><?php if (config("views.counter") == "true"): ?>
                    <th>Views</th><?php endif; ?>
                <th>Author</th>
                <th>Tag</th>
                <th>Operations</th>
            </tr>
        </thead>
        <tbody>
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
                    <td>
                        <a type="button" class="btn btn-md btn-warning" href="<?php echo $p->url ?>/edit?destination=admin/posts"><i class="glyphicon glyphicon-pencil"></i></a>
                        <a type="button" class="btn btn-md btn-danger" href="<?php echo $p->url ?>/delete?destination=admin/posts"><i class="glyphicon glyphicon-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php } else {
    echo 'No posts found!';
} ?>