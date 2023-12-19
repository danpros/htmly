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
        $oldcontent = i18n('Author_Description');
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

$images = get_gallery();

?>
<script src="<?php echo site_url() ?>system/resources/js/jquery.min.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/jquery-ui.min.js"></script>
<link rel="stylesheet" href="<?php echo site_url() ?>system/admin/editor.md/css/editormd.css" />
<link rel="stylesheet" href="<?php echo site_url() ?>system/resources/css/jquery-ui.css">

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
                    <label for="pMeta"><?php echo i18n('Meta_description');?> <?php echo i18n('optional');?></label>
                    <br />
                    <textarea id="pMeta" class="form-control" name="description" rows="3" cols="20" placeholder="<?php echo i18n('If_leave_empty_we_will_excerpt_it_from_the_content_below');?>"><?php if (isset($p->description)) { echo $p->description;} else {echo $olddescription;}?></textarea>
                    <br /><br />
                    <?php } ?>
                </div>
                <div class="col-sm-6">
                    <?php if($type != 'is_frontpage' && $type != 'is_profile') { ?>
                    <label for="pURL"><?php echo i18n('Slug');?> (<?php echo i18n('optional');?>)</label>
                    <br>
                    <input type="text" id="pURL" name="url" class="form-control text" value="<?php echo $oldmd ?>" placeholder="<?php echo i18n('If_the_url_leave_empty_we_will_use_the_page_title');?>"/>
                    <br>
                    <?php } ?>
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
                        <textarea id="wmd-input" class="form-control wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error'; } } ?>" name="content" style="display:none;"><?php echo $oldcontent ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
                <?php if($type == 'is_frontpage' || $type == 'is_profile') { ?>
                    <input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Save');?>"/>
                <?php } elseif ($type == 'is_category') {?>
                    <input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
                    <input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Save_category');?>"/>
                <?php } else {?>
                    <input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
                    <input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Save');?>"/> <a class="btn btn-danger" href="<?php echo $delete ?>"><?php echo i18n('Delete');?></a>
                <?php } ?>
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
</div>
<!-- Declare the base path. Important -->
<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
<script>
  $('.the-img').click(function(e) {
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