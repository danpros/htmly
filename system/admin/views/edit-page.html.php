<?php

if ($type == 'is_frontpage') {
	$filename = 'content/data/frontpage/frontpage.md';

	if (file_exists($filename)) {
		$content = file_get_contents($filename);
		$oldtitle = get_content_tag('t', $content, 'Welcome');
		$oldcontent = remove_html_comments($content);
	} else {
		$oldtitle = 'Welcome';
		$oldcontent = 'Welcome to our website.';
	}
} elseif ($type == 'is_profile') {

	if (isset($_SESSION[config("site.url")]['user'])) {
		$user = $_SESSION[config("site.url")]['user'];
	}

	$filename = 'content/' . $user . '/author.md';

	if (file_exists($filename)) {
		$content = file_get_contents($filename);
		$oldtitle = get_content_tag('t', $content, 'user');
		$oldcontent = remove_html_comments($content);
	} else {
		$oldtitle = $user;
		$oldcontent = 'Just another HTMLy user.';
	}

} else {

	if (isset($p->file)) {
		$url = $p->file;
	} else {
		$url = $oldfile;
	}
	$content = file_get_contents($url);
	$oldtitle = get_content_tag('t', $content, 'Untitled');
	$olddescription = get_content_tag('d', $content);
	$oldcontent = remove_html_comments($content);

	if (isset($_GET['destination'])) {
		$destination = $_GET['destination'];
	} else {
		$destination = 'admin';
	}
	$dir = substr($url, 0, strrpos($url, '/'));
	$oldurl = str_replace($dir . '/', '', $url);
	$oldmd = str_replace('.md', '', $oldurl);

	if (isset($p->url)) {
		$delete = $p->url . '/delete?destination=' . $destination;
	}
	else {
		if(empty($sub)) {
			$delete = site_url() . $oldmd . '/delete?destination=' . $destination;
		}
		else {
			$delete = site_url() . $static .'/'. $sub . '/delete?destination=' . $destination;
		}
	}
}

$isupdate = true;

?>

<div class="creatorMenu">
	<?php if($type == 'is_profile') : ?>
		<h2>Edit profile</h2>
	<?php else : ?>
		<h2>Edit page</h2>
		<a href="<?php echo site_url();?>admin/content">Back to pages</a>
	<?php endif; ?>
</div>

<?php include('partials/input-fields.html.php'); ?>