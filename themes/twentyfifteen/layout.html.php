<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>">
<head>
    <?php echo head_contents();?>
    <?php echo $metatags;?>
    <link rel="stylesheet" id="twentyfifteen-fonts-css" href="<?php echo theme_path();?>css/font.css" type="text/css" media="all">
    <link rel="stylesheet" id="genericons-css" href="<?php echo theme_path();?>genericons/genericons.css" type="text/css" media="all"> 
    <link rel="stylesheet" id="twentyfifteen-style-css" href="<?php echo theme_path();?>css/style_v2.css?v=1" type="text/css" media="all">
    <!--[if lt IE 9]>
    <link rel='stylesheet' id='twentyfifteen-ie-css'  href='<?php echo theme_path();?>css/ie.css' type='text/css' media='all' />
    <![endif]-->
    <!--[if lt IE 8]>
    <link rel='stylesheet' id='twentyfifteen-ie7-css'  href='<?php echo theme_path();?>css/ie7.css' type='text/css' media='all' />
    <![endif]-->
</head>
<body class="<?php echo $bodyclass;?>">
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>
    <div id="page" class="hfeed site">
        <div id="sidebar" class="sidebar">
            <header id="masthead" class="site-header" role="banner">
                <div class="site-branding">
                    <?php if (isset($is_front)) {?>
                    <h1 class="site-title"><a href="<?php echo site_url();?>" title="<?php echo blog_title();?>"><?php echo blog_title();?></a></h1>
                    <?php } else { ?>
                    <h2 class="site-title"><a href="<?php echo site_url();?>" title="<?php echo blog_title();?>"><?php echo blog_title();?></a></h2>
                    <?php } ?>
                    <p class="site-description"><?php echo blog_tagline() ?></p>
                    <button class="secondary-toggle">Menu and widgets</button>
                </div>
            </header>
            <div id="secondary" class="secondary">
                <div id="widget-area" class="widget-area" role="complementary">
                    <nav id="site-navigation" class="main-navigation">
                        <div class="menu-demo-menu-container">
                            <?php echo menu('nav-menu') ?>
                        </div>
                    </nav>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title"><?php echo i18n("About");?></h2>
                        <p><?php echo blog_description() ?></p>
                    </aside>
                    <nav id="social-navigation" class="social-navigation" role="navigation">
                        <div class="menu-social-links-container">
                            <?php echo social();?>
                        </div>
                    </nav>
                    <aside class="widget search">
                        <form><input type="search" name="search" class="form-control" placeholder="<?php echo i18n('Type_to_search');?>"></form>
                    </aside>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title"><?php echo i18n("Recent_posts");?></h2>
                        <?php echo recent_posts() ?>
                    </aside>
                    <?php if (config('views.counter') === 'true') :?>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title"><?php echo i18n("Popular_posts");?></h2>
                        <?php echo popular_posts() ?>
                    </aside>
                    <?php endif;?>
                    <?php if (disqus()): ?>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title">Recent comments</h2>
                        <script src="//<?php echo config('disqus.shortname');?>.disqus.com/recent_comments_widget.js?num_items=5&amp;hide_avatars=0&amp;avatar_size=48&amp;excerpt_length=200&amp;hide_mods=0" type="text/javascript"></script><style>li.dsq-widget-item {padding-top:15px;} img.dsq-widget-avatar {margin-right:5px;}</style>
                    </aside>
                    <?php endif;?>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title"><?php echo i18n("Archives");?></h2>
                        <?php echo archive_list() ?>
                    </aside>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title"><?php echo i18n('Category');?></h2>
                        <?php echo category_list() ?>
                    </aside>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title"><?php echo i18n("Tags");?></h2>
                        <div class="tag-cloud">
                            <?php echo tag_cloud();?>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
        <div id="content" class="site-content">
            <div id="primary" class="content-area">
                <main id="main" class="site-main" role="main">
                    <?php echo content();?>
                </main>
            </div>
            <footer id="colophon" class="site-footer hentry" role="contentinfo">
                <div class="site-info">
                     <?php echo copyright();?>
                </div>
            </footer>
        </div>
    </div>
    <script type="text/javascript">
    /* <![CDATA[ */
    var screenReaderText = {"expand":"<span class=\"screen-reader-text\">expand child menu<\/span>","collapse":"<span class=\"screen-reader-text\">collapse child menu<\/span>"};
    /* ]]> */
    </script>
    <!--[if lte IE 8]><script type='text/javascript' src='<?php echo theme_path();?>js/html5.js'></script><![endif]-->
    <script type="text/javascript" src="<?php echo theme_path();?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo theme_path();?>js/jquery-migrate.js"></script>
    <script type="text/javascript" src="<?php echo theme_path();?>js/theia-sticky-sidebar.min.js"></script>
    <script type="text/javascript" src="<?php echo theme_path();?>js/functions.js?v=2"></script>
    <script type="text/javascript" src="<?php echo theme_path();?>js/skip-link-focus-fix.js"></script>
    <?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>
