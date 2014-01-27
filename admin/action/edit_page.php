<?php 
	// Change this to your timezone
	date_default_timezone_set('Asia/Jakarta');
	require '../../system/includes/dispatch.php';
	config('source', '../../config/config.ini');
	include '../includes/session.php';
	
	if(isset($_GET['url'])) {
		$url = $_GET['url'];
	}
	else {
		header('location: ../index.php');
	}
	
	$content = file_get_contents($url);
	$arr = explode('t-->', $content);
	if(isset($arr[1])) {
		$oldtitle = ltrim(rtrim(str_replace('<!--t','',$arr[0]), ' '));
		$oldcontent = ltrim($arr[1]);
	}
	else {
		$oldtitle = 'Untitled';
		$oldcontent = ltrim($arr[0]);
	}
	
	$dir = substr($url, 0, strrpos($url, '/'));
	$oldurl = str_replace($dir . '/','',$url);
	$oldmd = str_replace('.md','',$oldurl);
	
	if(isset($_POST['submit'])) {
		$post_title = $_POST['title'];
		$post_url = preg_replace('/[^A-Za-z0-9,.-]/u', '', $_POST['url']);
		$post_url = rtrim($post_url, ',\.\-');
		$post_content = '<!--t ' . $post_title . ' t-->' . "\n\n" . $_POST['content'];
	}
	if(!empty($post_title) && !empty($post_url) && !empty($post_content)) {
		if(get_magic_quotes_gpc()) {
			$post_content = stripslashes($post_content);
		}
		$newurl = $dir . '/' . $post_url . '.md';
		if($url === $newurl) {
			file_put_contents($url, print_r($post_content, true));
		}
		else {
			rename($url, $newurl);
			file_put_contents($newurl, print_r($post_content, true));
		}
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
	<title>Edit page</title>
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
		<div class="wmd-panel">
		<form method="POST">
			Title: <br><input type="text" name="title" size="60" maxlength="60" value="<?php echo $oldtitle?>"/><br><br>
			Url: <br><input type="text" name="url" size="60" maxlength="60" value="<?php echo $oldmd ?>"/><br><br>
			<div id="wmd-button-bar" class="wmd-button-bar"></div>
			<textarea id="wmd-input" class="wmd-input" name="content" cols="20" rows="10"><?php echo $oldcontent ?></textarea><br>
			<input type="submit" name="submit" value="Submit"/>
		</form>
		</div>
		<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
		<script type="text/javascript">
		(function () {
			var converter = new Markdown.Converter();

			var editor = new Markdown.Editor(converter);
			
			editor.run();
		})();
		</script>
</div>
</div>
</body>
</html>
<?php } else {header('location: ../index.php');} ?>