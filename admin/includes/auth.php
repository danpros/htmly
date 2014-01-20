<div id="login">
	<?php if (login()) { ?>
	
		<div class="nav">
			<a href="<?php echo config('site.url');?>/admin">Admin</a>
			<a href="includes/create_post.php">Create post</a>
			<a href="includes/logout.php">Logout</a>
			<span class="welcome">Welcome <?php echo $_SESSION['user'];?>!</span>
		</div>
		<?php include 'includes/post_list.php';?>
	
	<?php } else {?>
	
		<?php include 'includes/login.php';?>
	
	<?php } ?>
	
</div>