<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
if (isset($_GET['destination'])) {
    $destination = _h($_GET['destination']);
}
$url = $p->file;

$dir = substr($url, 0, strrpos($url, '/'));
$oldurl = str_replace($dir . '/', '', $url);
$oldmd = str_replace('.md', '', $oldurl);

$post = $p->url;

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
    <input type="submit" class="btn btn-danger" name="submit" value="<?php echo i18n('Delete');?>"/>
    <span><a class="btn btn-primary" href="<?php echo $back . '">' . i18n('Cancel');?></a></span>
</form>