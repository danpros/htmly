<?php
 
use \Kanti\HubUpdater;

$CSRF = get_csrf();
        
$updater = new HubUpdater(array(
    'name' => 'danpros/htmly',
    'prerelease' => !!config("prerelease"),
));
if ($updater->able()){
	$info 	= $updater->getNewestInfo(); 
	$title 	= 'Update Available';
	$info	= '<p><strong>Important:</strong> Please always backup your files before upgrading to newer version.</p>';
    $info  .= '<p><a href="' . site_url() . 'admin/update/now/' . $CSRF . '" alt="' . $info['name'] . '">Update to ' . $info['tag_name'] . ' now</a></p>';
} else{
	$info 	= $updater->getCurrentInfo(); 
	$title 	= 'Congrats! You have the latest version of HTMLy';
	$note	= '<p>Read on <a target="_blank" href="' . $info['html_url'] . '">Github</a>.</p>';
}
?>

<div class="content-list">
	<h2><?php echo $title; ?></h2>
	<p>Release Title: <strong><?php echo $info['name']; ?></strong></p>
	<p>Version: <strong><?php echo  $info['tag_name']; ?></strong></p>
	<h4>Release Notes</h4>
	<div style="background-color:#f9f9f9;border:1px solid #ccc;border-radius:4px;color:#333;display:block;font-size:13px;margin:20px 0;padding:0 1em;">
		<?php echo \Michelf\MarkdownExtra::defaultTransform($info['body']); ?>
	</div>
	<?php echo $note; ?>
</div>