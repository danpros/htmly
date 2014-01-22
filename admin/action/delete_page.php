<?php 
	// Change this to your timezone
	date_default_timezone_set('Asia/Jakarta');
	require '../../system/includes/dispatch.php';
	config('source', '../../admin/config.ini');
	include '../includes/session.php';
	
	if(isset($_GET['url'])) {
		$url = $_GET['url'];
	}
	else {
		header('location: ../index.php');
	}
	
	if(isset($_POST['submit'])) {
		$deleted_content = $_POST['delete'];
	}
	if(!empty($deleted_content)) {
		unlink($deleted_content);
		header('location: ../index.php');		
	}
	
	if (login()) {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no" />
	<title>Delete page</title>
	<link rel="stylesheet" type="text/css" href="../resources/style.css" />
	<link rel="stylesheet" type="text/css" href="../editor/css/editor.css" />
	<script type="text/javascript" src="../editor/js/Markdown.Converter.js"></script>
    <script type="text/javascript" src="../editor/js/Markdown.Sanitizer.js"></script>
    <script type="text/javascript" src="../editor/js/Markdown.Editor.js"></script>
</head>
<body>
<div class="wrapper-outer">
<div class="wrapper-inner">
		<div class="nav">
			<a href="<?php echo config('site.url');?>" target="_blank">Home</a> | 
			<a href="<?php echo config('site.url');?>/admin">Admin</a> | 
			<a href="../action/create_post.php">Create post</a> |
			<a href="../action/create_page.php">Create page</a> | 
			<a href="../action/edit_bio.php">Edit bio</a> | 			
			<a href="../action/logout.php">Logout</a> | 
			<span class="welcome">Welcome <?php echo $_SESSION['user'];?>!</span>
		</div>
		
		<?php echo '<p>Are you sure want to delete <strong>' . $url . '</strong>?</p>';?>
		<form method="POST">
			<input type="hidden" name="delete" value="<?php echo $url ?>"/><br>
			<input type="submit" name="submit" value="Delete"/>
		</form>
</div>
</div>
</body>
</html>
<?php } else {header('location: ../index.php');} ?>