<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css" />
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<div class="wmd-panel">
	<form method="POST">
		Title: <br><input type="text" class="text" name="title"/><br><br>
		Tag: <br><input type="text" class="text" name="tag"/><br><br>
		Url: <br><input type="text" class="text" name="url"/><br><br>
		<div id="wmd-button-bar" class="wmd-button-bar"></div>
		<textarea id="wmd-input" class="wmd-input" name="content" cols="20" rows="10"></textarea><br/>
		<input type="submit" name="submit" class="submit" value="Publish"/>
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