<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php

$type = $type;

if ($type != 'is_post' && $type != 'is_image' && $type != 'is_video' && $type != 'is_audio' && $type != 'is_link' && $type != 'is_quote') {
    $add = site_url() . 'admin/content';
    header("location: $add");
}

$desc = get_category_info(null);
asort($desc);

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
    file_put_contents($tagslang, print_r($tmp, true));
}

$images = get_gallery();

?>

<script src="<?php echo site_url() ?>system/resources/js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="<?php echo site_url() ?>system/admin/editor.md/css/editormd.css" />
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
                    <input autofocus type="text" class="form-control text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';}} ?>" id="pTitle" name="title" value="<?php if (isset($postTitle)) { echo $postTitle;} ?>"/>
                    <br>
                    <label for="pCategory"><?php echo i18n('Category');?> <span class="required">*</span></label>
                    <select id="pCategory" class="form-control" name="category">
                        <?php foreach ($desc as $d):?>
                            <option value="<?php echo $d->md;?>"><?php echo $d->title;?></option>
                        <?php endforeach;?>
                    </select>
                    <br>
                    <label for="pTag"><?php echo i18n('Tags');?> <span class="required">*</span></label>
                    <input type="text" class="form-control text <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error';}} ?>" id="pTag" name="tag" value="<?php if (isset($postTag)) { echo $postTag; } ?>" placeholder="<?php echo i18n('Comma_separated_values');?>"/>
                    <br>
                    <label for="pMeta"><?php echo i18n('Meta_description');?> (<?php echo i18n('optional');?>)</label>
                    <textarea id="pMeta" class="form-control" name="description" rows="3" cols="20" placeholder="<?php echo i18n('If_leave_empty_we_will_excerpt_it_from_the_content_below');?>"><?php if (isset($p->description)) { echo $p->description;} ?></textarea>
                    <br>
                </div>

                <div class="col-sm-6">
                    <div class="form-row">
                        <div class="col">
                            <label for="pDate"><?php echo i18n('Date');?></label>
                            <input type="date" id="pDate" name="date" class="form-control text" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col">
                            <label for="pTime"><?php echo i18n('Time');?></label>
                            <input step="1" type="time" id="pTime" name="time" class="form-control text" value="<?php echo date('H:i:s'); ?>">
                        </div>
                        <small style="margin-top:10px;"><em><?php echo i18n('Scheduled_tips');?></em></small>
                    </div>                
                    <br>
                    <label for="pURL"><?php echo i18n('Slug');?> (<?php echo i18n('optional');?>)</label>
                    <input type="text" class="form-control text" id="pURL" name="url" value="<?php if (isset($postUrl)) { echo $postUrl;} ?>" placeholder="<?php echo i18n('If_the_url_leave_empty_we_will_use_the_post_title');?>"/>
                    <br>

                    <?php if ($type == 'is_audio'):?>
                    <label for="pAudio"><?php echo i18n('Featured_Audio');?> <span class="required">*</span> (e.g Soundcloud)</label>
                    <textarea rows="2" cols="20" class="media-uploader form-control text <?php if (isset($postAudio)) { if (empty($postAudio)) { echo 'error';} } ?>" id="pAudio" name="audio"><?php if (isset($postAudio)) { echo $postAudio;} ?></textarea>
                    <input type="hidden" name="is_audio" value="is_audio">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_video'):?>
                    <label for="pVideo"><?php echo i18n('Featured_Video');?> <span class="required">*</span> (e.g Youtube)</label>
                    <textarea rows="2" cols="20" class="media-uploader form-control text <?php if (isset($postVideo)) { if (empty($postVideo)) { echo 'error';} } ?>" id="pVideo" name="video"><?php if (isset($postVideo)) { echo $postVideo;} ?></textarea>
                    <input type="hidden" name="is_video" value="is_video">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_image'):?>
                    <style>.imgPrev img {width:50%;} </style>
                    <label for="pImage"><?php echo i18n('Featured_Image');?> <span class="required">*</span></label>
                    <br>
                    <label class="btn btn-primary btn-sm" id="insertButton"><?php echo i18n('Insert_Image');?></label>
                    <br>
                    <div class="imgPrev"><?php if (isset($postImage)) { echo '<img id="imgFile" src="' . $postImage . '"/>';} ?></div>
                    <br>
                    <input type="text" class="media-uploader form-control text <?php if (isset($postImage)) { if (empty($postImage)) { echo 'error';}} ?>" id="pImage" name="image" readonly value="<?php if (isset($postImage)) { echo $postImage;} ?>">
                    <input type="hidden" name="is_image" value="is_image">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_quote'):?>
                    <label for="pQuote"><?php echo i18n('Featured_Quote');?> <span class="required">*</span></label>
                    <textarea rows="3" cols="20" class="form-control text <?php if (isset($postQuote)) { if (empty($postQuote)) { echo 'error';} } ?>" id="pQuote" name="quote"><?php if (isset($postQuote)) { echo $postQuote;} ?></textarea>
                    <input type="hidden" name="is_quote" value="is_quote">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_link'):?>
                    <label for="pLink"><?php echo i18n('Featured_Link');?> <span class="required">*</span></label>
                    <textarea rows="2" cols="20" class="form-control text <?php if (isset($postLink)) { if (empty($postLink)) { echo 'error';} } ?>" id="pLink" name="link"><?php if (isset($postLink)) { echo $postLink;} ?></textarea>
                    <input type="hidden" name="is_link" value="is_link">
                    <br>
                    <?php endif;?>

                    <?php if ($type == 'is_post'):?>
                    <input type="hidden" name="is_post" value="is_post">
                    <?php endif;?>
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div>
                        <label for="wmd-input"><?php echo i18n('Content');?></label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label><?php echo i18n('Preview');?></label>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div id="htmly-editormd" >
                        <textarea id="wmd-input" class="form-control wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error'; } } ?>" name="content" style="display:none;"><?php if (isset($postContent)) { echo $postContent;} ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <input type="submit" name="publish" class="btn btn-primary submit" value="<?php echo i18n('Publish');?>"/> <input type="submit" name="draft" class="btn btn-primary draft" value="<?php echo i18n('Save_as_draft');?>"/>
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
                    <?php if (!empty($images)) :?>
                    <div class="form-group">
                        <div class="row-fluid cover-container">
                            <?php foreach ($images as $img):?>
                                <div class="cover-item">
                                    <img class="img-thumbnail the-img" src="<?php echo site_url() . $img['dirname'] . '/' . $img['basename']?>">
                                </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                    <hr>
                    <?php endif;?>
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
                    <?php if (!empty($images)) :?>
                    <div class="form-group">
                        <div class="row-fluid cover-container">
                            <?php foreach ($images as $img):?>
                                <div class="cover-item">
                                    <img class="img-thumbnail the-img" src="<?php echo site_url() . $img['dirname'] . '/' . $img['basename']?>">
                                </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                    <hr>
                    <?php endif;?>
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
<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
<script type="text/javascript" src="<?php echo site_url() ?>system/resources/js/media.uploader.js"></script>
<script>
  $('.the-img').click(function(e) {
    $('#insertMediaDialogURL').val($(e.target).attr('src'));
    $('#insertImageDialogURL').val($(e.target).attr('src'));
  });
</script>
<script src="<?php echo site_url() ?>system/admin/editor.md/editormd.min.js"></script>
<script type="text/javascript">
    var htmlyEditor;
    $(function() {
        htmlyEditor = editormd("htmly-editormd", {
            width   : "100%",
            height  : 640,
            syncScrolling : "single",
            path    : "<?php echo site_url() ?>system/admin/editor.md/lib/",
            imageUpload : true,
            imageFormats : ['jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'gif'],
            imageUploadURL : "<?php echo site_url() ?>system/admin/editor.md/upload.php",
        });
    });
</script>
<script src="<?php echo site_url() ?>system/admin/editor.md/languages/en.js"></script>