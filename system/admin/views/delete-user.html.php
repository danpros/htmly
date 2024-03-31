<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
$file = 'config/users/' . $username . '.ini';
?>
<p><?php echo sprintf(i18n('Are_you_sure_you_want_to_delete_'), $username);?></p>
<p><small><strong>Note:</strong> This only delete the user and not the contents.</small>
<form method="POST">
    <input type="hidden" name="file" value="<?php echo $file ?>"/><br>
	<input type="hidden" name="username" value="<?php echo $username ?>"/><br>
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
    <input type="submit" class="btn btn-danger" name="submit" value="<?php echo i18n('Delete');?>"/>
    <span><a class="btn btn-primary" href="<?php echo site_url();?>admin/users"><?php echo i18n('Cancel');?></a></span>
</form>