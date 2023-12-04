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
<?php 

if (isset($_SESSION[config("site.url")]['user'])) {
	$files = get_zip_files();
	if (!empty($files)) {
		krsort($files);
		echo '<table class="table backup-list">';
		echo '<tr class="head"><th>' . i18n('Filename') . '</th><th>'.i18n('Date').'</th><th>' . i18n('Operations') . '</th></tr>';
		$i = 0;
		$len = count($files);
		foreach ($files as $file) {

			if ($i == 0) {
				$class = 'item first';
			} elseif ($i == $len - 1) {
				$class = 'item last';
			} else {
				$class = 'item';
			}
			$i++;

			// Extract the date
			$arr = explode('_', $file);

			// Replaced string
			$replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

			$name = str_replace($replaced, '', $file);

			$date = str_replace('.zip', '', $arr[1]);
			$t = str_replace('-', '', $date);
			$time = new DateTime($t);
			$timestamp = $time->format("D, d F Y, H:i:s");

			$url = site_url() . $file;
			echo '<tr class="' . $class . '">';
			echo '<td>' . $name . '</td>';
			echo '<td>' . $timestamp . '</td>';
			echo '<td><a class="btn btn-primary btn-xs" target="_blank" href="' . $url . '">Download</a> <form method="GET"><input type="hidden" name="file" value="' . $name . '"/><input type="submit" class="btn btn-danger btn-xs" name="submit" value="Delete"/></form></td>';
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo i18n('No_available_backup');
	}
}

 ?>