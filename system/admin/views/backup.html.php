<?php
if (login()) {
    if (isset($_GET['file'])) {
        $file = $_GET['file'];

        if (!empty($file)) {
            unlink($file);
        }

    }
}
?>
<div class="creatorMenu">
    <h2>Backup</h2>
	<a href="<?php echo site_url() ?>admin/backup-start">Create a Backup</a>
</div>
<div class="content-list">
    <h2>Your backups</h2>
	<?php echo get_backup_files() ?>
</div>