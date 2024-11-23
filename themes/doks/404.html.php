<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article>
    <div class="blog-header">
        <h1>This page doesn't exist!</h1>
    </div>
    <div class="content-body">
        <p>Please search to find what you're looking for or visit our <a href="<?php echo site_url() ?>">homepage</a> instead.</p>
        <?php echo search() ?>
    </div>
</article>