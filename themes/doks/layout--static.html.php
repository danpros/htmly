<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>">
<head>
    <?php echo head_contents();?>
    <?php echo $metatags;?>
    <link rel="preload" as="font" href="<?php echo theme_path();?>fonts/jost/jost-v4-latin-regular.woff2" type="font/woff2" crossorigin>
    <link rel="preload" as="font" href="<?php echo theme_path();?>fonts/jost/jost-v4-latin-700.woff2" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?php echo theme_path();?>css/style.css">
    <meta name="theme-color" content="#fff">
</head>
<body class="docs single" <?php if (isset($is_page)):?>onload="htmlTableOfContents('.page-<?php echo $static->slug;?>');"<?php endif;?> <?php if (isset($is_subpage)):?>onload="htmlTableOfContents('.subpage-<?php echo $static->slug;?>');"<?php endif;?>>
<div class="header-bar fixed-top"></div>
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>
<?php if (login()):?>
<script>
function updateContentPosition() {
    const toolbarHeight = document.querySelector("#toolbar").offsetHeight; // Calculate #toolbar height
	const contentDiv = document.querySelector("#content");
	contentDiv.style.paddingTop = `${toolbarHeight}px`;
}

// Run once when the page loads
window.addEventListener("DOMContentLoaded", updateContentPosition);

// Update on window resize
window.addEventListener("resize", updateContentPosition);
</script>
<?php endif;?>
<header class="navbar fixed-top navbar-expand-md navbar-light">
<?php $filename = "content/data/menu.json";
if (file_exists($filename)) {
    $json = json_decode(file_get_contents('content/data/menu.json', true));
    $menus = json_decode($json);
} ?>
    <div class="container">
        <input class="menu-btn order-0" type="checkbox" id="menu-btn">
        <?php if (!empty($menus)):?>
        <label class="menu-icon d-md-none" for="menu-btn"><span class="navicon"></span></label>
        <?php endif;?>
        <a class="navbar-brand order-1 order-md-0 me-auto" href="<?php echo site_url();?>">
            <?php echo blog_title();?>
        </a>
        <button id="mode" class="btn btn-link order-2 order-md-4" type="button" aria-label="Toggle mode">
            <span class="toggle-dark"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg></span>
            <span class="toggle-light"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg></span>
        </button>
        <nav class="navbar-nav social-nav order-3 order-md-5">
            <?php echo social();?>
        </nav>
        <div class="collapse navbar-collapse order-4 order-md-1 top-menu">
            <?php if (!empty($menus)) { echo menu();}?>
        </div>
    </div>

</header>

<div class="wrap container" role="document">
    <div id="content" class="content">
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