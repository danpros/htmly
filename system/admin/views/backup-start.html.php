<?php

$timestamp = date('Y-m-d-H-i-s');
$dir = 'backup';

if(is_dir($dir)) {
	Zip('content/', 'backup/content_' . $timestamp . '.zip', true);
}
else {
	mkdir($dir, 0777, true);
	Zip('content/', 'backup/content_' . $timestamp . '.zip', true);
}

$redirect = site_url() . 'admin/backup';
header("Location: $redirect");	
 
 ?>