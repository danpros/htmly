<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>">
<head>
    <?php echo head_contents() ?>
    <?php echo $metatags;?>
    <link href="<?php echo theme_path() ?>css/style.css?v=1" rel="stylesheet"/>
    <link href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:700&subset=latin,cyrillic-ext" rel="stylesheet"/>
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body class="<?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
<div class="hide">
    <meta content="<?php echo blog_title() ?>" itemprop="name"/>
    <meta content="<?php echo strip_tags(blog_description()); ?>" itemprop="description"/>
</div>
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>
<aside>
    <?php if (is_index()) { ?>
        <h1 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h1>
    <?php } else { ?>
        <h2 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h2>
    <?php } ?>
    <div class="search">
        <?php echo search() ?>
    </div>
    <div class="social"><?php echo social() ?></div>
    <div class="menu"><?php echo menu() ?></div>
    <div class="recent"><h3><?php echo i18n('Recent_posts');?></h3><?php echo recent_posts() ?></div>
    <div class="archive"><h3><?php echo i18n('Archives');?></h3><?php echo archive_list() ?></div>
    <div class="category-list"><h3><?php echo i18n('Category');?></h3><?php echo category_list() ?></div>
    <div class="tagcloud">
        <h3><?php echo i18n('Tags');?></h3>
        <div class="tag-cloud">
            <?php echo tag_cloud();?>
        </div>			
    </div>
    <div class="copyright"><?php echo copyright() ?></div>
</aside>
<section id="content">
    <?php echo content() ?>
</section>
<?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>