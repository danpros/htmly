<?php
	if(isset($p->file)) {
		$url = $p->file;
	}
	else {
		$url = $oldfile;
	}

	$content = file_get_contents($url);
	$oldtitle = get_content_tag('t',$content,'Untitled');
	$oldfi = get_content_tag('fi',$content);
	$oldvid = get_content_tag('vid',$content);
	$oldcontent = remove_html_comments($content);
	
	$dir = substr($url, 0, strrpos($url, '/'));
	
	$oldurl = explode('_', $url);
	
	$oldtag = $oldurl[1];
	
	$oldmd = str_replace('.md','',$oldurl[2]);
	
	if(isset($_GET['destination'])) {
		$destination = $_GET['destination'];
	}
	else {
		$destination = 'admin';
	}
	$replaced = substr($oldurl[0], 0,strrpos($oldurl[0], '/')) . '/';
	$dt = str_replace($replaced,'',$oldurl[0]);
	$t = str_replace('-','',$dt);
	$time = new DateTime($t);
	$timestamp= $time->format("Y-m-d");
	// The post date
	$postdate = strtotime($timestamp);
	// The post URL
	$delete= site_url().date('Y/m', $postdate).'/'.$oldmd . '/delete?destination=' . $destination;
	
	$nojQueryCSS = "";
	if(config('jquery')== 'enable'){
		$nojQueryCSS="hidden";
	}
?>

<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css" />
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/plugins/dropzone/dropzone.min.js"></script>
<?php if (isset($error)) { ?>
	<div class="error-message"><?php echo $error ?></div>
 <?php } ?>
 
<section class="row">
	<form method="POST" class="col post_editor">
		<input type="text" class="text row <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';}} ?>" required id="title" name="title" placeholder="Title*" value="<?php echo $oldtitle ?>"/> 
		<input type="text" class="text row <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error';}} ?>" required id='tag' name="tag" placeholder="Tag*" value="<?php echo $oldtag ?>"/>
	
		<fieldset>
		<legend class='toggle_field_label'> Advance <i class="fa fa-chevron-down"></i></legend>
		<div  class="row toggle_field">
			<div class="col">
				<input type="text" class="text row <?=$nojQueryCSS?>" id="fi" name="fi" placeholder="Featured Image (optional)" value="<?php echo $oldfi?>" data-site-url="<?php echo site_url() ?>" data-image-url=""/>
				<input type="text" class="text row <?=$nojQueryCSS?>" id="vid" name="vid" placeholder="Embed Youtube Video (optional)" value="<?php echo $oldvid ?>"/>
				<div class="row dropzone hidden" id="featuredDropzone"></div>
			</div>
			<div class="col">
				<input type="text" class="text row" name="url" placeholder="Url (optional)" value="<?php echo $oldmd ?>"/>
				<p class="help ">If the url leave empty we will use the post title.</p>
				<textarea name="description" class="row" placeholder="Meta Description (optional)" maxlength="200" rows="6"><?php if (isset($p->description)) { echo $p->description;} ?></textarea>
			</div>
		</div>
		</fieldset>
		
		<div id="mdEditor" class="row">
			<div class="wmd-panel col">
				<div id="wmd-button-bar" class="wmd-button-bar row"></div>
				<textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error';}} ?>" name="content"><?php echo $oldcontent ?></textarea><br/>
			</div>
			<div id="wmd-preview" class="col wmd-panel wmd-preview"></div>
		</div>
		<div class="row pbutton">
			<input type="submit" name="submit" class="submit" value="Save"/>
			<a href="<?php echo $delete?>">Delete</a>
		<div class="row">
		
		<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
		<input type="hidden" name="csrf_token" value="<?php echo get_csrf()?>">
	</form>

</section>
 

<script type="text/javascript">
(function () {
	var converter = new Markdown.Converter();

	var editor = new Markdown.Editor(converter);
	
	editor.run();
})();
</script>

<?php if(config('jquery')== 'enable'){?>
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
		dictRemoveFile: "Cancel",
		dictCancelUpload: "Hapus/Ganti Gambar",
		dictDefaultMessage: '<i class="fa fa-picture-o"></i><p>Drop image to upload</p>',
		
		init: function() {
			this.on("success", function(file,ret) {
				$('#fi').val($('#fi').data('site-url')+ret); 
			});
			this.on("error", function (file,errmsg){alert(errmsg)});
			var oldfi = $('#fi').val();
			// Create the mock file:
			var mockFile = { name: "Filename", size: 12345 };
			// Call the default addedfile event handler
			this.emit("addedfile", mockFile);
			// And optionally show the thumbnail of the file:
			this.emit("thumbnail", mockFile, oldfi);
			// Make sure that there is no progress bar, etc...
			this.emit("complete", mockFile);

		 }
	}
})	
})(jQuery);
</script>
<?php } ?>