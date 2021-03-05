<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="page type-page hentry">
    <header class="entry-header">
        <h1 class="entry-title">This page doesn't exist!</h1>
    </header>
    <div class="entry-content">
        <p>Please search to find what you're looking for or visit our <a href="<?php echo site_url() ?>">homepage</a> instead.</p>
        <?php echo search() ?>
    </div>
</article>