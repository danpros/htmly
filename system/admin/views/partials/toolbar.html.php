<?php
    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    $base = site_url();
?>

<div id="toolbar">
	<div class="toolbarInner">
		<ul class="mainTools">
			<li><a href="<?php echo $base ; ?>admin">Posts</a></li>
			<li><a href="<?php echo $base ; ?>admin/content">Pages</a></li>
			<li><a href="<?php echo $base ; ?>admin/draft">Drafts</a></li>
			<?php if (config('input.showCat') == 'true') : ?>	
				<li><a href="<?php echo $base ; ?>admin/categories">Categories</a></li>
			<?php endif; ?>
		</ul>
		<ul class="adminTools">
			<li><a href="<?php echo $base ; ?>">Go to Front</a></li>
			<li class="dropDown"><a href="#">Tools</a>
				<ul class="dropDownItems">
					<li><a href="<?php echo $base; ?>admin/clear-cache">Clear cache</a></li>
					<li><a href="<?php echo $base ; ?>edit/profile">Profile</a></li>
					<li><a href="<?php echo $base ; ?>admin/mine">Mine</a></li>
					<?php if (config('views.counter') == 'true') : ?>
						<li><a href="<?php echo $base ; ?>admin/popular">Popular</a></li>
					<?php endif; ?>
					<?php if ($role == 'admin') : ?>
						<li><a href="<?php echo $base; ?>admin/config">Config</a></li>
						<li><a href="<?php echo $base ; ?>admin/update">Update</a></li>
						<li><a href="<?php echo $base ; ?>admin/import">Import</a></li>
						<li><a href="<?php echo $base ; ?>admin/backup">Backup</a></li>
					<?php endif; ?>
				</ul>
			</li>
			<li><a href="<?php echo $base; ?>logout">Logout</a></li>
		</ul>
	</div>
</div>