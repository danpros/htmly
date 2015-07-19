<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css"/>
<?php if (config("jquery") != "enable"):?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script> 
<?php endif;?> 
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/jquery.ajaxfileupload.js"></script>
<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>
<div class="wmd-panel">
    <form method="POST">
        Title <span class="required">*</span> <br><input type="text" class="text <?php if (isset($postTitle)) {
            if (empty($postTitle)) {
                echo 'error';
            }
        } ?>" name="title" value="<?php if (isset($postTitle)) {
            echo $postTitle;
        } ?>"/><br><br>
        Tag <span class="required">*</span> <br><input type="text" class="text <?php if (isset($postTag)) {
            if (empty($postTag)) {
                echo 'error';
            }
        } ?>" name="tag" value="<?php if (isset($postTag)) {
            echo $postTag;
        } ?>"/><br><br>
        Url (optional)<br><input type="text" class="text" name="url" value="<?php if (isset($postUrl)) {
            echo $postUrl;
        } ?>"/><br>
        <span class="help">If the url leave empty we will use the post title.</span><br><br>
        Meta Description (optional)<br><textarea name="description" maxlength="200"><?php if (isset($p->description)) {
                echo $p->description;
            } ?></textarea>
        <br><br>
        Featured Audio <span class="required">*</span> (SoundCloud Only)<br><textarea maxlength="200" class="text <?php if (isset($postAudio)) {
            if (empty($postAudio)) {
                echo 'error';
            }
        } ?>" name="audio"><?php if (isset($postAudio)) {
            echo $postAudio;
        } ?></textarea><br><br>
        <div id="wmd-button-bar" class="wmd-button-bar"></div>
        <textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) {
            if (empty($postContent)) {
                echo 'error';
            }
        } ?>" name="content" cols="20" rows="10"><?php if (isset($postContent)) {
                echo $postContent;
            } ?></textarea><br/>
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
        <input type="submit" name="publish" class="submit" value="Publish"/> <input type="submit" name="draft" class="draft" value="Save as draft"/>
    </form>
</div>
<div id="insertImageDialog" title="Insert Image">
    <h4>URL</h4>
    <input type="text" placeholder="Enter image URL" />
    <h4>Upload</h4>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="file" name="file" id="file" />
    </form>
<style>
#insertImageDialog { display:none; padding: 10px; font-size:12px;}
.wmd-prompt-background {z-index:10!important;}
</style>
</div>
<div id="wmd-preview" class="wmd-panel wmd-preview"></div>
<script type="text/javascript">
    (function () {
        var converter = new Markdown.Converter();
        var editor = new Markdown.Editor(converter);
		
        var $dialog = $('#insertImageDialog').dialog({ 
            autoOpen: false,
            closeOnEscape: false,
            open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
        });
		
        var $url = $('input[type=text]', $dialog);
        var $file = $('input[type=file]', $dialog);
        var base = '<?php echo site_url() ?>';
		
        editor.hooks.set('insertImageDialog', function(callback) {
		
            var dialogInsertClick = function() {                                      
                callback($url.val().length > 0 ? $url.val(): null);
                dialogClose();
            };

            var dialogCancelClick = function() {
                dialogClose();
                callback(null);
            };

            var dialogClose = function() {
                $url.val('');
                $file.val('');
                $dialog.dialog('close');
            };

            $dialog.dialog( 'option', 'buttons', { 
                'Insert': dialogInsertClick, 
                'Cancel': dialogCancelClick 
            });

            var uploadComplete = function(response) {
                if (response.error == '0') {
                    $url.val(base + response.path);
                } else {
                    alert(response.error);
                    $file.val('');
                }
            };
			
            $file.ajaxfileupload({
                'action': '<?php echo site_url() ?>upload.php',
                'onComplete': uploadComplete,
            });
			
            $dialog.dialog('open');

            return true; // tell the editor that we'll take care of getting the image url
        });

        editor.run();
	
    })();
</script>