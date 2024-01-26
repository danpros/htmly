<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2 class="post-index"><?php echo $category->title ?></h2>
<div><?php echo $category->body;?></div>
<br>
<?php if ($category->url !== site_url() . 'category/uncategorized'):?><a class="btn btn-primary right" href="<?php echo $category->url;?>/edit?destination=admin/categories"><?php echo i18n("Edit_category");?></a><?php endif;?>
<br><br>
<?php if (!empty($posts)) { ?>
    <table class="table post-list">
        <thead>
        <tr class="head">
            <th><?php echo i18n('Title');?></th>
            <th><?php echo i18n('Published');?></th>
            <th><?php echo i18n('Operations');?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($posts as $p): ?>
            <tr>
                <td><a target="_blank" href="<?php echo $p->url ?>"><?php echo $p->title ?></a></td>
                <td><?php echo format_date($p->date) ?></td>
                <?php if ($category->url !== site_url() . 'category/uncategorized') {?>
                <td><a class="btn btn-primary btn-xs" href="<?php echo $p->url ?>/edit?destination=admin/categories/<?php echo $category->slug;?>"><?php echo i18n('Edit');?></a> <a
                        class="btn btn-danger btn-xs" href="<?php echo $p->url ?>/delete?destination=admin/categories/<?php echo $category->slug;?>"><?php echo i18n('Delete');?></a></td>
                <?php } else {?>
                <td><a class="btn btn-primary btn-xs" href="<?php echo $p->url ?>/edit?destination=admin/categories/uncategorized"><?php echo i18n('Edit');?></a> <a
                        class="btn btn-danger btn-xs" href="<?php echo $p->url ?>/delete?destination=admin/categories/uncategorized"><?php echo i18n('Delete');?></a></td>                
                <?php } ?>
                        
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
    echo i18n('No_posts_found') . '!';
} ?>