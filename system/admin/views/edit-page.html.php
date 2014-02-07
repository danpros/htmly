<?php

	if(isset($p->file)) {
		$url = $p->file;
	}
	else {
		$url = $oldfile;
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
	
	if(isset($_GET['destination'])) {
		$destination = $_GET['destination'];
	}
	else {
		$destination = 'admin';
	}
	$dir = substr($url, 0, strrpos($url, '/'));
	$oldurl = str_replace($dir . '/','',$url);
	$oldmd = str_replace('.md','',$oldurl);
	
	$delete = site_url() . $oldmd . '/delete?destination=' . $destination;
	
?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css" />
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<?php if (isset($error)) { ?>
	<div class="error-message"><?php echo $error ?></div>
 <?php } ?>
<div class="wmd-panel">
<form method="POST">
	Title <span class="required">*</span><br><input type="text" name="title" class="text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';}} ?>" value="<?php echo $oldtitle?>"/><br><br>
	Url (optional)<br><input type="text" name="url" class="text" value="<?php echo $oldmd ?>"/><br>
	<span class="help">If the url leave empty we will use the page title.</span><br><br>
	<div id="wmd-button-bar" class="wmd-button-bar"></div>
	<textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error';}} ?>" name="content" cols="20" rows="10"><?php echo $oldcontent ?></textarea><br>
	<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
	<input type="submit" name="submit" class="submit" value="Save"/> <a href="<?php echo $delete?>">Delete</a>
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