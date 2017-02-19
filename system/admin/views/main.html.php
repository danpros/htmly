<?php include('partials/post-type-navi.html.php'); ?>

<?php
	/* Get All Posts */
	$page 		= from($_GET, 'page');
	$page 		= $page ? (int)$page : 1;
	$perpage 	= config('posts.perpage');
	$posts 		= get_posts(null, $page, $perpage);
?>

<div class="content-list">

	<?php if (!empty($posts)) : ?>
		<?php $actionDestination = 'admin'; ?>
		<?php include('partials/post-list.html.php'); ?>
	<?php else : ?>
		<p>No posts found!</p>
	<?php endif; ?>
	
</div>