<div class="creatorMenu">
	<?php if($type == 'is_category') : ?>
		<h2>Add category</h2>
		
		<a href="<?php echo site_url();?>admin/categories">Back to category-list</a>		
	<?php else : ?>
		<h2>Add page</h2>

		<a href="<?php echo site_url();?>admin/content">Back to page-list</a>
	<?php endif; ?>
</div>
<?php include('partials/input-fields.html.php'); ?>