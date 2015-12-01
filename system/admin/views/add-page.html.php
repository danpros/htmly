<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css"/>
<script src="<?php echo site_url() ?>system/resources/js/jquery.min.js"></script> 
<script src="<?php echo site_url() ?>system/resources/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Extra.js"></script>
<link rel="stylesheet" href="<?php echo site_url() ?>system/resources/css/jquery-ui.css">
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/jquery.ajaxfileupload.js"></script>

<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>

<div class="wmd-panel">
	<form method="POST">
		Title <span class="required">*</span>
		<br>
		<input type="text" class="text <?php if (isset($postTitle)) {if (empty($postTitle)) {echo 'error';}} ?>" name="title" value="<?php if (isset($postTitle)) {echo $postTitle;} ?>"/>
		<br><br>
		Url (optional)<br><input type="text" class="text" name="url" value="<?php if (isset($postUrl)) {echo $postUrl;} ?>"/>
		<br>
		<span class="help">If the url leave empty we will use the page title.</span>
		<br><br>
		Meta Description (optional)
		<br>
		<textarea name="description" rows="3" cols="20"><?php if (isset($p->description)) {echo $p->description;} ?></textarea>
		<br><br>
		<div id="wmd-button-bar" class="wmd-button-bar"></div>
		<textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) {if (empty($postContent)) {echo 'error';}} ?>" name="content" cols="20" rows="10"><?php if (isset($postContent)) {echo $postContent;} ?></textarea>
		<br/>
		<input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
		<?php if ($type == 'is_page') :?>
		<input type="submit" name="submit" class="submit" value="Publish"/>
		<?php endif;?>
		<?php if ($type == 'is_category') :?>
			<input type="submit" name="submit" class="submit" value="Add category"/>
		<?php endif;?>
	</form>
</div>

<style>
#insertImageDialog { display:none; padding: 10px; font-size:12px;}
.wmd-prompt-background {z-index:10!important;}
</style>

<div id="insertImageDialog" title="Insert Image">
    <h4>URL</h4>
    <input type="text" placeholder="Enter image URL" />
    <h4>Upload</h4>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="file" name="file" id="file" />
    </form>
</div>
<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
<!-- Declare the base path. Important -->
<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/editor.js"></script>