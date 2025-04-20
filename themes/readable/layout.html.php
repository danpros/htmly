<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>">
<head>
    <?php echo head_contents() ?>
    <?php echo $metatags;?>
    <link href="<?php echo theme_path() ?>css/style.css" rel="stylesheet"/>
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
<div id="outer-wrapper">
    <div id="menu-wrapper">
        <div class="container">
            <nav id="menu">
                <?php echo menu() ?>
                <?php echo search() ?>
            </nav>
        </div>
    </div>
    <div id="header-wrapper">
        <div class="container">
            <header id="header">
                <section id="branding">
                    <?php if (is_index()) { ?>
                        <h1 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h1>
                    <?php } else { ?>
                        <h2 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h2>
                    <?php } ?>
                    <div class="blog-tagline"><p><?php echo blog_tagline() ?></p></div>
                </section>
            </header>
        </div>
    </div>
    <div id="content-wrapper">
        <div class="container">
            <section id="content">
                <?php echo content() ?>
            </section>
        </div>
    </div>
    <div id="footer-wrapper">
        <div class="container">
            <footer id="footer">
                <div class="copyright"><?php echo copyright() ?></div>
            </footer>
        </div>
    </div>
</div>
<?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>