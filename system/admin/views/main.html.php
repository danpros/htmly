<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php echo '<h2>' . i18n('Your_recent_posts') . '</h2>';?>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>admin/content"><?php echo i18n('Add_content');?></a>
<br><br>
<?php get_user_posts();?>