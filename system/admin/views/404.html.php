<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html>
<head>
    <link href='<?php echo site_url() ?>favicon.ico' rel='icon' type='image/x-icon'/>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no"/>
    <title>404 Not Found - <?php echo blog_title() ?></title>
</head>
<body>
<div class="center message">
    <h1><?php echo i18n('This_page_doesnt_exist');?></h1>

    <p><?php echo i18n('Would_you_like_to_try_our');?> <a href="<?php echo site_url() ?>"><?php echo i18n('homepage');?></a> <?php echo i18n('instead');?> ?</p>
</div>
</body>
</html>