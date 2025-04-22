<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php

$type = $type;

if ($type != 'is_post' && $type != 'is_image' && $type != 'is_video' && $type != 'is_audio' && $type != 'is_link' && $type != 'is_quote') {
    $add = site_url() . 'admin/content';
    header("location: $add");
}

$desc = get_category_info(null);

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

$fields = array();
$field_file= 'content/data/field/post.json';
if (file_exists($field_file)) {
    $fields = json_decode(file_get_contents($field_file, true));
}
?>

<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css"/>
<script src="<?php echo site_url() ?>system/resources/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Extra.js"></script>
<link rel="stylesheet" href="<?php echo site_url() ?>system/resources/css/jquery-ui.css">
<script>
$( function() {
    // Decode HTML entities
    function decodeHtml(html) {
      var txt = document.createElement("textarea");
      txt.innerHTML = html;
      return txt.value;
    }

    var availableTags = [
<?php foreach ($tags as $tag => $count):?>
    "<?php echo tag_i18n($tag) ?>",
<?php endforeach;?>
    ].map(decodeHtml); // Decoding all tags

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
    <div class="hide-button" style="margin-bottom:1em;width:100%;text-align:right;"><button type="button" title="<?php echo i18n('Focus_mode');?>" id="hideButton" class="note-btn btn btn-sm <?php echo ((config('admin.theme') === 'light' || is_null(config('admin.theme'))) ? "btn-light" : "btn-dark");?>" style="width:38px;height:38px;font-size:18px;" ><i class="fa fa-eye" aria-hidden="true"></i></button></div>
    <div class="wmd-panel" style="width:100%;">
        <form method="POST">
            <div id="post-settings" class="row">
                <div class="col-sm-6">
                    <label for="pTitle"><?php echo i18n('Title');?> <span class="required">*</span></label>
                    <input autofocus type="text" class="form-control text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';}} ?>" id="pTitle" name="title" value="<?php if (isset($postTitle)) { echo $postTitle;} ?>"/>
                    <br>
                    <label for="pCategory"><?php echo i18n('Category');?> <span class="required">*</span></label>
                    <select id="pCategory" class="form-control" name="category">
                        <?php foreach ($desc as $d):?>
                            <option value="<?php echo $d->slug;?>"><?php echo $d->title;?></option>
                        <?php endforeach;?>
                    </select>
                    <br>
                    <label for="pTag"><?php echo i18n('Tags');?> <span class="required">*</span></label>
                    <input type="text" class="form-control text <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error';}} ?>" id="pTag" name="tag" value="<?php if (isset($postTag)) { echo $postTag; } ?>" placeholder="<?php echo i18n('Comma_separated_values');?>"/>
                    <br>
                    <label for="pMeta"><?php echo i18n('Meta_description');?> (<?php echo i18n('optional');?>)</label>
                    <textarea id="pMeta" class="form-control" name="description" rows="3" cols="20" placeholder="<?php echo i18n('If_left_empty_we_will_excerpt_it_from_the_content_below');?>"><?php if (isset($p->description)) { echo $p->description;} ?></textarea>
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
                    <input type="text" class="form-control text" id="pURL" name="url" value="<?php if (isset($postUrl)) { echo $postUrl;} ?>" placeholder="<?php echo i18n('If_the_url_is_left_empty_we_will_use_the_post_title');?>"/>
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
                    <input id="oldfile" type="hidden" name="oldfile" class="text"/>
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" style="text-align:right;">
                    <button class="note-btn btn btn-sm <?php echo ((config('admin.theme') === 'light' || is_null(config('admin.theme'))) ? "btn-light" : "btn-dark");?>" style="width:38px;height:38px;font-size:18px;" type="button" title="Toggle <?php echo i18n('Preview');?>" id="preview-toggle" class="btn btn-secondary btn-xs"><i class="fa fa-columns" aria-hidden="true"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6" id="editor-col">
                    <div>
                        <input type="hidden" id="pType" name="posttype" value="<?php echo $type; ?>">
                        <label for="wmd-input"><?php echo i18n('Content');?> <span class="required">*</span></label>
                        <div id="wmd-button-bar" class="wmd-button-bar"></div>
                        <textarea id="wmd-input" class="form-control wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error'; } } ?>" name="content" cols="20" rows="15"><?php if (isset($postContent)) { echo $postContent;} ?></textarea><br>
                        <?php if(!empty($fields)):?>
                        <details id="custom-fields"  >
                        <summary id="custom-fields-click" style="padding:10px; margin-bottom:10px; <?php echo ((config('admin.theme') === 'light' || is_null(config('admin.theme'))) ? "background-color: #E4EBF1;" : "background-color: rgba(255,255,255,.1);");?>"><strong><?php echo i18n('custom_fields');?></strong></summary>
                        <div class="row">
                            <div class="col">
                                <?php foreach ($fields as $fld):?>
                                    <?php if ($fld->type == 'text'):?>
                                    <label><?php echo $fld->label;?></label>
                                    <input type="<?php echo $fld->type;?>" placeholder="<?php echo $fld->info;?>" class="form-control text" id="<?php echo $fld->name;?>" name="<?php echo $fld->name;?>" value=""/>
                                    <br>
                                    <?php elseif ($fld->type == 'textarea'):?>
                                    <label><?php echo $fld->label;?></label>
                                    <textarea class="form-control text" id="<?php echo $fld->name;?>" rows="3" placeholder="<?php echo $fld->info;?>" name="<?php echo $fld->name;?>"></textarea>
                                    <br>
                                    <?php elseif ($fld->type == 'checkbox'):?>
                                    <input type="<?php echo $fld->type;?>" id="<?php echo $fld->name;?>" name="<?php echo $fld->name;?>" >
                                    <label for="<?php echo $fld->name;?>"><?php echo $fld->label;?></label>
									<span class="d-block mt-1"><small><em><?php echo $fld->info;?></em></small></span>
                                    <br>
                                    <?php elseif ($fld->type == 'select'):?>
                                    <label for="<?php echo $fld->name;?>"><?php echo $fld->label;?></label>
                                    <select id="<?php echo $fld->name;?>" class="form-control" name="<?php echo $fld->name;?>">
                                    <?php foreach ($fld->options as $val):?>
                                        <option value="<?php echo $val->value;?>" ><?php echo $val->label;?></option>
                                    <?php endforeach;?>
                                    </select>
									<span class="d-block mt-1"><small><em><?php echo $fld->info;?></em></small></span>
                                    <?php endif;?>        
                                <?php endforeach;?>
                            </div>
                        </div>
                        </details>
                        <br>
                        <script>if(localStorage.getItem("custom-fields-state")==="open"){document.getElementById("custom-fields").setAttribute("open","")}document.getElementById("custom-fields-click").addEventListener("click",()=>{if(document.getElementById("custom-fields").open){localStorage.setItem("custom-fields-state",'close')}else{localStorage.setItem("custom-fields-state",'open')}})</script>
                        <?php endif;?>
                        <input type="submit" name="publish" class="btn btn-primary submit" value="<?php echo i18n('Publish');?>"/> <input type="submit" name="draft" class="btn btn-primary draft" value="<?php echo i18n('Save_as_draft');?>"/>
                        <br>
                    </div>
                </div>
                <div class="col-sm-6" id="preview-col">
                    <label><?php echo i18n('Preview');?></label>
                    <br>
                    <div id="wmd-preview" class="wmd-panel wmd-preview <?php if (config('admin.theme') === 'dark'){echo "card";}?>" style="width:100%;overflow:auto;"></div>
                </div>
            </div>
        </form>
    </div>

<style>
.wmd-prompt-background {z-index:10!important;}
#wmd-preview img {max-width:100%;}
.cover-container {
    overflow: auto;
    max-height: 65vh;
    width: 100%;
    white-space: nowrap;
}
.cover-item {
    position: relative;
    margin: 2px 2px;
    border-top-right-radius: 2px;
    width: 190px;
    height: 140px;
    vertical-align: top;
    background-position: top left;
    background-repeat: no-repeat;
    background-size: cover;
    float:left;
}
</style>

    <div class="modal fade" id="insertImageDialog" tabindex="-1" role="dialog" aria-labelledby="insertImageDialogTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="insertImageDialogTitle"><?php echo i18n('Insert_Image');?></p>
                    <button type="button" class="close" id="insertImageDialogClose" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-9">
                            <div class="form-group">
                                <div class="row-fluid img-container" id="gallery-1">
                                    <?php echo $images;?>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="insertImageDialogURL">URL</label>
                                <textarea class="form-control" id="insertImageDialogURL" rows="5" placeholder="<?php echo i18n('Enter_image_URL');?>" ></textarea>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="insertImageDialogFile"><?php echo i18n('Upload');?></label>
                                <input type="file" class="form-control-file" name="file" id="insertImageDialogFile" accept="image/png,image/jpeg,image/gif, image/webp" />
                            </div>
                            <hr>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" id="insertImageDialogInsert"><?php echo i18n('Insert_Image');?></button>    
                                <button type="button" class="btn btn-secondary"  id="insertImageDialogCancel" data-dismiss="modal"><?php echo i18n('Cancel');?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
    <?php if ($type == 'is_image'):?>
    <div class="modal fade" id="insertMediaDialog" tabindex="-1" role="dialog" aria-labelledby="insertMediaDialogTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="insertMediaDialogTitle"><?php echo i18n('Insert_Image');?></p>
                    <button type="button" class="close" id="insertMediaDialogClose" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-9">
                            <div class="form-group">
                                <div class="row-fluid img-container" id="gallery-2">
                                    <?php echo $images;?>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="insertMediaDialogURL">URL</label>
                                <textarea class="form-control" id="insertMediaDialogURL" rows="5" placeholder="<?php echo i18n('Enter_image_URL');?>"></textarea>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="insertMediaDialogFile"><?php echo i18n('Upload');?></label>
                                <input type="file" class="form-control-file" name="file" id="insertMediaDialogFile" accept="image/png,image/jpeg,image/gif, image/webp" />
                            </div>
                            <hr>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" id="insertMediaDialogInsert"><?php echo i18n('Insert_Image');?></button>    
                                <button type="button" class="btn btn-secondary"  id="insertMediaDialogCancel" data-dismiss="modal"><?php echo i18n('Cancel');?></button>
                            </div>
                        </div>
                    </div>
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
    var addEdit = 'add';
    var saveInterval = 60000;
    const field = [<?php foreach ($fields as $f){ echo '"' . $f->name . '", ';}?>];
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
<script src="<?php echo site_url();?>system/resources/js/save_draft.js?v=1"></script>
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

