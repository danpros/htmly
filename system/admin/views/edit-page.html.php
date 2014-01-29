<?php

	$url = $p->file;
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
	
?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css" />
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<div class="wmd-panel">
<form method="POST">
	Title: <br><input type="text" name="title" class="text" value="<?php echo $oldtitle?>"/><br><br>
	Url: <br><input type="text" name="url" class="text" value="<?php echo $oldmd ?>"/><br><br>
	<div id="wmd-button-bar" class="wmd-button-bar"></div>
	<textarea id="wmd-input" class="wmd-input" name="content" cols="20" rows="10"><?php echo $oldcontent ?></textarea><br>
	<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
	<input type="submit" name="submit" class="submit" value="Submit"/>
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