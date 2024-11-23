<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2 class="post-index"><?php echo $heading ?></h2>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>admin/content"><?php echo i18n('Add_new_post');?></a>
<br><br>
<?php if (!empty($posts)) { ?>
    <table class="table post-list">
        <thead>
        <tr class="head">
            <th><?php echo i18n('Title');?></th>
            <th><?php echo i18n('Publish');?></th>
            <th><?php echo i18n('Category');?></th>
            <th><?php echo i18n('Tags');?></th>
            <th><?php echo i18n('Operations');?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($posts as $p): ?>
            <tr>
                <td><?php echo $p->title ?></td>
                <td><?php echo format_date($p->date, 'd F Y, H:i:s') ?></td>
                <td><a href="<?php echo site_url() . 'admin/categories/' . $p->categorySlug; ?>"><?php echo $p->categoryTitle;?></a></td>
                <td><?php echo str_replace('rel="tag"', 'rel="tag" class="badge badge-light text-primary font-weight-normal"', $p->tag); ?></td>
                <td><a class="btn btn-primary btn-xs" href="<?php echo $p->url ?>/edit?destination=admin/scheduled"><?php echo i18n('Edit');?></a> <a class="btn btn-danger btn-xs" href="<?php echo $p->url ?>/delete?destination=admin/scheduled"><?php echo i18n('Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
<br>
    <div class="pager">
    <ul class="pagination">
        <?php if (!empty($pagination['prev'])) { ?>
            <li class="newer page-item"><a class="page-link" href="?page=<?php echo $page - 1 ?>" rel="prev">&#8592; <?php echo i18n('Newer');?></a></li>
        <?php } else { ?>
        <li class="page-item disabled" ><span class="page-link">&#8592; <?php echo i18n('Newer');?></span></li>
        <?php } ?>
        <li class="page-number page-item disabled"><span class="page-link"><?php echo $pagination['pagenum'];?></span></li>
        <?php if (!empty($pagination['next'])) { ?>
            <li class="older page-item" ><a class="page-link" href="?page=<?php echo $page + 1 ?>" rel="next"><?php echo i18n('Older');?> &#8594;</a></li>
        <?php } else { ?>
            <li class="page-item disabled" ><span class="page-link"><?php echo i18n('Older');?> &#8594;</span></li>
        <?php } ?>
        </ul>
    </div>
<?php endif; ?>
<?php } else {
    echo i18n('No_scheduled_posts_found');
} ?>
