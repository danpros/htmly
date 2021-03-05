<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<div class="post">
    <div class="main">
        <h1 class="title-post">Search results not found!</h1>
        <p>Please search again, or would you like to try our <a href="<?php echo site_url() ?>">homepage</a> instead?</p>
        <div class="search-404">
            <?php echo search() ?>
        </div>
    </div>
</div>
