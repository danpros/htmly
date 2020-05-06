<h2 class="post-index"><?php echo $heading ?></h2>
<?php if (!empty($posts)) { ?>
    <table class="post-list">
        <tr class="head">
            <th><?php echo i18n('Title');?></th>
            <th><?php echo i18n('Created');?></th>
            <th><?php echo i18n('Tag');?></th>
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
                <td><?php echo $p->title ?></td>
                <td><?php echo format_date($p->date) ?></td>
                <td><?php echo strip_tags($p->tag) ?></td>
                <td><a href="<?php echo $p->url ?>/edit?destination=admin/draft"><?php echo i18n('Edit');?></a> <a href="<?php echo $p->url ?>/delete?destination=admin/draft"><?php echo i18n('Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php } else {
    echo i18n('No_draft_found') . '!';
} ?>