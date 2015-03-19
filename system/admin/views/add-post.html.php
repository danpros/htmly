<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css" />
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/plugins/dropzone/dropzone.min.js"></script>
<?php if (isset($error)) { ?>
	<div class="error-message"><?php echo $error ?></div>
 <?php } ?>
 
<section class="row add_post">
	<form method="POST" class="col post_editor">
		<input type="text" class="text row" required id="title" name="title" placeholder="Title*" value="<?php if (isset($postTitle)) { echo $postTitle;} ?>"/> 
		<input type="text" class="text row" required id='tag' name="tag" placeholder="Tag*" value="<?php if (isset($postTag)) { echo $postTag;} ?>"/>
		
		<fieldset>
		<legend class='toggle_field_label'> Advance <i class="fa fa-chevron-down"></i></legend>
		<div  class="row toggle_field">
			<div class="col"><div type="text" class="dropzone" id="featuredDropzone"></div></div>
			<div class="col">
				<input type="text" class="text row" name="url" placeholder="Url (optional)" value="<?php if (isset($postUrl)) { echo $postUrl;} ?>"/>
				<p class="help ">If the url leave empty we will use the post title.</p>
				<textarea name="description" class="row" placeholder="Meta Description (optional)" maxlength="200"><?php if (isset($p->description)) { echo $p->description;} ?></textarea>
			</div>
		</div>
		</fieldset>
		
		<input type="hidden" id="fi" name="fi" value="" data-site-url="<?php echo site_url() ?>" data-image-url=""/>

		<div id="wmd-button-bar" class="wmd-button-bar row"></div>
		<div class="wmd-panel">
		<textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error';}} ?>" name="content"><?php if (isset($postContent)) { echo $postContent;} ?></textarea><br/>
		</div>
		<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
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


<script type="text/javascript">

(function ($) {
$(document).ready(function() {
	$('.toggle_field').hide();
	$('.toggle_field_label').click(function(){
		$(this).siblings().toggle();
		$(this).children('i').toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
	});

	Dropzone.options.featuredDropzone = {
		url: "/edd/upload.php",
		acceptedFiles: 'image/*',
		uploadMultiple: false,
		addRemoveLinks: true,
		thumbnailWidth: 300,
		thumbnailHeight: null,
		dictRemoveFile: "Cancel",
		dictCancelUpload: "Hapus/Ganti Gambar",
		dictDefaultMessage: '<i class="fa fa-picture-o"></i><p>Drop image to upload</p>',
		
		init: function() {
			this.on("success", function(file,ret) {
				$('#fi').val($('#fi').data('site-url')+ret); 
			});
			this.on("error", function (file,errmsg){alert(errmsg)});
		 }
	}
	
})	
})(jQuery);
</script>

<!--  
<input type="text" class="text row" id="vid" name="vid" placeholder="Embed Youtube Video (optional)" value="<?php if (isset($postVid)) { echo $postVid;} ?>"/>

-->


		