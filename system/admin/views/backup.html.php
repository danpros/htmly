<?php
if (login()) {
    if (isset($_GET['file'])) {
        $file = _h($_GET['file']);

        if (!empty($file)) {
            unlink("backup/$file");
        }

    }
}
?>
    <h2>Your backups</h2>
	<br>
    <a class="btn btn-primary" href="<?php echo site_url() ?>admin/backup-start">Create backup</a>
	<br><br>
<?php echo get_backup_files() ?>