<div class="creatorMenu">
	<h2 class="post-index"><?php echo $heading ?></h2>
</div>

<div class="content-list">

	<?php if (!empty($posts)) : ?>
		<?php $actionDestination = 'admin/posts'; ?>
		<?php include('partials/post-list.html.php'); ?>
	<?php else : ?>
		<p>No posts found!</p>
	<?php endif; ?>
	
</div>