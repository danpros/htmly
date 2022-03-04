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
    <h2><?php echo i18n('Your_backups');?></h2>
	<br>
    <a class="btn btn-primary" href="<?php echo site_url() ?>admin/backup-start"><?php echo i18n('Create_backup');?></a>
	<br><br>
<?php echo get_backup_files() ?>