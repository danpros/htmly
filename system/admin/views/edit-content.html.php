<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
if (isset($p->file)) {
    $file_path = pathinfo($p->file);
} else {
    $file_path = pathinfo($oldfile);
}

$filename = $file_path['dirname'] . '/' . $file_path['basename'];

$desc = get_category_info(null);

$content = file_get_contents($filename);
$oldtitle = get_content_tag('t', $content, 'Untitled');
$olddescription = get_content_tag('d', $content);
$oldtag = get_content_tag('tag', $content);
$oldcomment = check_comments_state($content);
$oldcontent = remove_html_comments($content);

$oldimage = get_content_tag('image', $content);
$oldaudio = get_content_tag('audio', $content);
$oldvideo = get_content_tag('video', $content);
$oldlink = get_content_tag('link', $content);
$oldquote = get_content_tag('quote', $content);

$dir = $file_path['dirname'];
$isdraft = explode('/', $dir);
$oldurl = explode('_', $file_path['basename']);

if (empty($oldtag)) {
    $oldtag = $oldurl[1];
}

$oldmd = str_replace('.md', '', $oldurl[2]);

if (isset($_GET['destination'])) {
    $destination = _h($_GET['destination']);
} else {
    $destination = 'admin';
}

$cat = explode('/', $dir);
$category = $cat[3];

$dt = $oldurl[0];
$t = str_replace('-', '', $dt);
$time = new DateTime($t);
$timestamp = $time->format("Y-m-d H:i:s");
// The post date
$postdate = strtotime($timestamp);
// The post URL
if (permalink_type() == 'default') {
    $delete = site_url() . date('Y/m', $postdate) . '/' . $oldmd . '/delete?destination=' . $destination;
} else {
    // The post URL
    $delete = site_url() . permalink_type() . '/' . $oldmd . '/delete?destination=' . $destination;
}

$tags = tag_cloud(true);
$tagslang = "content/data/tags.lang";
if (file_exists($tagslang)) {
    $ptags = unserialize(file_get_contents($tagslang));
    $tkey = array_keys($tags);
    if (!empty($ptags)) {
        $newlang = array_intersect_key($ptags, array_flip($tkey));
    } else {
        $newlang = array_combine($tkey, $tkey);
    }
    $tmp = serialize($newlang);
    file_put_contents($tagslang, print_r($tmp, true), LOCK_EX);
}

