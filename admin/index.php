<?php
	date_default_timezone_set('Asia/Jakarta');
	require '../system/includes/dispatch.php';
	config('source', '../admin/config.ini');
	include 'includes/session.php';
	include 'includes/post_list.php';
	include 'includes/page_list.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no" />
	<title>Admin Panel</title>
	<link rel="stylesheet" type="text/css" href="resources/style.css" />
</head>
<body>
<div class="wrapper-outer">
	<div class="wrapper-inner">
		<?php if (login()) { ?>
		
			<div class="nav">
				<a href="<?php echo config('site.url');?>" target="_blank">Home</a> | 
				<a href="<?php echo config('site.url');?>/admin">Admin</a> | 
				<a href="action/create_post.php">Create post</a> | 
				<a href="action/create_page.php">Create page</a> | 				
				<a href="action/logout.php">Logout</a> | 
				<span class="welcome">Welcome <?php echo $_SESSION['user'];?>!</span>
			</div>
			<p>Your blog posts:</p>
			<?php  echo get_post_list(); ?>
			<p>Static page:</p>
			<?php  echo get_page_list(); ?>
			
		<?php } else {?>
		
			<p>Login Form</p>
			<form method="POST" action="action/login.php">
				User:<br>
				<input type="text" name="user"/><br><br>
				Pass:<br>
				<input type="password" name="password"/><br><br>
				<input type="submit" name="submit" value="Login"/>
			</form>
		
		<?php } ?>
	</div>
</div>
</body>
</html>