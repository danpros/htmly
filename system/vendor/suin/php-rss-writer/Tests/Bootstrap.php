<?php

// For composer
require_once 'Vendor/autoload.php';

// Load test target classes
spl_autoload_register(function($c) { @include_once strtr($c, '\\_', '//').'.php'; });
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__).'/Source');