<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2 class="post-index"><?php echo $heading ?></h2>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>add/author"><?php echo i18n('Add_author');?></a>
<br><br>
<?php if (!empty($authors)) { ?>
    <table id="htmly-table" class="table post-list" style="width:100%">
    <thead>
    <tr class="head">
        <th><?php echo i18n('Title');?></th>
        <th><?php echo i18n('Username');?></th>
        <th><?php echo i18n('Operations');?></th>
    </tr>
    </thead>
    <tbody>
        <?php $i = 0;
        $len = count($authors); ?>
        <?php foreach ($authors as $a): ?>
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
            <?php
            $user = $_SESSION[config("site.url")]['user'];
            ?>
            <tr class="<?php echo $class ?>">
                <td><a target="_blank" href="<?php echo $a->url ?>"><?php echo $a->title ?></a></td>
                <td><?php echo $a->username ?></td>
                <td><a class="btn btn-primary btn-sm" href="<?php echo $a->url ?>/edit?destination=admin/authors"><?php echo i18n('Edit');?></a> <?php if($user !== $a->username): ?><a
                        class="btn btn-danger btn-sm" href="<?php echo $a->url ?>/delete?destination=admin/authors"><?php echo i18n('Delete');?></a><?php endif; ?></td>
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
    echo i18n('No_authors_found') . '!';
} ?>