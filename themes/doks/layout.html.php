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
<body class="blog list">
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

<?php if(config('static.frontpage') == 'true' && isset($is_front)) {
$pages = find_page();
$front = get_frontpage(); ?>

<div class="wrap container" role="document">
    <div class="content">
        <section class="section container-fluid mt-n3 pb-3">
            <div class="row justify-content-center">
                <div class="col-lg-12 text-center">
                    <h1 class="mt-0"><?php echo $front->title;?></h1>
                    <?php if(login()):?><small><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828.0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg> <a href="<?php echo $front->url;?>/edit?destination=front"><?php echo i18n('edit');?></a></small><?php endif;?>
                </div>
                <div class="col-lg-9 col-xl-8 text-center">
                    <div class="lead"><?php echo $front->body;?></div>
                    <?php if (!empty($pages[0])):?><a class="btn btn-primary btn-lg px-4 mb-2" href="<?php echo $pages[0]->url;?>" role="button">Get Started</a><?php endif;?>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="d-flex justify-content-start"><div class="bg-dots"></div></div>

<section class="section section-sm">
    <div class="container">
        <div class="row justify-content-center text-center">
            <?php foreach ($pages as $pg):?>
            <div class="col-lg-5">
                <h2 class="h4"><a href="<?php echo $pg->url;?>"><?php echo $pg->title;?></a></h2>
                <p><?php echo $pg->description;?></p>
            </div>
            <?php endforeach;?>
        </div>
    </div>
</section>

<?php } else {?>
<div class="wrap container" role="document">
    <div id="content" class="content">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-10 col-xl-9">
            <?php echo content();?>
            </div>
        </div>
    </div>
</div>
<?php }?>

<section class="section section-sm container-fluid"><div class="row justify-content-center text-center"><div class="col-lg-9"></div></div></section>

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

<script src="<?php echo theme_path();?>js/main.js"></script>
<?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>