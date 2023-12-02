<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
if (login()) {
    if (isset($_GET['file'])) {
        $file = _h($_GET['file']);
        $file = preg_replace('/\/|\\\\/','0',$file);
        if (!empty($file)) {
            unlink("backup/$file");
        }

    }
}
?>
<?php if (!extension_loaded('zip')) { ?>
<div class="callout callout-info">
<h5><i class="fa fa-info"></i> Note:</h5>
Please install the ZIP extension to use the backup feature.
</div>
<?php } ?>
    <h2><?php echo i18n('Your_backups');?></h2>
	<br>
    <a class="btn btn-primary <?php if (!extension_loaded('zip')) { ?>disabled<?php } ?>" href="<?php echo site_url() ?>admin/backup-start"><?php echo i18n('Create_backup');?></a>
	<br><br>
<?php echo get_backup_files() ?>