<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2 class="post-index"><?php echo $heading ?></h2>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>admin/content"><?php echo i18n('Add_new_post');?></a>
<br><br>
<?php if (!empty($posts)) { ?>
    <table class="table post-list">
        <tr class="head">
            <th><?php echo i18n('Title');?></th>
            <th><?php echo i18n('Published');?></th><?php if (config("views.counter") == "true"): ?>
                <th><?php echo i18n('Views');?></th><?php endif; ?>
            <th><?php echo i18n('Author');?></th>
            <th><?php echo i18n('Category');?></th>
            <th><?php echo i18n('Tags');?></th>
            <th><?php echo i18n('Operations');?></th>
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
                <td><?php echo format_date($p->date) ?></td>
                <?php if (config("views.counter") == "true"): ?>
                    <td><?php echo $p->views ?></td><?php endif; ?>
                <td><a target="_blank" href="<?php echo $p->authorUrl ?>"><?php echo $p->author ?></a></td>
                <td><a href="<?php echo str_replace('category', 'admin/categories', $p->categoryUrl); ?>"><?php echo strip_tags($p->category);?></a></td>
                <td><?php echo $p->tag ?></td>
                <td><a class="btn btn-primary btn-xs" href="<?php echo $p->url ?>/edit?destination=admin/posts"><?php echo i18n('Edit');?></a> <a
                        class="btn btn-danger btn-xs" href="<?php echo $p->url ?>/delete?destination=admin/posts"><?php echo i18n('Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php } else {
    echo i18n('No_posts_found') . '!';
} ?>