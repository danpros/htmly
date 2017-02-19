<?php

/* create a variable for quick check if type post, page or category */

$globalType = false;
if($type == 'is_page' || $type == 'staticPage'){ $globalType = 'page'; }
if($type == 'is_category'){ $globalType = 'category'; }
if($type == 'is_post' || $type == 'is_video' || $type == 'is_audio' || $type == 'is_link' || $type == 'is_quote' || $type == 'is_image'){ $globalType = 'post'; }

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

<div class="content-creator">
	<div class="wmd-panel">
		<form method="POST">
			<div class="formElement">
				<label for="name">Title <span class="required">*</span></label>
				<input type="text" class="text <?php if (isset($postTitle)) {if (empty($postTitle)) {echo 'error';}} ?>" name="title" value="<?php if (isset($postTitle)) {echo $postTitle;} elseif(isset($oldtitle)){ echo $oldtitle; }?>"/>			
			</div>
			
			<div class="formElement <?php if(config('input.showUrl')  == "false"){ echo 'hide'; } ?>">
				<label for="url">Url (optional)
				<input type="text" class="text" name="url" value="<?php if (isset($postUrl)) {echo $postUrl;} elseif(isset($oldmd)){echo $oldmd;} ?>"/>
				<span class="help">If the url leave empty we will use the page title.</span>
			</div>

			<?php if($globalType == 'post') : ?>
			
				<div class="formElement <?php if(config('input.showCat')  == "false"){ echo 'hide'; } ?>">
					<label for="category">Category <span class="required">*</span></label>
					<select name="category">
						<option value="uncategorized">Uncategorized</option>
						<?php foreach ($desc as $d):?>
							<option value="<?php echo $d->md;?>" <?php if($category === $d->md) { echo 'selected="selected"';} ?>><?php echo $d->title;?></option>					
						<?php endforeach;?>
					</select>
				</div>
			
				<div class="formElement <?php if(config('input.showTag')  == "false"){ echo 'hide'; } ?>">
					<label for="tag">Tag <span class="required">*</span></label>
					<input type="text" class="text <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error';}} ?>" name="tag" value="<?php if (isset($postTag)) { echo $postTag; } elseif(isset($oldtag)){ echo $oldtag; } else { echo 'hidden'; } ?>"/>
				</div>
				
				<?php if(config('input.showDate') == "true" && isset($postdate)) : ?>
					<div class="formElement">
						<label for="date">Year, Month, Day</label>
						<input type="date" name="date" class="text" value="<?php echo date('Y-m-d', $postdate); ?>">
					</div>
				<?php endif; ?>
				
				<?php if(config('input.showDate') == "true" && isset($time)) : ?>
					<div class="formElement">
						<label for="time">Hour, Minute, Second</label>
						<input type="time" name="time" class="text" value="<?php echo $time->format('H:i:s'); ?>">
					</div>
				<?php endif; ?>
				
			<?php endif; ?>
			
			<?php if($globalType == 'post' || $globalType == 'page') : ?>
				<?php if(config('input.showMeta') == "true") : ?>
					<div class="formElement">
						<label for="description">Meta Description (optional)</label>
						<textarea name="description" rows="3" cols="20"><?php if (isset($p->description)) { echo $p->description; } elseif(isset($olddescription)){ echo $olddescription;} ?></textarea>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if($globalType == 'post') : ?>
				
				<?php if ($type == 'is_audio'):?>
					<div id="audioEditor" class="formElement">
						<label for="audio">Featured Audio <span class="required">*</span> <span class="inforight"> (iframe-code of soundcloud)</span></label>
						<textarea id="wmd-input-feature" rows="3" cols="20" class="text <?php if (isset($postAudio)) { if (empty($postAudio)) { echo 'error';} } ?>" name="audio"><?php if (isset($postAudio)) { echo $postAudio;} elseif(isset($oldaudio)){ echo $oldaudio; } ?></textarea>
						<input type="hidden" name="is_audio" value="is_audio">
					</div>
				<?php endif;?>
				
				<?php if ($type == 'is_video'):?>
					<div id="videoEditor" class="formElement">
						<label for="video">Featured Video <span class="required">*</span> <span class="inforight"> (iframe-code of YouTube/vimeo)</span></label>
						<textarea id="wmd-input-feature" rows="3" cols="20" class="text <?php if (isset($postVideo)) { if (empty($postVideo)) { echo 'error';} } ?>" name="video"><?php if (isset($postVideo)) { echo $postVideo;} elseif(isset($oldvideo)){echo $oldvideo; } ?></textarea>
						<input type="hidden" name="is_video" value="is_video">
					</div>
				<?php endif;?>
		 
				<?php if ($type == 'is_image'):?>
					<div id="imageEditor" class="formElement">
						<label>Featured Image <span class="required">*</span></label>
						<div id="wmd-button-bar-image" class="wmd-button-bar"></div>
						<textarea id="wmd-input-image" rows="3" cols="20" class="text <?php if (isset($postImage)) { if (empty($postImage)) { echo 'error';} } ?>" name="image"><?php if (isset($postImage)) { echo $postImage;} elseif(isset($oldimage)){echo $oldimage;} ?></textarea>
						<input type="hidden" name="is_image" value="is_image">
					</div>
				<?php endif;?>
				
				<?php if ($type == 'is_quote'):?>
					<div id="quoteEditor" class="formElement">
						<label>Featured Quote <span class="required">*</span></label>
						<textarea rows="3" id="wmd-input-feature" cols="20" class="text <?php if (isset($postQuote)) { if (empty($postQuote)) { echo 'error';} } ?>" name="quote"><?php if (isset($postQuote)) { echo $postQuote;} elseif(isset($oldquote)){echo $oldquote;} ?></textarea>
						<input type="hidden" name="is_quote" value="is_quote">
					</div>
				<?php endif;?>
				
				<?php if ($type == 'is_link'):?>
					<div id="linkEditor" class="formElement">
						<label>Featured Link <span class="required">*</span></label>
						<div id="wmd-button-bar-link" class="wmd-button-bar"></div>
						<textarea rows="3" id="wmd-input-link" cols="20" class="text <?php if (isset($postLink)) { if (empty($postLink)) { echo 'error';} } ?>" name="link"><?php if (isset($postLink)) { echo $postLink;} elseif(isset($oldlink)){ echo $oldlink; } ?></textarea>
						<input type="hidden" name="is_link" value="is_link">
					</div>
				<?php endif;?>
				
				<?php if ($type == 'is_post'):?>
					<input type="hidden" name="is_post" value="is_post">
				<?php endif;?>
				
			<?php endif; ?>

			<div class="formElement">
				<label for="content">Main-Content</label>
				<div id="wmd-button-bar" class="wmd-button-bar"></div>
				<textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) {if (empty($postContent)) {echo 'error';}} ?>" name="content" cols="20" rows="10"><?php if (isset($postContent)) {echo $postContent;} elseif(isset($oldcontent)){ echo $oldcontent; } ?></textarea>
				<input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
				
				<?php if ($globalType == 'post') : ?>
					<?php if (isset($isdraft[4]) AND $isdraft[4] == 'draft') : ?>
						<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
						<input type="submit" name="publishdraft" class="submit" value="Publish draft"/>
						<input type="submit" name="updatedraft" class="draft" value="Update draft"/>
						<a href="<?php echo $delete ?>">Delete</a>
					<?php elseif(isset($isupdate)) : ?>
						<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
						<input type="submit" name="updatepost" class="submit" value="Update"/>
						<input type="submit" name="revertpost" class="revert" value="Revert to draft"/>
						<a href="<?php echo $delete ?>">Delete</a>
					<?php else: ?>
						<input type="submit" name="draft" class="draft" value="Save as draft"/>
						<input type="submit" name="publish" class="submit" value="Publish"/>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ($globalType == 'page') : ?>
					<?php if(isset($isupdate)) : ?>
						<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
						<input type="submit" name="submit" class="submit" value="Update"/>
						<a href="<?php echo $delete ?>">Delete</a>
					<?php else: ?>
						<input type="submit" name="submit" class="submit" value="Publish"/>
					<?php endif; ?>
				<?php endif;?>
				
				<?php if ($globalType == 'category') :?>
					<?php if(isset($isupdate)) : ?>
						<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
						<input type="submit" name="submit" class="submit" value="Update"/>
					<?php else: ?>
						<input type="submit" name="submit" class="submit" value="Add category"/>
					<?php endif; ?>
				<?php endif;?>

				<?php if($type == 'is_frontpage' || $type == 'is_profile') : ?>
					<input type="submit" name="submit" class="submit" value="Save"/>
				<?php endif; ?>
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

	<div class="content-preview">
		<?php if($type == 'is_image' || $type == 'is_post') : ?>
			<div id="wmd-preview-image" class="wmd-panel wmd-preview"></div>
		<?php elseif($type == 'is_video' || $type == 'is_audio' || $type == 'is_quote') : ?>
			<div id="wmd-preview-feature" class="wmd-panel wmd-preview"></div>
		<?php elseif($type == 'is_link'): ?>
			<div id="wmd-preview-link" class="wmd-panel wmd-preview"></div>		
		<?php endif; ?>
		<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
	</div>
	
	<!-- Declare the base path. Important -->
	<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
	<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/editor.js"></script>
</div>