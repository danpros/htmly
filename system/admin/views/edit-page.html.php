<?php

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
    $destination = $_GET['destination'];
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

?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css"/>
<script src="<?php echo site_url() ?>system/resources/js/jquery.min.js"></script> 
<script src="<?php echo site_url() ?>system/resources/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<link rel="stylesheet" href="<?php echo site_url() ?>system/resources/css/jquery-ui.css">
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/jquery.ajaxfileupload.js"></script>
<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>
<div class="wmd-panel">
    <form method="POST">
        Title <span class="required">*</span><br><input type="text" name="title"
                                                        class="text <?php if (isset($postTitle)) {
                                                            if (empty($postTitle)) {
                                                                echo 'error';
                                                            }
                                                        } ?>" value="<?php echo $oldtitle ?>"/><br><br>
        Url (optional)<br><input type="text" name="url" class="text" value="<?php echo $oldmd ?>"/><br>
        <span class="help">If the url leave empty we will use the page title.</span><br><br>
        Meta Description (optional)<br><textarea name="description" maxlength="200"><?php if (isset($p->description)) {
                echo $p->description;
            } else {echo $olddescription;}?></textarea>
        <br><br>
        <div id="wmd-button-bar" class="wmd-button-bar"></div>
        <textarea id="wmd-input" class="wmd-input <?php if (isset($postContent)) {
            if (empty($postContent)) {
                echo 'error';
            }
        } ?>" name="content" cols="20" rows="10"><?php echo $oldcontent ?></textarea><br>
        <input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
        <input type="submit" name="submit" class="submit" value="Save"/> <a href="<?php echo $delete ?>">Delete</a>
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