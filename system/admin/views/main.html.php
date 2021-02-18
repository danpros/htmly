<?php echo '<h2>' . i18n('Your_recent_posts') . '</h2>';?>
<br>
<a class="btn btn-primary right" href="<?php echo site_url();?>admin/content">Add new post</a>
<br><br>
<?php get_user_posts();?>