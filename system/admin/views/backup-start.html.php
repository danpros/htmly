<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php

$title = config('blog.title');
$name = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($title));
$name = str_replace(' ', '-', $name);
$name = str_replace('--', '-', $name);
$name = str_replace('--', '-', $name);
$name = rtrim(ltrim($name, ' \,\.\-'), ' \,\.\-');

$timestamp = date('Y-m-d-H-i-s');
$dir = 'backup';

if (is_dir($dir)) {
    Zip('content/', 'backup/' . $name . '_' . $timestamp . '.zip', true);
} else {
    mkdir($dir, 0775, true);
    Zip('content/', 'backup/' . $name . '_' . $timestamp . '.zip', true);
}

$redirect = site_url() . 'admin/backup';
header("Location: $redirect");

?>