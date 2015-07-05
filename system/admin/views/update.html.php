<?php
 
use \Kanti\HubUpdater;

$CSRF = get_csrf();
        
$updater = new HubUpdater(array(
    'name' => 'danpros/htmly',
    'prerelease' => !!config("prerelease"),
));
    
if ($updater->able()) {
    $info = $updater->getNewestInfo();
    echo '<h3>Update Available</h3>';
    echo '<p><a href="' . $base . 'admin/update/now/' . $CSRF . '" alt="' . $info['name'] . '">Update to ' . $info['tag_name'] . '</a></p>';
} else {
    echo '<h3>No Available Update</h3>';
    echo '<p>You are using the latest HTMLy version.</p>';	
}