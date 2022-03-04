<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<section class="inpage post section">
    <div class="section-inner">
        <div class="content">
            <div class="item">
                <h1 class="title"><?php echo i18n('Search_results_not_found') ?></h1>
                <p> <?php echo i18n('No_search_results') ?></p>
                <div class="search-404">
                    <?php echo search() ?>
                </div>
            </div>
        </div>
    </div>
</section>
