<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
 
use \Kanti\HubUpdater;

$CSRF = get_csrf();
        
$updater = new HubUpdater(array(
    'name' => 'danpros/htmly',
    'prerelease' => config("prerelease"),
));

$dir = 'cache/';
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}
if (defined("JSON_PRETTY_PRINT")) {
    file_put_contents(
        'cache/installedVersion.json',
        json_encode(array(
            "tag_name" => constant('HTMLY_VERSION')
        ), JSON_PRETTY_PRINT), LOCK_EX);
} else {
    file_put_contents(
        'cache/installedVersion.json',
        json_encode(array(
            "tag_name" => constant('HTMLY_VERSION')
        )), LOCK_EX
    );
}

if (empty($updater->getNewestInfo())) {
    echo '<h2>'.i18n('Update').'</h2><hr>';
    echo "Can't check Github server for latest version. You are probably offline.";
} else {    
    if ($updater->able()) {
        $info = $updater->getNewestInfo();
        echo '<h2>'.i18n('Update_Available').'</h2><hr>';
        echo '<h4>'. $info['name'] .'</h4>';
        echo '<p>Version: <strong>'. $info['tag_name'] .'</strong></p>';
        echo '<h5>Release Notes</h5>';
        echo '<div>';
        echo \Michelf\MarkdownExtra::defaultTransform($info['body']);
        echo '</div>';
        echo '<p><strong>Important:</strong> Please always backup your files before upgrading to newer version.</p>';
        echo '<p><a class="btn btn-primary" href="' . site_url() . 'admin/update/now/' . $CSRF . '" alt="' . $info['name'] . '">'.i18n('Update_to').' '. $info['tag_name'] . ' '.i18n('now').'</a></p>';
    } else {
        echo '<h2>'.i18n('Congrats_You_have_the_latest_version_of_HTMLy').'</h2><hr>';
        $info = $updater->getCurrentInfo();
        echo '<h4>'. $info['name'] .'</h4>';
        echo '<p>Installed Version: <strong>'. $info['tag_name'] .'</strong></p>';
        echo '<h5>Release Notes: </h5>';
        echo '<div>';
        echo \Michelf\MarkdownExtra::defaultTransform($info['body']);
        echo '</div>';
        echo '<p><a class="btn btn-primary" target="_blank" href="' . $info['html_url'] . '">Read on Github</a></p>';
        if (config('show.version') == 'false') {
            if(file_exists('cache/installedVersion.json')) {
                unlink('cache/installedVersion.json');
            }
        }
    }
}
