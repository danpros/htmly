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
    echo '<h3>'. $info['name'] .'</h3>';
    echo '<p>Version: '. $info['tag_name'] .'</p>';
    echo '<h4>Release Notes</h4>';
    echo '<pre><code>'. $info['body'] .'</code></pre>';
    echo '<p><strong>Important:</strong> Please always backup your files before upgrading to newer version.</p>';
    echo '<p><a href="' . site_url() . 'admin/update/now/' . $CSRF . '" alt="' . $info['name'] . '">Update to ' . $info['tag_name'] . ' now</a></p>';
} else {
    echo '<h3>No Available Update</h3>';
    echo '<p>You are using the latest HTMLy version.</p>';  
}