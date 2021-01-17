<!DOCTYPE html>
<html>
<head>
    <?php echo head_contents() ?>
    <title><?php echo $title;?></title>
    <meta name="description" content="<?php echo $description; ?>"/>
    <?php if($canonical): ?>
        <link rel="canonical" href="<?php echo $canonical; ?>" />
    <?php endif; ?>
    <link href="<?php echo site_url() ?>system/resources/css/admin.css" rel="stylesheet"/>
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
    <?php if (publisher()): ?>
        <link href="<?php echo publisher() ?>" rel="publisher" /><?php endif; ?>
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body class="admin <?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
<div class="hide">
    <meta content="<?php echo blog_title() ?>" itemprop="name"/>
    <meta content="<?php echo blog_description() ?>" itemprop="description"/>
</div>
<?php if (login()) {
    toolbar();
} ?>
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
                    <h1 class="blog-title"><a href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h1>
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