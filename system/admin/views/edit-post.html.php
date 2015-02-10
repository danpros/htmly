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
	Tag <span class="required">*</span> <br><input type="text" name="tag" class="text <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error';}} ?>" value="<?php echo $oldtag?>"/><br><br>
	Url (optional)<br><input type="text" name="url" class="text" value="<?php echo $oldmd ?>"/><br>
	<span class="help">If the url leave empty we will use the post title.</span><br><br>
	Date Time<br><input type="date" name="date" class="text" value="<?php echo $timestamp; ?>"><br><input type="time" name="time" class="text" value="<?php echo $time->format('H:i'); ?>"><br><br>
	Meta Description (optional)<br><textarea name="description" maxlength="200"><?php if (isset($p->description)) { echo $p->description;} ?></textarea>
	<br><br>
	Featured Image (optional)<br><input type="text" class="text" name="fi" value="<?php echo $oldfi?>"/><br><br>
	Embed Youtube Video (optional)<br><input type="text" class="text" name="vid" value="<?php echo $oldvid?>"/><br><br>	
	<div id="wmd-button-bar" class="wmd-button-bar"></div>
	<textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error';}} ?>" name="content" cols="20" rows="10"><?php echo $oldcontent ?></textarea><br>
	<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
	<input type="hidden" name="csrf_token" value="<?php echo get_csrf()?>">
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
