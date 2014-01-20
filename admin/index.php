<?php
	date_default_timezone_set('Asia/Jakarta');
	require '../system/includes/dispatch.php';
	config('source', '../admin/config.ini');
	include 'includes/session.php';
?>
<!DOCTYPE html>
<html>
<head>
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
				<a href="includes/create_post.php">Create post</a> | 
				<a href="includes/logout.php">Logout</a> | 
				<span class="welcome">Welcome <?php echo $_SESSION['user'];?>!</span>
			</div>
			<?php include 'includes/post_list.php';?>
		
		<?php } else {?>
		
			<p>Login Form</p>
			<form method="POST" action="includes/login.php">
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