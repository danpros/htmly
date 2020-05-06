<?php
if (isset($_GET['destination'])) {
    $destination = _h($_GET['destination']);
}
$url = $p->file;
$oldurl = explode('_', $url);
$oldtag = $oldurl[1];
$oldmd = str_replace('.md', '', $oldurl[2]);

$replaced = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/';
$dt = str_replace($replaced, '', $oldurl[0]);
$t = str_replace('-', '', $dt);
$time = new DateTime($t);
$timestamp = $time->format("Y-m-d");
// The post date
$postdate = strtotime($timestamp);
// The post URL
$post = site_url() . date('Y/m', $postdate) . '/' . $oldmd;

if (isset($destination)) {

    if ($destination == 'post') {
        $back = $post;
    } else {
        $back = site_url() . $destination;
    }
} else {
    $back = site_url();
}

?>
<p><?php echo sprintf(i18n('Are_you_sure_you_want_to_delete_'), $p->title);?></p>
<form method="POST">
    <input type="hidden" name="file" value="<?php echo $p->file ?>"/><br>
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
    <input type="submit" name="submit" value="<?php echo i18n('Delete');?>"/>
    <span><a href="<?php echo $back . '">' . i18n('Cancel');?></a></span>
</form>