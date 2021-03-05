<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php echo '<h2>' . i18n('Static_pages') . '</h2>';?>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>add/page">Add new page</a>
<br><br>
<?php get_user_pages(); ?>