$images = image_gallery(null, 1, 40);

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
<div class="notice" id="response"></div>
<div class="row">
    <div class="hide-button" style="margin-bottom:1em;width:100%;text-align:right;"><button type="button" title="<?php echo i18n('Focus_mode');?>" id="hideButton" class="note-btn btn btn-light btn-sm" style="width:38px;height:38px;font-size:18px;" ><i class="fa fa-eye" aria-hidden="true"></i></button></div>
    <div class="wmd-panel" style="width:100%;">
        <form method="POST">
            <div id="post-settings" class="row">
                <div class="col-sm-6">
                    <label for="pTitle"><?php echo i18n('Title');?> <span class="required">*</span></label>
                    <input autofocus type="text" id="pTitle" name="title" class="form-control text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';} } ?>" value="<?php echo $oldtitle ?>"/>
                    <br>
                    <label for="pCategory"><?php echo i18n('Category');?> <span class="required">*</span></label>
                    <select id="pCategory" class="form-control" name="category">
                        <?php foreach ($desc as $d):?>
                            <option value="<?php echo $d->slug;?>" <?php if($category === $d->slug) { echo 'selected="selected"';} ?>><?php echo $d->title;?></option>
                        <?php endforeach;?>
                    </select>
                    <br>
                    <label for="pTag"><?php echo i18n('Tags');?> <span class="required">*</span></label>
                    <input type="text" id="pTag" name="tag" class="form-control text <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error'; } } ?>" value="<?php echo $oldtag ?>" placeholder="<?php echo i18n('Comma_separated_values');?>"/>
                    <br>

                    <label for="pMeta"><?php echo i18n('Meta_description');?> (<?php echo i18n('optional');?>)</label>
                    <textarea id="pMeta" class="form-control" name="description" rows="3" cols="20" placeholder="<?php echo i18n('If_left_empty_we_will_excerpt_it_from_the_content_below');?>"><?php if (isset($p->description)) { echo $p->description; } else { echo $olddescription;} ?></textarea>
                    <br>
                    <label for="pComments"><?php echo i18n('Comment_State');?></label>
                    <select id="pComments" class="form-control" name="comments">
                        <option value="true" <?php if($oldcomment == "true") { echo 'selected="selected"';} ?>><?php echo i18n('Comments_enabled');?></option>
                        <option value="false" <?php if($oldcomment == "false") { echo 'selected="selected"';} ?>><?php echo i18n('Comments_disabled');?></option>
                    </select>
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
                        <small style="margin-top:10px;"><em><?php echo i18n('Scheduled_tips');?></em></small>
                    </div>                
                    <br>
                    <label for="pURL"><?php echo i18n('Slug');?>  (<?php echo i18n('optional');?>)</label>
                    <input type="text" id="pURL" name="url" class="form-control text" value="<?php echo $oldmd ?>" placeholder="<?php echo i18n('If_the_url_is_left_empty_we_will_use_the_post_title');?>"/>
                    <br>

                    <?php if ($type == 'is_audio'):?>
                    <label for="pAudio"><?php echo i18n('Featured_Audio');?>  <span class="required">*</span> (e.g Soundcloud)</label>
                    <textarea rows="2" cols="20" class="media-uploader form-control text <?php if (isset($postAudio)) { if (empty($postAudio)) { echo 'error';} } ?>" id="pAudio" name="audio"><?php echo $oldaudio; ?></textarea>
                    <input type="hidden" name="is_audio" value="is_audio">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_video'):?>
                    <label for="pVideo"><?php echo i18n('Featured_Video');?> <span class="required">*</span> (e.g Youtube)</label>
                    <textarea rows="2" cols="20" class="media-uploader form-control text <?php if (isset($postVideo)) { if (empty($postVideo)) { echo 'error';} } ?>" id="pVideo" name="video"><?php echo $oldvideo ?></textarea>
                    <input type="hidden" name="is_video" value="is_video">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_image'):?>
                    <style>.imgPrev img {width:50%;} </style>
                    <label for="pImage"><?php echo i18n('Featured_Image');?> <span class="required">*</span></label>
                    <br>
                    <label class="btn btn-primary btn-sm" id="insertButton"><?php echo i18n('Insert_Image');?></label>
                    <br>
                    <div class="imgPrev"><img id="imgFile" src="<?php echo $oldimage; ?>"/></div>
                    <br>
                    <input type="text" class="media-uploader form-control text <?php if (isset($postImage)) { if (empty($postImage)) { echo 'error';}} ?>" id="pImage" name="image" readonly value="<?php echo $oldimage; ?>">
                    <input type="hidden" name="is_image" value="is_image">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_quote'):?>
                    <label for="pQuote"><?php echo i18n('Featured_Quote');?> <span class="required">*</span></label>
                    <textarea rows="3" cols="20" class="form-control text <?php if (isset($postQuote)) { if (empty($postQuote)) { echo 'error';} } ?>" id="pQuote" name="quote"><?php echo $oldquote ?></textarea>
                    <input type="hidden" name="is_quote" value="is_quote">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_link'):?>
                    <label for="pLink"><?php echo i18n('Featured_Link');?> <span class="required">*</span></label>
                    <textarea rows="2" cols="20" class="form-control text <?php if (isset($postLink)) { if (empty($postLink)) { echo 'error';} } ?>" id="pLink" name="link"><?php echo $oldlink ?></textarea>
                    <input type="hidden" name="is_link" value="is_link">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_post'):?>
                    <input type="hidden" name="is_post" value="is_post">
                    <?php endif;?>
                    <input type="hidden" name="oldfile" class="text" value="<?php echo $filename; ?>"/>
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="text-align:right;">
                    <button class="note-btn btn btn-light btn-sm" style="width:38px;height:38px;font-size:18px;" type="button" title="Toggle <?php echo i18n('Preview');?>" id="preview-toggle" class="btn btn-secondary btn-xs"><i class="fa fa-columns" aria-hidden="true"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6" id="editor-col">
                    <div>
                        <input type="hidden" id="pType" name="posttype" value="<?php echo $type; ?>">
                        <label for="wmd-input"><?php echo i18n('Content');?> <span class="required">*</span></label>
                        <div id="wmd-button-bar" class="wmd-button-bar"></div>
                        <textarea id="wmd-input" class="form-control wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error'; } } ?>" name="content" cols="20" rows="15"><?php echo $oldcontent ?></textarea><br>
                        <?php if ($isdraft[4] == 'draft') { ?>
                            <input type="submit" name="publishdraft" class="btn btn-primary submit" value="<?php echo i18n('Publish_draft');?>"/> <input type="submit" name="updatedraft" class="btn btn-primary draft" value="<?php echo i18n('Update_draft');?>"/> <a class="btn btn-danger" href="<?php echo $delete ?>"><?php echo i18n('Delete');?></a>
                        <?php } else { ?>
                            <input type="submit" name="updatepost" class="btn btn-primary submit" value="<?php echo i18n('Update_post');?>"/> <input type="submit" name="revertpost" class="btn btn-primary revert" value="<?php echo i18n('Revert_to_draft');?>"/> <a class="btn btn-danger" href="<?php echo $delete ?>"><?php echo i18n('Delete');?></a>
                        <?php }?>
                        <br><br>
                    </div>
                </div>
                <div class="col-sm-6" id="preview-col">
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
.cover-container {
    height: 200px;
    width: 100%;
    white-space: nowrap;
    overflow-x: scroll;
    overflow-y: hidden;
}
.cover-item {
    position: relative;
    display: inline-block;
    margin: 2px 2px;
    border-top-right-radius: 2px;
    width: 200px;
    height: 150px;
    vertical-align: top;
    background-position: top left;
    background-repeat: no-repeat;
    background-size: cover;
}
</style>

    <div class="modal fade" id="insertImageDialog" tabindex="-1" role="dialog" aria-labelledby="insertImageDialogTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertImageDialogTitle"><?php echo i18n('Insert_Image');?></h5>
                    <button type="button" class="close" id="insertImageDialogClose" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row-fluid img-container" id="gallery-1">
                            <?php echo $images;?>
                        </div>
                    </div>
                    <hr>
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
    <?php if ($type == 'is_image'):?>
    <div class="modal fade" id="insertMediaDialog" tabindex="-1" role="dialog" aria-labelledby="insertMediaDialogTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertMediaDialogTitle"><?php echo i18n('Insert_Image');?></h5>
                    <button type="button" class="close" id="insertMediaDialogClose" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row-fluid img-container" id="gallery-2">
                            <?php echo $images;?>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="insertMediaDialogURL">URL</label>
                        <input type="text" class="form-control" id="insertMediaDialogURL" size="48" placeholder="<?php echo i18n('Enter_image_URL');?>" />
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="insertMediaDialogFile"><?php echo i18n('Upload');?></label>
                        <input type="file" class="form-control-file" name="file" id="insertMediaDialogFile" accept="image/png,image/jpeg,image/gif" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="insertMediaDialogInsert"><?php echo i18n('Insert_Image');?></button>    
                    <button type="button" class="btn btn-secondary"  id="insertMediaDialogCancel" data-dismiss="modal"><?php echo i18n('Cancel');?></button>
                </div>
            </div>
        </div>
    </div>
    <?php endif;?>
    
