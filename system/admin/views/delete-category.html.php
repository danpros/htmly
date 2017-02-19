<?php
if (isset($_GET['destination'])) {
    $destination = $_GET['destination'];
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
<?php include('partials/delete.html.php'); ?>