<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>">
<head>
    <?php echo head_contents();?>
    <title><?php echo $title;?></title>
    <meta name="description" content="<?php echo $description; ?>"/>
    <link rel="canonical" href="<?php echo $canonical; ?>" />
</head>
<body>
<div id="progress" style="border:1px solid #ccc;"></div>
<div id="information" style="width:100%"></div>
</body>
</html>
<?php 
$search = $search;
$tmp = array();
foreach ($search as $k => $v) {
    $tmp[] = $v;
}

// Loop through process
for($i = 0, $size = count($tmp); $i < $size; ++$i) {
    
    $content = file_get_contents($tmp[$i][1]);

    add_search_index('post_' . $tmp[$i][0], $content);

    // Calculate the percentage
    $percent = intval($i/$size * 100)."%";

    // Progress bar and information
    echo '<script language="javascript">
    document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("information").innerHTML="File <strong>'.$tmp[$i][1].'</strong> processed.";
    </script>';

    // Buffer
    echo str_repeat(' ',1024*64);

    // Send output to browser
    ob_flush();

    // Sleep one second
    // sleep(1);
}

// The process is completed
echo '<script language="javascript">
 document.getElementById("progress").innerHTML="<div style=\"width:100%;background-color:#ddd;\">&nbsp;</div>";
document.getElementById("information").innerHTML="Process completed";
</script>';

echo '<a href="' . site_url() .'admin/search">'. i18n('back_to') . ' ' . i18n('search_index') .'</a>';

// Redir
echo '<script language="javascript">window.location.href = "' .site_url() . 'admin/search"</script>';

?>