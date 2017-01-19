<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo head_contents();?>
    <title><?php echo $title;?></title>
    <meta name="description" content="<?php echo $description; ?>"/>
    <link rel="canonical" href="<?php echo $canonical; ?>" />
    <?php if (publisher()): ?>
    <link href="<?php echo publisher() ?>" rel="publisher" /><?php endif; ?>
    <link rel="stylesheet" id="twentyfifteen-fonts-css" href="<?php echo site_url();?>themes/twentyfifteen/css/font.css" type="text/css" media="all">
    <link rel="stylesheet" id="genericons-css" href="<?php echo site_url();?>themes/twentyfifteen/genericons/genericons.css" type="text/css" media="all"> 
    <link rel="stylesheet" id="twentyfifteen-style-css" href="<?php echo site_url();?>themes/twentyfifteen/css/style.css" type="text/css" media="all">
    <!--[if lt IE 9]>
    <link rel='stylesheet' id='twentyfifteen-ie-css'  href='<?php echo site_url();?>themes/twentyfifteen/css/ie.css' type='text/css' media='all' />
    <![endif]-->
    <!--[if lt IE 8]>
    <link rel='stylesheet' id='twentyfifteen-ie7-css'  href='<?php echo site_url();?>themes/twentyfifteen/css/ie7.css' type='text/css' media='all' />
    <![endif]-->
</head>
<?php     
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }
?>
<body class="<?php echo $bodyclass;?>">
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>
    <div id="page" class="hfeed site">
        <div style="top: 0px;" id="sidebar" class="sidebar">
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
                        <h2 class="widget-title">About</h2>
                        <p><?php echo blog_description() ?></p>
                    </aside>                    
                    <nav id="social-navigation" class="social-navigation" role="navigation">
                        <div class="menu-social-links-container">
                            <ul id="menu-social-links" class="menu">
                                <li class="menu-item">
                                    <a href="<?php echo config('social.twitter');?>">
                                    <span class="screen-reader-text">Twitter</span>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="<?php echo config('social.facebook');?>">
                                    <span class="screen-reader-text">Facebook</span>
                                    </a>
                                </li>
                                <li class="menu-item">
                                    <a href="<?php echo config('social.google');?>">
                                    <span class="screen-reader-text">Google</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <aside class="widget search">
                        <form><input type="search" name="search" class="form-control" placeholder="Type to search"></form>
                    </aside>                            
                    <aside class="widget widget_meta">
                        <h2 class="widget-title">Recent Posts</h2>
                        <?php echo recent_posts() ?>
                    </aside>
                    <?php if (config('views.counter') === 'true') :?>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title">Popular Posts</h2>
                        <?php echo popular_posts() ?>
                    </aside>
                    <?php endif;?>
                    <?php if (disqus()): ?>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title">Recent Comments</h2>
                        <script src="//<?php echo config('disqus.shortname');?>.disqus.com/recent_comments_widget.js?num_items=5&amp;hide_avatars=0&amp;avatar_size=48&amp;excerpt_length=200&amp;hide_mods=0" type="text/javascript"></script><style>li.dsq-widget-item {padding-top:15px;} img.dsq-widget-avatar {margin-right:5px;}</style>
                    </aside>
                    <?php endif;?>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title">Archive</h2>
                        <?php echo archive_list() ?>
                    </aside>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title">Category</h2>
                        <?php echo category_list() ?>
                    </aside>
                    <aside class="widget widget_meta">
                        <h2 class="widget-title">Popular Tags</h2>
                            <?php $i = 1; $tags = tag_cloud(true); arsort($tags); ?>
                            <ul>
                            <?php foreach ($tags as $tag => $count):?>
                                <li><a class="more-link" href="<?php echo site_url();?>tag/<?php echo $tag;?>"><?php echo tag_i18n($tag);?> (<?php echo $count;?>)</a></li>
                                <?php if ($i++ >= 5) break;?>
                            <?php endforeach;?>
                            </ul>
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
        </div>
        <footer id="colophon" class="site-footer" role="contentinfo">
            <div class="site-info">
                 <?php echo copyright();?>
            </div>
        </footer>
    </div>
    <script type="text/javascript">
    /* <![CDATA[ */
    var screenReaderText = {"expand":"<span class=\"screen-reader-text\">expand child menu<\/span>","collapse":"<span class=\"screen-reader-text\">collapse child menu<\/span>"};
    /* ]]> */
    </script>
    <!--[if lte IE 8]><script type='text/javascript' src='<?php echo site_url();?>themes/twentyfifteen/js/html5.js'></script><![endif]-->
    <script type="text/javascript" src="<?php echo site_url();?>themes/twentyfifteen/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo site_url();?>themes/twentyfifteen/js/jquery-migrate.js"></script>
    <script type="text/javascript" src="<?php echo site_url();?>themes/twentyfifteen/js/functions.js"></script>
    <script type="text/javascript" src="<?php echo site_url();?>themes/twentyfifteen/js/skip-link-focus-fix.js"></script>
    <?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>
