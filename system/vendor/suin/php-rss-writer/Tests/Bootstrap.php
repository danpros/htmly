<?php

// For composer
require_once __DIR__ . '/../vendor/autoload.php';

// Load test target classes
spl_autoload_register(function ($c) {
  @include_once strtr($c, '\\_', '//') . '.php';
});
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__DIR__) . '/src');

date_default_timezone_set('UTC');
