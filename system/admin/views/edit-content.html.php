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

$isupdate = true;

?>


<div class="creatorMenu">
	<h2>Edit post</h2>

	<a href="<?php echo site_url();?>admin">Back to posts</a>
</div>

<?php include('partials/input-fields.html.php'); ?>