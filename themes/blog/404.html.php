<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<section class="inpage post section">
    <div class="section-inner">
        <div class="content">
            <div class="item">
                <h1 class="title">This page doesn't exist!</h1>
                <p>Please search to find what you're looking for or visit our <a href="<?php echo site_url() ?>">homepage</a> instead.</p>
                <?php echo search() ?>
            </div>
        </div>
    </div>
</section>