</div>
<!-- Declare the base path. Important -->
<script type="text/javascript">
    var base_path = '<?php echo site_url() ?>';
    var initial_image = '<?php echo $images;?>';
    var parent_page = '';
    var oldfile = '<?php echo $filename;?>';
    var addEdit = 'edit';
    var saveInterval = 60000;
</script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/editor.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/resources/js/media.uploader.js"></script>
<script>
function loadImages(page) {
  $.ajax({
    url: '<?php echo site_url();?>admin/gallery',
    type: 'POST',
    data: { page: page },
    dataType: 'json',
      success: function(response) {
        $('#gallery-1').html(response.images);
        $('#gallery-2').html(response.images);
      }
  });
}

$('.img-container').on("click", ".the-img", function(e) {
  $('#insertMediaDialogURL').val($(e.target).attr('src'));
  $('#insertImageDialogURL').val($(e.target).attr('src'));
});
</script>
<script>
    function toggleDivs() {
        var div1 = document.getElementById('post-settings');
        if (div1.style.display === 'none') {
            div1.style.display = '';
            document.body.classList.add("sidebar-mini");
            document.body.classList.remove("sidebar-collapse");
        } else {
            div1.style.display = 'none';
            document.body.classList.remove("sidebar-mini");
            document.body.classList.add("sidebar-collapse");
        }
    }
    document.getElementById('hideButton').addEventListener('click', toggleDivs);
</script>
<?php if (config('autosave.enable') == 'true' ):?>
<?php if ($isdraft[4] == 'draft') : ?>
<script src="<?php echo site_url();?>system/resources/js/save_draft.js"></script>
<?php endif;?>
<?php endif;?>
<script>
    if (localStorage.getItem("preview-state") === "open") {
        document.getElementById("editor-col").classList.remove('col-sm-12');
        document.getElementById("editor-col").classList.add('col-sm-6');
        document.getElementById("preview-col").style.display = '';
    } else if (localStorage.getItem("preview-state") === "close") {
        document.getElementById("editor-col").classList.remove('col-sm-6');
        document.getElementById("editor-col").classList.add('col-sm-12');
        document.getElementById("preview-col").style.display = 'none';
    }
    document.getElementById("preview-toggle").addEventListener("click", () => {
        if (document.getElementById("editor-col").className.includes("col-sm-6")) {
            document.getElementById("editor-col").classList.remove('col-sm-6');
            document.getElementById("editor-col").classList.add('col-sm-12');
            document.getElementById("preview-col").style.display = 'none';
            localStorage.setItem("preview-state", 'close');
        } else {
            document.getElementById("editor-col").classList.remove('col-sm-12');
            document.getElementById("editor-col").classList.add('col-sm-6');
            document.getElementById("preview-col").style.display = '';
            localStorage.setItem("preview-state", 'open');
        }
    })
</script>
