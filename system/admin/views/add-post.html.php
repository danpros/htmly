<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css" />
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/plugins/dropzone/dropzone.min.js"></script>
<?php if (isset($error)) { ?>
	<div class="error-message"><?php echo $error ?></div>
 <?php } ?>
 
<?php
$nojQueryCSS = "";
if(config('jquery')== 'enable'){
	$nojQueryCSS="hidden";
}
?> 
 
<section class="row add_post">
	<form method="POST" class="col post_editor">
		<input type="text" class="text row <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';}} ?>" required id="title" name="title" placeholder="Title*" value="<?=(isset($postTitle)) ? $postTitle : "" ?>"/>
		<input type="text" class="text row <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error';}} ?>" required id="tag" name="tag" placeholder="Tag*" value="<?=(isset($postTag)) ? $postTag : "" ?>"/>
		
		<fieldset>
		<legend class='toggle_field_label'> Advance <i class="fa fa-chevron-down"></i></legend>
		<div  class="row toggle_field">
			<div class="col">
				<input type="text" class="text row <?=$nojQueryCSS?>" id="fi" name="fi" placeholder="Featured Image (optional)" value="" data-site-url="<?php echo site_url() ?>" data-image-url=""/>
				<input type="text" class="text row <?=$nojQueryCSS?>" id="vid" name="vid" placeholder="Embed Youtube Video (optional)" value="<?php if (isset($postVid)) { echo $postVid;} ?>"/>
				<div class="row dropzone hidden" id="featuredDropzone"></div>
			</div>
			<div class="col">
				<input type="text" class="text row" name="url" placeholder="Url (optional)" value="<?php if (isset($postUrl)) { echo $postUrl;} ?>"/>
				<p class="help ">If the url leave empty we will use the post title.</p>
				<textarea name="description" class="row" placeholder="Meta Description (optional)" maxlength="200" rows="6"><?php if (isset($p->description)) { echo $p->description;} ?></textarea>
			</div>
		</div>
		</fieldset>
		
		<div id="mdEditor" class="row">
			<div class="wmd-panel col">
				<div id="wmd-button-bar" class="wmd-button-bar row"></div>
				<textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error';}} ?>" name="content"><?php if (isset($postContent)) { echo $postContent;} ?></textarea><br/>
			</div>
			<div id="wmd-preview" class="col wmd-panel wmd-preview"></div>
		</div>

		<input type="hidden" name="csrf_token" value="<?php echo get_csrf()?>">
		<div class="row pbutton">
			<input type="submit" name="submit" class="submit" value="Publish"/>
		<div class="row">
	</form>

</section>
 

<script type="text/javascript">
(function () {
	var converter = new Markdown.Converter();

	var editor = new Markdown.Editor(converter);
	
	editor.run();
})();
</script>

<?php if(config('jquery')== 'enable'){ ?>

<script type="text/javascript">
(function ($) {
$(document).ready(function() {
	$('.toggle_field').hide();
	$('.toggle_field_label').click(function(){
		$(this).siblings().toggle();
		$(this).children('i').toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
	});
	$('.dropzone').show();
	Dropzone.options.featuredDropzone = {
		url: "../upload.php",
		acceptedFiles: 'image/*',
		uploadMultiple: false,
		addRemoveLinks: true,
		thumbnailWidth: 300,
		thumbnailHeight: null,
		dictRemoveFile: "Remove",
		dictCancelUpload: "Remove/Change Picture",
		dictDefaultMessage: '<i class="fa fa-picture-o"></i><p>Drop image to upload</p>',
		
		init: function() {
			this.on("success", function(file,ret) {
				$('#fi').val($('#fi').data('site-url')+ret);			
			});
			this.on("removedfile", function(file) {
				$('#fi').val('');			
			});
			this.on("error", function (file,errmsg){alert(errmsg)});
		 }
	}
	
})	
})(jQuery);
</script>

<?php } ?> 




		