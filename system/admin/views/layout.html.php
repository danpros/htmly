<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title; ?></title>
	<link href='<?php echo site_url() ?>favicon.ico' rel='icon' type='image/x-icon'/>
	<meta charset="utf-8" />
	<meta content='htmly' name='generator'/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no" />
	<meta name="description" content="<?php echo $description; ?>" />
	<link rel="sitemap" href="<?php echo site_url() ?>sitemap.xml" />
	<link rel="canonical" href="<?php echo $canonical; ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo config('blog.title')?> Feed" href="<?php echo site_url()?>feed/rss" />
	<link href="<?php echo site_url() ?>themes/default/css/style.css" rel="stylesheet" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
	<?php if (publisher() == true):?><link href="<?php echo publisher() ?>" rel="publisher" /><?php endif;?>
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body class="admin <?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
	<div class="hide">
		<meta content="<?php echo config('blog.title') ?>" itemprop="name"/>
		<meta content="<?php echo config('blog.description')?>" itemprop="description"/>
	</div>
	<?php if(login()) {?>
		<style>	#outer-wrapper{ padding-top:30px;} @media all and (max-width: 550px) {#outer-wrapper{ padding-top:60px;}}</style>
		<div id="toolbar">						
			<ul>
				<li><a href="<?php echo site_url() ?>">Home</a></li>
				<li><a href="<?php echo site_url() ?>admin">Admin</a></li>
				<li><a href="<?php echo site_url() ?>admin/posts">Posts</a></li>
				<li><a href="<?php echo site_url() ?>add/post">Add post</a></li>
				<li><a href="<?php echo site_url() ?>add/page">Add page</a></li>
				<li><a href="<?php echo site_url() ?>edit/profile">Edit profile</a></li>
				<li><a href="<?php echo site_url() ?>admin/import">Import</a></li>
				<li><a href="<?php echo site_url() ?>logout">Logout</a></li>
			</ul>
		</div>
	<?php } ?>
	<div id="outer-wrapper">
		<div id="menu-wrapper">
			<div class="container">
				<nav id="menu">
					<?php echo menu() ?>
					<?php echo search() ?>
				</nav>
			</div>
		</div>
		<div id="header-wrapper">
			<div class="container">
				<header id="header">
					<section id="branding">
						<h1 class="blog-title"><a href="<?php echo site_url() ?>"><?php echo config('blog.title') ?></a></h1>
						<div class="blog-tagline"><p><?php echo config('blog.tagline')?></p></div>
					</section>
				</header>
			</div>
		</div>
		<div id="content-wrapper">
			<div class="container">
				<section id="content">
					<?php echo content()?>
				</section>
			</div>
		</div>
		<div id="footer-wrapper">
			<div class="container">
				<footer id="footer">
					<div class="copyright"><?php echo copyright() ?></div>
				</footer>
			</div>
		</div>
	</div>
	<?php if (analytics() == true):?><?php echo analytics() ?><?php endif;?>
</body>
</html>