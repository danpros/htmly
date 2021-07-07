<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
if (isset($p->file)) {
    $url = $p->file;
} else {
    $url = $oldfile;
}

$desc = get_category_info(null);

$content = file_get_contents($url);
$oldtitle = get_content_tag('t', $content, 'Untitled');
$olddescription = get_content_tag('d', $content);
$oldtag = get_content_tag('tag', $content);
$oldcontent = remove_html_comments($content);

$oldimage = get_content_tag('image', $content);
$oldaudio = get_content_tag('audio', $content);
$oldvideo = get_content_tag('video', $content);
$oldlink = get_content_tag('link', $content);
$oldquote = get_content_tag('quote', $content);

$dir = substr($url, 0, strrpos($url, '/'));
$isdraft = explode('/', $dir);
$oldurl = explode('_', $url);

if (empty($oldtag)) {
    $oldtag = $oldurl[1];
}

$oldmd = str_replace('.md', '', $oldurl[2]);

if (isset($_GET['destination'])) {
    $destination = _h($_GET['destination']);
} else {
    $destination = 'admin';
}
$replaced = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/';

// Category string
$cat = explode('/', $replaced);
$category = $cat[count($cat) - 3];

$dt = str_replace($replaced, '', $oldurl[0]);
$t = str_replace('-', '', $dt);
$time = new DateTime($t);
$timestamp = $time->format("Y-m-d H:i:s");
// The post date
$postdate = strtotime($timestamp);
// The post URL
if (config('permalink.type') == 'post') {
    $delete = site_url() . 'post/' . $oldmd . '/delete?destination=' . $destination;
} else {
    // The post URL
    $delete = site_url() . date('Y/m', $postdate) . '/' . $oldmd . '/delete?destination=' . $destination;
}

$tags = tag_cloud(true);
$tagslang = "content/data/tags.lang";
if (file_exists($tagslang)) {
    $ptags = unserialize(file_get_contents($tagslang));
    $tkey = array_keys($tags);
    $newlang = array_intersect_key($ptags, array_flip($tkey));
    $tmp = serialize($newlang);
    file_put_contents($tagslang, print_r($tmp, true));
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
<script>
$( function() {
    var availableTags = [
<?php foreach ($tags as $tag => $count):?>
    "<?php echo tag_i18n($tag) ?>",
<?php endforeach;?>
    ];
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }
 
    $( "#pTag" )
      // don't navigate away from the field on tab when selecting an item
      .on( "keydown", function( event ) {
        if ( event.keyCode === 9 && // 9 = tab
            $( this ).autocomplete( "instance" ).menu.active ) {
          event.preventDefault();
        }
      })
      .autocomplete({
        minLength: 0,
        source: function( request, response ) {
          // delegate back to autocomplete, but extract the last term
          response( $.ui.autocomplete.filter(
            availableTags, extractLast( request.term ) ) );
        },
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.value );
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          return false;
        }
      });
  } );
</script>

<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>

