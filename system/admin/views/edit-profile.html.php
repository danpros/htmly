<?php

	if(isset($_SESSION[config("site.url")]['user'])) {
		$user = $_SESSION[config("site.url")]['user'];
	}
	
	$filename = 'content/' . $user . '/author.md';
	
	if(file_exists($filename)) {
		$content = file_get_contents($filename);
		$oldtitle = get_content_tag('t',$content,'user');
		$oldcontent = remove_html_comments($content);
	}
	else {
		$oldtitle = $user;
		$oldcontent = 'Just another HTMLy user.';
	}
	
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
	Title <span class="required">*</span> <br><input type="text" name="title" class="text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';}} ?>" value="<?php echo $oldtitle?>"/><br><br>
	<br>
	<div id="wmd-button-bar" class="wmd-button-bar"></div>
	<textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error';}} ?>" name="content" cols="20" rows="10"><?php echo $oldcontent ?></textarea><br>
	<input type="hidden" name="csrf_token" value="<?php echo get_csrf()?>">
	<input type="submit" name="submit" class="submit" value="Save"/>
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