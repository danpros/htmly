<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>">
<head>
    <?php echo head_contents();?>
    <title><?php echo $title;?></title>
    <meta name="description" content="<?php echo $description; ?>"/>
    <link rel="canonical" href="<?php echo $canonical; ?>" />
    <link rel="preload" as="font" href="<?php echo theme_path();?>fonts/jost/jost-v4-latin-regular.woff2" type="font/woff2" crossorigin>
    <link rel="preload" as="font" href="<?php echo theme_path();?>fonts/jost/jost-v4-latin-700.woff2" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?php echo theme_path();?>css/style.css">
    <meta name="theme-color" content="#fff">
</head>
<body class="docs single" onload="htmlTableOfContents();">
<div class="header-bar fixed-top"></div>
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>
<header class="navbar fixed-top navbar-expand-md navbar-light">

    <div class="container">
        <input class="menu-btn order-0" type="checkbox" id="menu-btn">
        <label class="menu-icon d-md-none" for="menu-btn"><span class="navicon"></span></label>
        <a class="navbar-brand order-1 order-md-0 me-auto" href="<?php echo site_url();?>">
            <?php echo blog_title();?>
        </a>
        <button id="mode" class="btn btn-link order-2 order-md-4" type="button" aria-label="Toggle mode">
            <span class="toggle-dark"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg></span>
            <span class="toggle-light"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg></span>
        </button>
        <ul class="navbar-nav social-nav order-3 order-md-5">
            <?php if(!empty(config('social.github'))):?>
            <li class="nav-item">
                <a class="nav-link" target="_blank" rel="nofollow" href="<?php echo config('social.github');?>"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-github"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg><span class="ms-2 visually-hidden">GitHub</span></a>
            </li>
            <?php endif;?>
            <?php if(!empty(config('social.twitter'))):?>
            <li class="nav-item">
                <a class="nav-link" target="_blank" rel="nofollow" href="<?php echo config('social.twitter');?>"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-twitter"><path d="M23 3a10.9 10.9.0 01-3.14 1.53 4.48 4.48.0 00-7.86 3v1A10.66 10.66.0 013 4s-4 9 5 13a11.64 11.64.0 01-7 2c9 5 20 0 20-11.5a4.5 4.5.0 00-.08-.83A7.72 7.72.0 0023 3z"></path></svg><span class="ms-2 visually-hidden">Twitter</span></a>
            </li>
            <?php endif;?>
            <?php if(!empty(config('social.facebook'))):?>
            <li class="nav-item">
                <a class="nav-link" target="_blank" rel="nofollow" href="<?php echo config('social.facebook');?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-facebook" viewBox="0 0 18 18"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/></svg><span class="ms-2 visually-hidden">Twitter</span></a>
            </li>
            <?php endif;?>
        </ul>
        <div class="collapse navbar-collapse order-4 order-md-1 top-menu">

            <?php
            // just to make sure only print the content from custom menu 
            $filename = "content/data/menu.json";
            if (file_exists($filename)) {
                $json = json_decode(file_get_contents('content/data/menu.json', true));
                $menus = json_decode($json);
                if (!empty($menus)) {
                    echo menu();        
                }
            } ?>

        </div>
    </div>

</header>

<div class="wrap container" role="document">
    <div class="content">
        <div class="row flex-xl-nowrap">
            <div class="col-lg-5 col-xl-4 docs-bar">
                <input class="menu-btn order-0 float-right" type="checkbox" id="menu-btn2">
                <label class="menu-icon float-right" for="menu-btn2"><span class="navicon"></span></label>
                <div class="docs-sidebar">
                    <nav class="docs-links" aria-label="Main navigation">
                        <?php echo get_menu('list-unstyled collapsible-sidebar', false);?>    
                    </nav>
                </div>
            </div>
            <?php echo content();?>
        </div>
    </div>
</div>

<footer class="footer text-muted">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 order-last order-lg-first">
                <ul class="list-inline">
                    <li class="list-inline-item"><?php echo copyright();?></li>
                </ul>
            </div>
            <div class="col-lg-8 order-last order-lg-last text-lg-end">
                <ul class="list-inline">
                    <li class="list-inline-item">Design by <a href="https://getdoks.org/" target="_blank" rel="nofollow">Doks</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<script src="<?php echo theme_path();?>js/toc.js"></script>
<script src="<?php echo theme_path();?>js/main.js"></script>
<?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>