<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>">
<head>
    <?php echo head_contents() ?>
    <?php echo $metatags;?>
    <link href="<?php echo theme_path() ?>css/style.css?v=1" rel="stylesheet"/>
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
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
<div id="cover">
    <div id="header-wrapper">
        <header id="header" class="responsive">
            <div id="branding">
                <?php if (is_index()) { ?>
                    <h1 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h1>
                <?php } else { ?>
                    <h2 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h2>
                <?php } ?>
                <div class="blog-tagline"><p><?php echo blog_tagline() ?></p></div>
            </div>
        </header>
    </div>
    <div id="menu-wrapper">
        <nav id="menu" class="responsive">
            <?php echo menu() ?>
            <?php echo search() ?>
        </nav>
    </div>
    <div id="main-wrapper">
        <div id="main" class="responsive">
            <section id="content">
                <?php echo content() ?>
            </section>
            <aside id="sidebar">
                <div class="about">
                    <h3><?php echo i18n('About');?></h3>
                    <p><?php echo blog_description() ?></p>
                </div>
                <div class="social">
                    <h3><?php echo i18n('Follow');?></h3>
                    <?php echo social() ?>
                </div>
                <div class="recent">
                    <h3><?php echo i18n('Recent_posts');?></h3>
                    <?php echo recent_posts() ?>
                </div>
                <?php if(config('views.counter') === 'true') :?>
                <div class="popular">
                    <h3><?php echo i18n('Popular_posts');?></h3>
                    <?php echo popular_posts() ?>
                </div>
                <?php endif;?>
                <div class="archive">
                    <h3><?php echo i18n('Archives');?></h3>
                    <?php echo archive_list() ?>
                </div>
                <?php if (disqus()): ?>
                    <div class="comments">
                        <h3><?php echo i18n('Comments');?></h3>                    
                        <?php echo recent_comments() ?>
                        <style>li.dsq-widget-item {border-bottom: 1px solid #ebebeb;margin:0;margin-bottom:10px;padding:0;padding-bottom:10px;}a.dsq-widget-user {font-weight:normal;}img.dsq-widget-avatar {margin-right:10px; }.dsq-widget-comment {display:block;padding-top:5px;}.dsq-widget-comment p {display:block;margin:0;}p.dsq-widget-meta {padding-top:5px;margin:0;}#dsq-combo-widget.grey #dsq-combo-content .dsq-combo-box {background: transparent;}#dsq-combo-widget.grey #dsq-combo-tabs li {background: none repeat scroll 0 0 #DDDDDD;}</style>
                    </div>
                <?php endif; ?>
                <div class="category-list">
                    <h3><?php echo i18n('Category');?></h3>
                    <?php echo category_list() ?>
                </div>
                <div class="tagcloud">
                    <h3><?php echo i18n('Tags');?></h3>
					<div class="tag-cloud">
						<?php echo tag_cloud();?>
					</div>
                </div>
            </aside>
        </div>
    </div>
    <div id="copyright-wrapper">
        <footer id="copyright" class="responsive">
            <?php echo copyright() ?>
        </footer>
    </div>
</div>
<?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>