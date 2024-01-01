<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php $images = get_gallery(); ?>
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

<div class="row">
    <div class="wmd-panel" style="width:100%;">
        <form method="POST">
            <div class="row">
                <div class="col-sm-6">
                    <label for="pTitle"><?php echo i18n('Title');?> <span class="required">*</span></label>
                    <input type="text" class="form-control text <?php if (isset($postTitle)) {if (empty($postTitle)) {echo 'error';}} ?>" id="pTitle" name="title" value="<?php if (isset($postTitle)) {echo $postTitle;} ?>"/>
                    <br>
                    <label for="pMeta"><?php echo i18n('Meta_description');?> (<?php echo i18n('optional');?>)</label>
                    <textarea id="pMeta" class="form-control" name="description" rows="3" cols="20" placeholder="<?php echo i18n('If_leave_empty_we_will_excerpt_it_from_the_content_below');?>"><?php if (isset($p->description)) {echo $p->description;} ?></textarea>
                    <br>
                </div>
                <div class="col-sm-6">
                    <?php if ($type == 'is_page') :?>
                    <label for="pURL"><?php echo i18n('Slug');?> (<?php echo i18n('optional');?>)</label>
                    <input type="text" class="form-control text" id="pURL" name="url" value="<?php if (isset($postUrl)) {echo $postUrl;} ?>" placeholder="<?php echo i18n('If_the_url_leave_empty_we_will_use_the_page_title');?>"/>
                    <br>
                    <?php endif;?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-sm-6">
                    <label for="wmd-input"><?php echo i18n('Content');?></label>
                    <div id="wmd-button-bar" class="wmd-button-bar"></div>
                    <textarea id="wmd-input" class="form-control wmd-input <?php if (isset($postContent)) {if (empty($postContent)) {echo 'error';}} ?>" name="content" cols="20" rows="10"><?php if (isset($postContent)) {echo $postContent;} ?></textarea>
                    <br>
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
                    <?php if ($type == 'is_page') :?>
                    <input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Publish');?>"/> <input type="submit" name="draft" class="btn btn-primary draft" value="<?php echo i18n('Save_as_draft');?>"/>
                    <?php endif;?>
                    <?php if ($type == 'is_category') :?>
                        <input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Add_category');?>"/>
                    <?php endif;?>
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
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/editor.js"></script>
<script>
  $('.the-img').click(function(e) {
    $('#insertImageDialogURL').val($(e.target).attr('src'));
  });
</script>
