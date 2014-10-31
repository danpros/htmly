<?php
require 'vendor/autoload.php';

$updater = new \Kanti\HubUpdater('kanti/test');
$updater->update();