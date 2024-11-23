<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article>
    <div class="blog-header">
        <h1>Search results not found!</h1>
    </div>
    <div class="content-body">
        <p>Please search again, or would you like to visit our <a href="<?php echo site_url() ?>">homepage</a> instead?</p>
        <?php echo search() ?>
    </div>
</article>