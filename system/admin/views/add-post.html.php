<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css" />
<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/jquery.datetimepicker.css" />
<script src="<?php echo site_url() ?>system/admin/editor/js/jquery-2.1.1.min.js"></script>
<script src="<?php echo site_url() ?>system/admin/editor/js/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<?php if (isset($error)) { ?>
	<div class="error-message"><?php echo $error ?></div>
 <?php } ?>
<div class="wmd-panel">
	<form method="POST">
		Title <span class="required">*</span> <br><input type="text" class="text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';}} ?>" name="title" value="<?php if (isset($postTitle)) { echo $postTitle;} ?>"/><br><br>
		Tag <span class="required">*</span> <br><input type="text" class="text <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error';}} ?>" name="tag" value="<?php if (isset($postTag)) { echo $postTag;} ?>"/><br><br>
		Url (optional)<br><input type="text" class="text" name="url" value="<?php if (isset($postUrl)) { echo $postUrl;} ?>"/><br>
		<span class="help">If the url leave empty we will use the post title.</span>
		<br><br>
		Date and time (Optional)<br><input id="datetimepicker" name="datatime" type="text" value="<?php if (isset($postDateTime)) { echo $postDateTime; } else { echo date("Y-m-d-H-m-s"); } ?>" />
		<br><br>
		<div id="wmd-button-bar" class="wmd-button-bar"></div>
		<textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error';}} ?>" name="content" cols="20" rows="10"><?php if (isset($postContent)) { echo $postContent;} ?></textarea><br/>
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
<script type="text/javascript">
jQuery('#datetimepicker').datetimepicker({
 format:'Y-m-d-H-i-s'
});
</script>