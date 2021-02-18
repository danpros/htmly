<?php

if ($type == 'is_frontpage') {
	$filename = 'content/data/frontpage/frontpage.md';

	if (file_exists($filename)) {
		$content = file_get_contents($filename);
		$oldtitle = get_content_tag('t', $content, 'Welcome');
		$oldcontent = remove_html_comments($content);
	} else {
		$oldtitle = 'Welcome';
		$oldcontent = 'Welcome to our website.';
	}
} elseif ($type == 'is_profile') {

	if (isset($_SESSION[config("site.url")]['user'])) {
		$user = $_SESSION[config("site.url")]['user'];
	}

	$filename = 'content/' . $user . '/author.md';

	if (file_exists($filename)) {
		$content = file_get_contents($filename);
		$oldtitle = get_content_tag('t', $content, 'user');
		$oldcontent = remove_html_comments($content);
	} else {
		$oldtitle = $user;
		$oldcontent = 'Just another HTMLy user.';
	}

} else {

	if (isset($p->file)) {
		$url = $p->file;
	} else {
		$url = $oldfile;
	}
	$content = file_get_contents($url);
	$oldtitle = get_content_tag('t', $content, 'Untitled');
	$olddescription = get_content_tag('d', $content);
	$oldcontent = remove_html_comments($content);

	if (isset($_GET['destination'])) {
		$destination = _h($_GET['destination']);
	} else {
		$destination = 'admin';
	}
	$dir = substr($url, 0, strrpos($url, '/'));
	$oldurl = str_replace($dir . '/', '', $url);
	$oldmd = str_replace('.md', '', $oldurl);

	if (isset($p->url)) {
		$delete = $p->url . '/delete?destination=' . $destination;
	}
	else {
		if(empty($sub)) {
			$delete = site_url() . $oldmd . '/delete?destination=' . $destination;
		}
		else {
			$delete = site_url() . $static .'/'. $sub . '/delete?destination=' . $destination;
		}
	}
}

?>
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

<div class="row">
	<div class="wmd-panel" style="width:100%;">
		<form method="POST">
			<div class="row">
				<div class="col-sm-6">
					<label for="pTitle"><?php echo i18n('Title');?> <span class="required">*</span></label>
					<input type="text" id="pTitle" name="title" class="form-control text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error'; } } ?>" value="<?php echo $oldtitle ?>"/>
					<br>
					<?php if($type != 'is_frontpage' && $type != 'is_profile') { ?>
					<label for="pMeta"><?php echo i18n('Meta_description');?> (optional)</label>
					<br />
					<textarea id="pMeta" class="form-control" name="description" rows="3" cols="20" placeholder"If leave empty we will excerpt it from the content below"><?php if (isset($p->description)) { echo $p->description;} else {echo $olddescription;}?></textarea>
					<br /><br />
					<?php } ?>
				</div>
				<div class="col-sm-6">
					<?php if($type != 'is_frontpage' && $type != 'is_profile') { ?>
					<label for="pURL">Url (optional)</label>
					<br>
					<input type="text" id="pURL" name="url" class="form-control text" value="<?php echo $oldmd ?>" placeholder="If the url leave empty we will use the page title"/>
					<br>
					<?php } ?>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-6">
					<label for="wmd-input">Content</label>
					<div id="wmd-button-bar" class="wmd-button-bar"></div>
					<textarea id="wmd-input" class="form-control wmd-input <?php if (isset($postContent)) {if (empty($postContent)) {echo 'error';}} ?>" name="content" cols="20" rows="10"><?php echo $oldcontent ?></textarea>
					<br>
					<input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
					<?php if($type == 'is_frontpage' || $type == 'is_profile') { ?>
						<input type="submit" name="submit" class="btn btn-primary submit" value="Save"/>
					<?php } elseif ($type == 'is_category') {?>
						<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
						<input type="submit" name="submit" class="btn btn-primary submit" value="Save category"/>
					<?php } else {?>
						<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
						<input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Save');?>"/> <a class="btn btn-danger" href="<?php echo $delete ?>"><?php echo i18n('Delete');?></a>
					<?php } ?>
				</div>
				<div class="col-sm-6">
					<label>Preview</label>
					<br>
					<div id="wmd-preview" class="wmd-panel wmd-preview" style="width:100%;overflow:auto;"></div>
				</div>
			</div>
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
</div>
<!-- Declare the base path. Important -->
<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/editor.js"></script>