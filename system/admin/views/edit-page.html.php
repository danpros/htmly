<?php if (!defined('HTMLY')) die('HTMLy'); ?>
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

    if (isset($_SESSION[site_url()]['user'])) {
        $user = $_SESSION[site_url()]['user'];
    }

    $filename = 'content/' . $user . '/author.md';

    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $oldtitle = get_content_tag('t', $content, 'user');
        $olddescription = get_content_tag('d', $content, remove_html_comments($content));
        $oldcontent = remove_html_comments($content);
        $oldimage = get_content_tag('image', $content);
    } else {
        $oldtitle = $user;
        $olddescription = i18n('Author_Description');
        $oldcontent = i18n('Author_Description');
        $oldimage = '';
    }

} elseif ($type == 'is_category') {
    $content = $p->body;
    $oldtitle = $p->title;
    $olddescription = $p->description;
    $oldcontent = $p->body;
    $oldmd = $p->slug;
    $url = 'content/data/category/'. $p->slug . '.md';
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
    $dir = pathinfo($url, PATHINFO_DIRNAME);
    $oldurl = pathinfo($url, PATHINFO_BASENAME);
    
    $fn = explode('.', pathinfo($url, PATHINFO_FILENAME));
    if (isset($fn[1])) {
        $oldmd = $fn[1];
    } else {
        $oldmd = pathinfo($url, PATHINFO_FILENAME);
    }

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
                    <input type="text" id="pTitle" name="title" class="form-control text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error'; } } ?>" value="<?php echo $oldtitle ?>"/>
                    <br>
                    <?php if($type != 'is_frontpage') { ?>
                    <label for="pMeta"><?php echo i18n('Meta_description');?> (<?php echo i18n('optional');?>)</label>
                    <br />
                    <textarea id="pMeta" class="form-control" name="description" rows="3" cols="20" placeholder="<?php echo i18n('If_left_empty_we_will_excerpt_it_from_the_content_below');?>"><?php if (isset($p->description)) { echo $p->description;} else {echo $olddescription;}?></textarea>
                    <br /><br />
                    <?php } ?>
                </div>
                <div class="col-sm-6">
                    <?php if($type != 'is_frontpage' && $type != 'is_profile') { ?>
                    <label for="pURL"><?php echo i18n('Slug');?> (<?php echo i18n('optional');?>)</label>
                    <br>
                    <input type="text" id="pURL" name="url" class="form-control text" value="<?php echo $oldmd ?>" placeholder="<?php echo i18n('If_the_url_is_left_empty_we_will_use_the_page_title');?>"/>
                    <br>
                    <?php } ?>

                    <?php if ($type == 'is_profile'):?>
                    <style>.imgPrev img {width:50%;} </style>
                    <label for="pImage">Avatar (<?php echo i18n('optional');?>)</label>
                    <br>
                    <label class="btn btn-primary btn-sm" id="insertButton"><?php echo i18n('Insert_Image');?></label>
                    <br>
                    <div class="imgPrev"><img id="imgFile" src="<?php echo $oldimage; ?>"/></div>
                    <br>
                    <input type="text" class="media-uploader form-control text <?php if (isset($postImage)) { if (empty($postImage)) { echo 'error';}} ?>" id="pImage" name="image" readonly value="<?php echo $oldimage; ?>">
                    <input type="hidden" name="is_image" value="is_image">
                    <br>
                    <?php endif;?>

                </div>
            </div>
             <div class="row">
                <div class="col-sm-12" style="text-align:right;">
                    <button class="note-btn btn btn-light btn-sm" style="width:38px;height:38px;font-size:18px;" type="button" title="Toggle <?php echo i18n('Preview');?>" id="preview-toggle" class="btn btn-secondary btn-xs"><i class="fa fa-columns" aria-hidden="true"></i></button>
                </div>
            </div>                
            <div class="row">
                <div class="col-sm-6" id="editor-col">
                    <label for="wmd-input"><?php echo i18n('Content');?> <span class="required">*</span></label>
                    <div id="wmd-button-bar" class="wmd-button-bar"></div>
                    <textarea id="wmd-input" class="form-control wmd-input <?php if (isset($postContent)) {if (empty($postContent)) {echo 'error';}} ?>" name="content" cols="20" rows="10"><?php echo $oldcontent ?></textarea>
                    <br>
                    <input type="hidden" id="pType" name="posttype" value="<?php echo $type; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
                    <?php if($type == 'is_frontpage' || $type == 'is_profile') { ?>
                        <input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Save');?>"/>
                    <?php } elseif ($type == 'is_category') {?>
                        <input id="oldfile" type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
                        <input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Save_category');?>"/>
                    <?php } else {?>
                        <input id="oldfile" type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
                        <?php $dd = find_subpage($oldmd); ?>
                        <?php $dr = find_draft_subpage($oldmd);?>
                        <?php if (stripos($dir . '/', '/draft/') !== false) { ?>
                        <input type="submit" name="publishdraft" class="btn btn-primary submit" value="<?php echo i18n('Publish_draft');?>"/> <input type="submit" name="updatedraft" class="btn btn-primary draft" value="<?php echo i18n('Update_draft');?>"/> <?php if (empty($dd) && empty($dr)):?><a class="btn btn-danger" href="<?php echo $delete ?>"><?php echo i18n('Delete');?></a><?php endif;?>

                        <?php } else { ?>
                        <input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Save');?>"/> <?php if (empty($dd) && empty($dr)):?><input type="submit" name="revertpage" class="btn btn-primary revert" value="<?php echo i18n('Revert_to_draft');?>"/> <a class="btn btn-danger" href="<?php echo $delete ?>"><?php echo i18n('Delete');?></a><?php endif;?>
                        <?php } ?>
                    <?php } ?>
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
	
    <?php if ($type == 'is_profile'):?>
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
    var parent_page = '<?php echo isset($parent) ? $parent : '';?>';
    var addEdit = 'edit';
    var saveInterval = 60000;
</script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/editor.js"></script>
<?php if ($type == 'is_profile'):?>
<script type="text/javascript" src="<?php echo site_url() ?>system/resources/js/media.uploader.js"></script>
<?php endif;?>
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
<?php if (config('autosave.enable') == 'true'):?>
<?php if ($type !== 'is_category' && $type !== 'is_profile' && $type !== 'is_frontpage') :?>
<?php if (stripos($dir . '/', '/draft/') !== false): ?>
<script src="<?php echo site_url();?>system/resources/js/save_draft.js?v=1"></script>
<?php endif;?>
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
