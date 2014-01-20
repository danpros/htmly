<?php 
	// Change this to your timezone
	date_default_timezone_set('Asia/Jakarta');
	require '../../system/includes/dispatch.php';
	config('source', '../../admin/config.ini');
	include '../includes/session.php';;
	if(isset($_GET['url'])) {
		$url = $_GET['url'];
	}
	else {
		header('location: ../index.php');
	}
?> 
<!DOCTYPE html>
<html>
<head>
	<title>Edit post</title>
	<link rel="stylesheet" type="text/css" href="../resources/style.css" />
	<link rel="stylesheet" type="text/css" href="../editor/css/editor.css" />
	<script type="text/javascript" src="../editor/js/Markdown.Converter.js"></script>
    <script type="text/javascript" src="../editor/js/Markdown.Sanitizer.js"></script>
    <script type="text/javascript" src="../editor/js/Markdown.Editor.js"></script>
</head>
<body>
<div class="wrapper-outer">
<div class="wrapper-inner">
	<?php if (login()) { ?>
		<div class="nav">
			<a href="<?php echo config('site.url');?>/admin">Admin</a>
			<a href="../includes/create_post.php">Create post</a>
			<a href="../includes/logout.php">Logout</a>
			<span class="welcome">Welcome <?php echo $_SESSION['user'];?>!</span>
		</div>
	<?php } else {?>
		<?php header('location: ../index.php');?>
	<?php } ?>
	<?php
		if(isset($_POST['submit'])) {
			$post_content = $_POST['content'];
		}
		if(!empty($post_content)) {
			file_put_contents('../'. $url, print_r($post_content, true));
			header('location: ../index.php');
		}
	?>
	<div class="wmd-panel">
		<form method="POST">
			<div id="wmd-button-bar" class="wmd-button-bar"></div>
			<textarea id="wmd-input" class="wmd-input" name="content" cols="20" rows="10"><?php echo file_get_contents('../' . $url)?></textarea><br>
			<input type="submit" name="submit" value="Submit"/>
		</form>
	</div>
	<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
	<script type="text/javascript">
	(function () {
		var converter = Markdown.getSanitizingConverter();
		
		converter.hooks.chain("preBlockGamut", function (text, rbg) {
			return text.replace(/^ {0,3}""" *\n((?:.*?\n)+?) {0,3}""" *$/gm, function (whole, inner) {
				return "<blockquote>" + rbg(inner) + "</blockquote>\n";
			});
		});
		
		var editor = new Markdown.Editor(converter);
		
		editor.run();
	})();
	</script>
</div>
</div>
</body>
</html>