<?php
 
use \Kanti\HubUpdater;

$CSRF = get_csrf();
        
$updater = new HubUpdater(array(
    'name' => 'danpros/htmly',
    'prerelease' => !!config("prerelease"),
));
    
if ($updater->able()) {
    $info = $updater->getNewestInfo();
    echo '<h2>Update Available</h2>';
    echo '<p>Release Title: <strong>'. $info['name'] .'</strong></p>';
    echo '<p>Version: <strong>'. $info['tag_name'] .'</strong></p>';
    echo '<h4>Release Notes</h4>';
    echo '<div style="background-color:#f9f9f9;border:1px solid #ccc;border-radius:4px;color:#333;display:block;font-size:13px;margin:20px 0;padding:0 1em;">';
    echo \Michelf\MarkdownExtra::defaultTransform($info['body']);
    echo '</div>';
    echo '<p><strong>Important:</strong> Please always backup your files before upgrading to newer version.</p>';
    echo '<p><a href="' . site_url() . 'admin/update/now/' . $CSRF . '" alt="' . $info['name'] . '">Update to ' . $info['tag_name'] . ' now</a></p>';
} else {
    echo '<h2>Congrats! You have the latest version of HTMLy</h2>';
    $info = $updater->getCurrentInfo();
    echo '<p>Release Title: <strong>'. $info['name'] .'</strong></p>';
    echo '<p>Installed Version: <strong>'. $info['tag_name'] .'</strong></p>';
    echo '<h4>Release Notes: </h4>';
    echo '<div style="background-color:#f9f9f9;border:1px solid #ccc;border-radius:4px;color:#333;display:block;font-size:13px;margin:20px 0;padding:0 1em;">';
    echo \Michelf\MarkdownExtra::defaultTransform($info['body']);
    echo '</div>';
    echo '<p>Read on <a target="_blank" href="' . $info['html_url'] . '">Github</a>.</p>';
}