<div class="row">
	<div class="wmd-panel" style="width:100%;">
		<form method="POST">
			<div class="row">
				<div class="col-sm-6">
					<label for="pTitle"><?php echo i18n('Title');?> <span class="required">*</span></label>
					<input autofocus type="text" id="pTitle" name="title" class="form-control text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'is-invalid';} } ?>" value="<?php echo $oldtitle ?>"/>
					<br>
					<label for="pCategory"><?php echo i18n('Category');?> <span class="required">*</span></label>
					<select id="pCategory" class="form-control" name="category">
						<option value="uncategorized"><?php echo i18n("Uncategorized");?></option>
						<?php foreach ($desc as $d):?>
							<option value="<?php echo $d->md;?>" <?php if($category === $d->md) { echo 'selected="selected"';} ?>><?php echo $d->title;?></option>
						<?php endforeach;?>
					</select>
					<br>
					<label for="pTag">Tag <span class="required">*</span></label>
					<input type="text" id="pTag" name="tag" class="form-control text <?php if (isset($postTag)) { if (empty($postTag)) { echo 'is-invalid'; } } ?>" value="<?php echo $oldtag ?>" placeholder="<?php echo i18n('Comma_separated_values');?>"/>
					<br>

					<label for="pMeta"><?php echo i18n('Meta_description');?> (<?php echo i18n('optional');?>)</label>
					<textarea id="pMeta" class="form-control" name="description" rows="3" cols="20" placeholder="<?php echo i18n('If_leave_empty_we_will_excerpt_it_from_the_content_below');?>"><?php if (isset($p->description)) { echo $p->description; } else { echo $olddescription;} ?></textarea>
					<br>
				</div>
				
				<div class="col-sm-6">
					<div class="form-row">
						<div class="col">
							<label for="pDate"><?php echo i18n('Date');?></label>
							<input type="date" id="pDate" name="date" class="form-control text" value="<?php echo date('Y-m-d', $postdate); ?>">
						</div>
						<div class="col">
							<label for="pTime"><?php echo i18n('Time');?></label>
							<input step="1" type="time" id="pTime" name="time" class="form-control text" value="<?php echo $time->format('H:i:s'); ?>">
						</div>
					</div>				
					<br>
					<label for="pURL">Url  (<?php echo i18n('optional');?>)</label>
					<input type="text" id="pURL" name="url" class="form-control text" value="<?php echo $oldmd ?>" placeholder="<?php echo i18n('If_the_url_leave_empty_we_will_use_the_post_title');?>"//>
					<br>

					<?php if ($type == 'is_audio'):?>
					<label for="pAudio"><?php echo i18n('Featured_Audio');?>  <span class="required">*</span> (e.g Soundcloud)</label>
					<textarea rows="2" cols="20" class="form-control text <?php if (isset($postAudio)) { if (empty($postAudio)) { echo 'is-invalid';} } ?>" id="pAudio" name="audio"><?php echo $oldaudio; ?></textarea>
					<input type="hidden" name="is_audio" value="is_audio">
					<br>
					<?php endif;?>

					<?php if ($type == 'is_video'):?>
					<label for="pVideo"><?php echo i18n('Featured_Video');?> <span class="required">*</span> (e.g Youtube)</label>
					<textarea rows="2" cols="20" class="form-control text <?php if (isset($postVideo)) { if (empty($postVideo)) { echo 'is-invalid';} } ?>" id="pVideo" name="video"><?php echo $oldvideo ?></textarea>
					<input type="hidden" name="is_video" value="is_video">
					<br>
					<?php endif;?>

					<?php if ($type == 'is_image'):?>
					<label for="pImage"><?php echo i18n('Featured_Image');?> <span class="required">*</span></label>
					<textarea rows="2" cols="20" class="form-control text <?php if (isset($postImage)) { if (empty($postImage)) { echo 'is-invalid';} } ?>" id="pImage" name="image"><?php echo $oldimage; ?></textarea>
					<input type="hidden" name="is_image" value="is_image">
					<br>
					<?php endif;?>

					<?php if ($type == 'is_quote'):?>
					<label for="pQuote"><?php echo i18n('Featured_Quote');?> <span class="required">*</span></label>
					<textarea rows="3" cols="20" class="form-control text <?php if (isset($postQuote)) { if (empty($postQuote)) { echo 'is-invalid';} } ?>" id="pQuote" name="quote"><?php echo $oldquote ?></textarea>
					<input type="hidden" name="is_quote" value="is_quote">
					<br>
					<?php endif;?>

					<?php if ($type == 'is_link'):?>
					<label for="pLink"><?php echo i18n('Featured_Link');?> <span class="required">*</span></label>
					<textarea rows="2" cols="20" class="form-control text <?php if (isset($postLink)) { if (empty($postLink)) { echo 'is-invalid';} } ?>" id="pLink" name="link"><?php echo $oldlink ?></textarea>
					<input type="hidden" name="is_link" value="is_link">
					<br>
					<?php endif;?>

					<?php if ($type == 'is_post'):?>
					<input type="hidden" name="is_post" value="is_post">
					<?php endif;?>
					<input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
					<input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div>
						<label for="wmd-input"><?php echo i18n('Content');?></label>
						<div id="wmd-button-bar" class="wmd-button-bar"></div>
						<textarea id="wmd-input" class="form-control wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'is-invalid'; } } ?>" name="content" cols="20" rows="15"><?php echo $oldcontent ?></textarea><br>
						<?php if ($isdraft[4] == 'draft') { ?>
							<input type="submit" name="publishdraft" class="btn btn-primary submit" value="<?php echo i18n('Publish_draft');?>"/> <input type="submit" name="updatedraft" class="btn btn-primary draft" value="<?php echo i18n('Update_draft');?>"/> <a class="btn btn-danger" href="<?php echo $delete ?>"><?php echo i18n('Delete');?></a>
						<?php } else { ?>
							<input type="submit" name="updatepost" class="btn btn-primary submit" value="<?php echo i18n('Update_post');?>"/> <input type="submit" name="revertpost" class="btn btn-primary revert" value="<?php echo i18n('Revert_to_draft');?>"/> <a class="btn btn-danger" href="<?php echo $delete ?>"><?php echo i18n('Delete');?></a>
						<?php }?>
						<br><br>
					</div>
				</div>
				<div class="col-sm-6">
					<label><?php echo i18n('Preview');?></label>
					<br>
					<div id="wmd-preview" class="wmd-panel wmd-preview" style="width:100%;overflow:auto;"></div>
				</div>
			</div>
		</form>
	</div>

	<style>
	.wmd-prompt-background {z-index:10!important;}
	#wmd-preview img {max-width:100%;}
	</style>
	<div class="modal fade" id="insertImageDialog" tabindex="-1" role="dialog" aria-labelledby="insertImageDialogTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="insertImageDialogTitle"><?php echo i18n('Insert_Image');?></h5>
					<button type="button" class="close" id="insertImageDialogClose" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="insertImageDialogURL">URL</label>
						<input type="text" class="form-control" id="insertImageDialogURL" size="48" placeholder="<?php echo i18n('Enter_image_URL');?>" />
					</div>
					<hr>
					<div class="form-group">
						<label for="insertImageDialogFile"><?php echo i18n('Upload');?></label>
						<input type="file" class="form-control-file" name="file" id="insertImageDialogFile" accept="image/png,image/jpeg,image/gif" />
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="insertImageDialogInsert"><?php echo i18n('Insert_Image');?></button>	
					<button type="button" class="btn btn-secondary"  id="insertImageDialogCancel" data-dismiss="modal"><?php echo i18n('Cancel');?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Declare the base path. Important -->
<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/editor.js"></script>
