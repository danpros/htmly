<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
 
$CSRF = get_csrf();

echo '<h2>'.i18n('health_check').'</h2><hr>';

echo '<h3>'.i18n('php_check').'</h3>';

$requiredPhpVersion = '7.2';
if (version_compare(PHP_VERSION, $requiredPhpVersion, '>=')) {
    echo '<p> ✅ '.i18n('php_version_check_passed').' (Current: '.PHP_VERSION.', Required: '.$requiredPhpVersion.')</p>';
} else {
    echo '<p> ❌ '.i18n('php_version_check_failed').' (Current: '.PHP_VERSION.', Required: '.$requiredPhpVersion.')</p>';
}

echo '<h3>'.i18n('directory_permissions').'</h3>';

$cachedir = 'cache/';
if (!is_writable($cachedir)) {
    echo '<p> ❌ '.i18n('cache_folder_not_writable').'</p>';
} else {
    echo '<p> ✅ '.i18n('cache_folder_writable').'</p>';
}

$contentdir = 'content/';
if (!is_writable($contentdir)) {
    echo '<p> ❌ '.i18n('content_folder_not_writable').'</p>';
} else {
    echo '<p> ✅ '.i18n('content_folder_writable').'</p>';
}

$usersdir = 'config/users/';
if (!is_writable($usersdir)) {
    echo '<p> ❌ '.i18n('users_folder_not_writable').'</p>';
} else {
    echo '<p> ✅ '.i18n('users_folder_writable').'</p>';
}

echo '<h3>'.i18n('php_modules').'</h3>';

$requiredChecks = array(
    'json' => extension_loaded('json'),
    'mbstring' => extension_loaded('mbstring'),
    'libxml' => extension_loaded('libxml'),
    'dom' => extension_loaded('dom') && class_exists('DOMDocument'),
    'simplexml' => extension_loaded('simplexml') && class_exists('SimpleXMLElement'),
    'xml' => extension_loaded('xml'),
    'hash' => extension_loaded('hash'),
    'session' => extension_loaded('session'),
    'pcre' => extension_loaded('pcre'),
    'filter' => extension_loaded('filter'),
    'ctype' => extension_loaded('ctype'),
    'openssl' => extension_loaded('openssl'),
    'zip' => extension_loaded('zip') && class_exists('ZipArchive'),
    'gd' => extension_loaded('gd') && function_exists('gd_info'),
    'iconv' => extension_loaded('iconv') && function_exists('iconv'),
    'intl' => extension_loaded('intl'),
    'apcu' => extension_loaded('apcu'),
    'mcrypt' => extension_loaded('mcrypt'),
);

foreach ($requiredChecks as $label => $ok) {
    if ($ok) {
        echo '<p> ✅ '.$label.'</p>';
    } else {
        echo '<p> ❌ '.$label.'</p>';
    }
}

