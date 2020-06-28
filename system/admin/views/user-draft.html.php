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
    echo i18n('No_draft_found') . '!';
} ?>
