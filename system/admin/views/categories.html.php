<?php
    $desc = get_category_info(null); 
?>

<a href="<?php echo site_url();?>add/category">Add category</a>
<table class="category-list">
	<tr class="head">
		<th>Name</th>
		<th>Description</th>
		<th>Contents</th>
		<th>Operations</th>
	</tr>
	<?php foreach ($desc as $d):?>
	<tr>
		<td><a href="<?php echo $d->url;?>" target="_blank"><?php echo $d->title;?></a></td>
		<td><?php echo $d->body;?></td>
		<td><?php $total = get_draftcount($d->md) + get_categorycount($d->md); echo $total?></td>
		<td><a href="<?php echo $d->url;?>/edit?destination=admin/categories">Edit</a> <?php if (get_categorycount($d->md) == 0 && get_draftcount($d->md) == 0 ){echo '<a href="' . $d->url . '/delete?destination=admin/categories">Delete</a>';}?></td>
	</tr>
	<?php endforeach;?>
</table>