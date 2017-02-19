<?php 

$type = $type;

if ($type != 'is_post' && $type != 'is_image' && $type != 'is_video' && $type != 'is_audio' && $type != 'is_link' && $type != 'is_quote') {
    $add = site_url() . 'admin/content';
    header("location: $add");    
}

$desc = get_category_info(null);

?>
<?php include('partials/post-type-navi.html.php'); ?>

<?php include('partials/input-fields.html.php'); ?>