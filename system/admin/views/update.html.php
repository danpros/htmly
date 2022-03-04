<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
 
use \Kanti\HubUpdater;

$CSRF = get_csrf();
        
$updater = new HubUpdater(array(
    'name' => 'danpros/htmly',
    'prerelease' => !!config("prerelease"),
));
    
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
}