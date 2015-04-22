<?php
ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__) . "\index.php";
chdir(dirname(__DIR__));

require_once __DIR__ . "/../vendor/autoload.php";