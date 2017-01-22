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
    $destination = $_GET['destination'];
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
<?php $editorClass = NULL; ?>
<h1 class="page-header">Edit Content</h1>
<div class="wmd-panel">
    <form method="POST">
        Category <span class="required">*</span>
        <br>
        <select class="select2" name="category">
            <option value="uncategorized">Uncategorized</option>
            <?php foreach ($desc as $d):?>
                <option value="<?php echo $d->md;?>" <?php if($category === $d->md) { echo 'selected="selected"';} ?>><?php echo $d->title;?></option>
            <?php endforeach;?>
        </select> 
        <br><br>
        Title <span class="required">*</span>
        <br>
        <input type="text" name="title" class="text form-control <?php if (isset($postTitle)) { if (empty($postTitle)) { echo 'error';} } ?>" value="<?php echo $oldtitle ?>"/>
        <br>
        Tag <span class="required">*</span><br>
        <input type="text" name="tag" class="text form-control <?php if (isset($postTag)) { if (empty($postTag)) { echo 'error'; } } ?>" value="<?php echo $oldtag ?>"/>
        <br>
        Url (optional)<br>
        <span class="help"><input type="text" name="url" class="text form-control" value="<?php echo $oldmd ?>"/>
        If the url leave empty we will use the post title.</span>
        <br>
        <br>
        Year, Month, Day<br>
        <input type="date" name="date" class="text form-control" value="<?php echo date('Y-m-d', $postdate); ?>">
        <br>
        Hour, Minute, Second<br>
        <input type="time" name="time" class="text form-control" value="<?php echo $time->format('H:i:s'); ?>">
        <br>
        Meta Description (optional)<br>
        <textarea class="meta-textinput form-control" name="description" rows="3" cols="20"><?php if (isset($p->description)) { echo $p->description; } else { echo $olddescription;} ?></textarea>
        <br>
        
        <?php if ($type == 'is_audio'):?>
        Featured Audio <span class="required">*</span> (SoundCloud Only)
        <br>
        <textarea rows="3" cols="20" class="text media-textinput form-control <?php if (isset($postAudio)) { if (empty($postAudio)) { echo 'error';} } ?>" name="audio"><?php echo $oldaudio; ?></textarea>
        <input type="hidden" name="is_audio" value="is_audio">
        <?php endif;?>
        
        <?php if ($type == 'is_video'):?>
        Featured Video <span class="required">*</span> (Youtube Only)
        <br>
        <textarea rows="3" cols="20" class="text media-textinput form-control <?php if (isset($postVideo)) { if (empty($postVideo)) { echo 'error';} } ?>" name="video"><?php echo $oldvideo ?></textarea>
        <input type="hidden" name="is_video" value="is_video">
        <?php endif;?>
        
        <?php if ($type == 'is_image'):?>
        Featured Image <span class="required">*</span>
        <br>
        <textarea rows="3" cols="20" class="text media-textinput form-control <?php if (isset($postImage)) { if (empty($postImage)) { echo 'error';} } ?>" name="image"><?php echo $oldimage; ?></textarea>
        <input type="hidden" name="is_image" value="is_image">
        <?php endif;?>
        
        <?php if ($type == 'is_quote'):?>
        Featured Quote <span class="required">*</span>
        <br>
        <textarea rows="3" cols="20" class="text media-textinput form-control <?php if (isset($postQuote)) { if (empty($postQuote)) { echo 'error';} } ?>" name="quote"><?php echo $oldquote ?></textarea>
        <input type="hidden" name="is_quote" value="is_quote">
        <?php endif;?>
        
        <?php if ($type == 'is_link'):?>
        Featured Link <span class="required">*</span>
        <br>
        <textarea rows="3" cols="20" class="text media-textinput form-control <?php if (isset($postLink)) { if (empty($postLink)) { echo 'error';} } ?>" name="link"><?php echo $oldlink ?></textarea>
        <input type="hidden" name="is_link" value="is_link">
        <?php endif;?>
        
        <?php if ($type == 'is_post'):?>
        <?php if (isset($postContent)) { if (empty($postContent)) { $editorClass = 'error'; } } ?>
        <input type="hidden" name="is_post" value="is_post">
        <?php endif;?>
        <br>
        <div id="wmd-button-bar" class="wmd-button-bar"></div>
        <textarea id="wmd-input" class="wmd-input <?php echo $editorClass; ?>" name="content" cols="30" rows="10" style="margin-bottom: 10px;"><?php echo $oldcontent ?></textarea>
        <br>
        <input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
        <?php if ($isdraft[4] == 'draft') { ?>
            <input type="submit" name="publishdraft" class="submit btn btn-md btn-primary" value="Publish draft"/> 
            <input type="submit" name="updatedraft" class="draft btn btn-md btn-warning" value="Update draft"/> 
            <a type="button" class="btn btn-md btn-danger" href="<?php echo $delete ?>">Delete</a>
        <?php } else { ?>
            <input type="submit" name="updatepost" class="submit btn btn-md btn-primary" value="Update post"/> 
            <input type="submit" name="revertpost" class="revert btn btn-md btn-warning" value="Revert to draft"/> 
            <a type="button" class="btn btn-md btn-danger pull-right" href="<?php echo $delete ?>">Delete</a>
        <?php }?>
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