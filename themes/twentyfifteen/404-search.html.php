<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<article class="page type-page hentry">
    <header class="entry-header">
        <h1 class="entry-title">Search results not found!</h1>
    </header>
    <div class="entry-content">
        <p>Please search again, or would you like to try our <a href="<?php echo site_url() ?>">homepage</a> instead?</p>
        <div class="search-404">
            <?php echo search() ?>
        </div>
    </div>
</article>
