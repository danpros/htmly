<?php 

$type = $type;

if ($type != 'is_post' && $type != 'is_image' && $type != 'is_video' && $type != 'is_audio' && $type != 'is_link' && $type != 'is_quote') {
    $add = site_url() . 'admin/content';
    header("location: $add");    
}

$desc = get_category_info(null);

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

<div class="wmd-panel">
    <form method="POST">
        Title <span class="required">*</span>
        <br>
        <input type="text" class="text <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';}} ?>" name="title" value="<?php if (isset($postTitle)) { echo $postTitle;} ?>"/>
        <br><br>
        Category <span class="required">*</span>
        <br>
        <select name="category">
            <option value="uncategorized">Uncategorized</option>
            <?php foreach ($desc as $d):?>
                <option value="<?php echo $d->md;?>"><?php echo $d->title;?></option>
            <?php endforeach;?>
        </select> 
        <br><br>
        Tag <span class="required">*</span>
        <br>
        <input type="text" class="text <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error';}} ?>" name="tag" value="<?php if (isset($postTag)) { echo $postTag; } ?>"/>
        <br><br>
        Url (optional)<br><input type="text" class="text" name="url" value="<?php if (isset($postUrl)) { echo $postUrl;} ?>"/>
        <br>
        <span class="help">If the url leave empty we will use the post title.</span>
        <br><br>
        Meta Description (optional)<br>
        <textarea name="description" rows="3" cols="20"><?php if (isset($p->description)) { echo $p->description;} ?></textarea>
        <br><br>
        
        <?php if ($type == 'is_audio'):?>
        Featured Audio <span class="required">*</span> (SoundCloud Only)
        <br>
        <textarea rows="3" cols="20" class="text <?php if (isset($postAudio)) { if (empty($postAudio)) { echo 'error';} } ?>" name="audio"><?php if (isset($postAudio)) { echo $postAudio;} ?></textarea>
        <input type="hidden" name="is_audio" value="is_audio">
        <br>
        <?php endif;?>
        
        <?php if ($type == 'is_video'):?>
        Featured Video <span class="required">*</span> (Youtube Only)
        <br>
        <textarea rows="3" cols="20" class="text <?php if (isset($postVideo)) { if (empty($postVideo)) { echo 'error';} } ?>" name="video"><?php if (isset($postVideo)) { echo $postVideo;} ?></textarea>
        <input type="hidden" name="is_video" value="is_video">
        <br>
        <?php endif;?>
        
        <?php if ($type == 'is_image'):?>
        Featured Image <span class="required">*</span>
        <br>
        <textarea rows="3" cols="20" class="text <?php if (isset($postImage)) { if (empty($postImage)) { echo 'error';} } ?>" name="image"><?php if (isset($postImage)) { echo $postImage;} ?></textarea>
        <input type="hidden" name="is_image" value="is_image">
        <br>
        <?php endif;?>
        
        <?php if ($type == 'is_quote'):?>
        Featured Quote <span class="required">*</span>
        <br>
        <textarea rows="3" cols="20" class="text <?php if (isset($postQuote)) { if (empty($postQuote)) { echo 'error';} } ?>" name="quote"><?php if (isset($postQuote)) { echo $postQuote;} ?></textarea>
        <input type="hidden" name="is_quote" value="is_quote">
        <br>
        <?php endif;?>
        
        <?php if ($type == 'is_link'):?>
        Featured Link <span class="required">*</span>
        <br>
        <textarea rows="3" cols="20" class="text <?php if (isset($postLink)) { if (empty($postLink)) { echo 'error';} } ?>" name="link"><?php if (isset($postLink)) { echo $postLink;} ?></textarea>
        <input type="hidden" name="is_link" value="is_link">
        <br>
        <?php endif;?>
        
        <?php if ($type == 'is_post'):?>
        <input type="hidden" name="is_post" value="is_post">
        <?php endif;?>
        <div id="wmd-button-bar" class="wmd-button-bar"></div>
        <textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) { if (empty($postContent)) { echo 'error'; } } ?>" name="content" cols="20" rows="10"><?php if (isset($postContent)) { echo $postContent;} ?></textarea>
        <br/>
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
        <input type="submit" name="publish" class="submit" value="Publish"/> <input type="submit" name="draft" class="draft" value="Save as draft"/>
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

<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
<!-- Declare the base path. Important -->
<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/editor.js"></script>