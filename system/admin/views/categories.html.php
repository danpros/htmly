<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php $desc = get_category_info(null); ?>
<h2><?php echo i18n("Categories");?></h2>
<br>
<a class="btn btn-primary " href="<?php echo site_url();?>add/category"><?php echo i18n('Add_category');?></a>
<br><br>
<table class="table category-list">
    <tr class="head">
        <th><?php echo i18n('Name');?></th>
        <th><?php echo i18n('Description');?></th>
        <th><?php echo i18n('Contents');?></th>
        <th><?php echo i18n('Operations');?></th>
    </tr>
    <tr>
        <td><a href="<?php echo site_url();?>admin/categories/uncategorized"><?php echo i18n("Uncategorized");?></a></td>
        <td><p><?php echo i18n('Uncategorized_comment');?>.</p></td>
        <td><?php $total = get_draftcount('uncategorized') + get_categorycount('uncategorized') + get_scheduledcount('uncategorized'); echo $total?></td>
        <td></td>
    </tr>
    <?php foreach ($desc as $d):?>
    <tr>
        <td><a href="<?php echo site_url();?>admin/categories/<?php echo $d->md;?>"><?php echo $d->title;?></a></td>
        <td><?php echo $d->body;?></td>
        <td><?php $total = get_draftcount($d->md) + get_categorycount($d->md) + get_scheduledcount($d->md); echo $total?></td>
        <td><a class="btn btn-primary btn-xs" href="<?php echo $d->url;?>/edit?destination=admin/categories"><?php echo i18n('Edit');?></a> <?php if (get_categorycount($d->md) == 0 && get_draftcount($d->md) == 0 ){echo '<a class="btn btn-danger btn-xs" href="' . $d->url . '/delete?destination=admin/categories">' . i18n('Delete') . '</a>';}?></td>
    </tr>
    <?php endforeach;?>
</table>
