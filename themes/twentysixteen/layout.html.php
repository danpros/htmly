<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo head_contents();?>
    <title><?php echo $title;?></title>
    <meta name="description" content="<?php echo $description; ?>"/>
    <link rel="canonical" href="<?php echo $canonical; ?>" />
    <?php if (publisher()): ?><link href="<?php echo publisher() ?>" rel="publisher" /><?php endif; ?> 
    <link rel="stylesheet" id="twentysixteen-fonts-css" href="https://fonts.googleapis.com/css?family=Merriweather%3A400%2C700%2C900%2C400italic%2C700italic%2C900italic%7CMontserrat%3A400%2C700%7CInconsolata%3A400&#038;subset=latin%2Clatin-ext" type="text/css" media="all" />
    <link rel="stylesheet" id="genericons-css"  href="<?php echo site_url();?>themes/twentysixteen/genericons/genericons.css" type="text/css" media="all" />
    <link rel="stylesheet" id="twentysixteen-style-css"  href="<?php echo site_url();?>themes/twentysixteen/css/style.css" type="text/css" media="all" />
    <!--[if lt IE 10]>
    <link rel="stylesheet" id="twentysixteen-ie-css"  href="<?php echo site_url();?>themes/twentysixteen/css/ie.css" type="text/css" media="all" />
    <![endif]-->
    <!--[if lt IE 9]>
    <link rel="stylesheet" id="twentysixteen-ie8-css"  href="<?php echo site_url();?>themes/twentysixteen/css/ie8.css" type="text/css" media="all" />
    <![endif]-->
    <!--[if lt IE 8]>
    <link rel="stylesheet" id="twentysixteen-ie7-css"  href="<?php echo site_url();?>themes/twentysixteen/css/ie7.css" type="text/css" media="all" />
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
    <div id="page" class="site">
        <div class="site-inner">
        
            <a class="skip-link screen-reader-text" href="#content">Skip to content</a>

            <header id="masthead" class="site-header" role="banner">
                <div class="site-header-main">
                    <div class="site-branding">
                        <h1 class="site-title"><a href="<?php echo site_url();?>" rel="home"><?php echo blog_title();?></a></h1>
                        <p class="site-description"><?php echo blog_tagline();?></p>
                    </div><!-- .site-branding -->

                    <button id="menu-toggle" class="menu-toggle">Menu</button>

                    <div id="site-header-menu" class="site-header-menu">
                        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Primary Menu">
                            <div class="menu-main-container">
                                <?php echo menu('primary-menu');?>
                            </div>
                        </nav><!-- .main-navigation -->
                    </div><!-- .site-header-menu -->
                    
                </div><!-- .site-header-main -->
            </header><!-- .site-header -->

            <div id="content" class="site-content">

                <div id="primary" class="content-area">
                    <main id="main" class="site-main" role="main">
                        <?php echo content();?>
                    </main><!-- .site-main -->
                </div><!-- .content-area -->


                <aside id="secondary" class="sidebar widget-area" role="complementary">
                    <section class="widget widget_text">
                        <h2 class="widget-title">About</h2>
                        <div class="textwidget"><p><?php echo blog_description();?></p>
                        </div>
                    </section>
                    
                    <section id="search" class="widget widget_search">
                        <form role="search" class="search-form">
                        <label>
                            <span class="screen-reader-text">Search for:</span>
                            <input type="search" class="search-field" placeholder="Search &hellip;" value="" name="search" title="Search for:" />
                        </label>
                        <button type="submit" class="search-submit"><span class="screen-reader-text">Search</span></button>
                        </form>
                    </section>    
                    
                    <section id="recent-posts" class="widget widget_recent_entries">        
                        <h2 class="widget-title">Recent Posts</h2>
                        <?php echo recent_posts();?>
                    </section>
                    
                    <?php if (config('views.counter') === 'true') :?>
                    <section id="popular-posts" class="widget widget_popular_entries">        
                        <h2 class="widget-title">Popular Posts</h2>
                        <?php echo popular_posts();?>
                    </section>
                    <?php endif;?>

                    <?php if (disqus()): ?>
                    <section id="recent-comments" class="widget widget_recent_comments">
                        <h2 class="widget-title">Recent Comments</h2>
                        <script src="//<?php echo config('disqus.shortname');?>.disqus.com/recent_comments_widget.js?num_items=5&amp;hide_avatars=0&amp;avatar_size=48&amp;excerpt_length=200&amp;hide_mods=0" type="text/javascript"></script><style>li.dsq-widget-item {padding-top:15px;} img.dsq-widget-avatar {margin-right:5px;} .dsq-widget-list {margin-left:0;}</style>
                    </section>
                    <?php endif;?>

                    <section id="archives" class="widget widget_archive">
                    <h2 class="widget-title">Archives</h2>        
                        <?php echo archive_list() ?>
                    </section>
                    
                    <section id="category" class="widget widget_category">
                    <h2 class="widget-title">Category</h2>        
                        <?php echo category_list() ?>
                    </section>
                    
                    <section id="popular-tags" class="widget widget_popular_tags">
                    <h2 class="widget-title">Popular Tags</h2>
                        <?php $i = 1; $tags = tag_cloud(true); arsort($tags); ?>
                        <ul>
                        <?php foreach ($tags as $tag => $count):?>
                            <li><a class="more-link" href="<?php echo site_url();?>tag/<?php echo $tag;?>"><?php echo tag_i18n($tag);?> (<?php echo $count;?>)</a></li>
                        <?php if ($i++ >= 5) break;?>
                        <?php endforeach;?>
                        </ul>
                    </section>
                    
                </aside><!-- .sidebar .widget-area -->

            </div><!-- .site-content -->

            <footer id="colophon" class="site-footer" role="contentinfo">
                <nav class="main-navigation" role="navigation" aria-label="Footer Primary Menu">
                    <div class="menu-main-container">
                        <?php echo menu('primary-menu');?>
                    </div>
                </nav><!-- .main-navigation -->
                <nav aria-label="Footer Social Links Menu" role="navigation" class="social-navigation">
                    <div class="menu-social-links-container">
                    <ul class="social-links-menu" id="menu-social-links">
                        <li><a href="<?php echo config('social.twitter');?>"><span class="screen-reader-text">Twitter</span></a></li>
                        <li><a href="<?php echo config('social.facebook');?>"><span class="screen-reader-text">Facebook</span></a></li>
                        <li><a href="<?php echo config('social.google');?>"><span class="screen-reader-text">Google+</span></a></li>
                        <li><a href="<?php echo config('social.github');?>"><span class="screen-reader-text">GitHub</span></a></li>
                    </ul>
                    </div>                
                </nav>
                <div class="site-info">
                    <span class="site-title"><a href="<?php echo site_url();?>" rel="home"><?php echo blog_title();?></a></span>
                    <span class="copyright"><?php echo copyright();?></span>
                </div><!-- .site-info -->
            </footer><!-- .site-footer -->
        </div><!-- .site-inner -->
    </div><!-- .site -->

    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo site_url();?>themes/twentysixteen/js/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="<?php echo site_url();?>themes/twentysixteen/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo site_url();?>themes/twentysixteen/js/jquery-migrate.js"></script>
    <script type="text/javascript" src="<?php echo site_url();?>themes/twentysixteen/js/skip-link-focus-fix.js"></script>
    <script type="text/javascript">
    /* <![CDATA[ */
    var screenReaderText = {"expand":"expand child menu","collapse":"collapse child menu"};
    /* ]]> */
    </script>
    <script type="text/javascript" src="<?php echo site_url();?>themes/twentysixteen/js/functions.js"></script>
    <?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>