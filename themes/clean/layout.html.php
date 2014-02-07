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
	<link href="<?php echo site_url() ?>themes/clean/css/style.css" rel="stylesheet" />
	<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700&subset=latin,cyrillic-ext" rel="stylesheet" />
	<?php if (publisher() == true):?><link href="<?php echo publisher() ?>" rel="publisher" /><?php endif;?>
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body class="<?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
	<div class="hide">
		<meta content="<?php echo config('blog.title') ?>" itemprop="name"/>
		<meta content="<?php echo config('blog.description')?>" itemprop="description"/>
	</div>
	<?php if(login()) { toolbar();} ?>
	<aside>
		<h1 class="blog-title"><a href="<?php echo site_url() ?>"><?php echo config('blog.title') ?></a></h1>
		<div class="blog-tagline"><p><?php echo config('blog.tagline')?></p></div>
		<div class="search">
			<?php echo search() ?>
		</div>
        <div class="social"><?php echo social() ?></div>
		<div class="menu"><?php echo menu() ?></div>
		<div class="archive"><?php echo archive_list()?></div>
		<div class="tagcloud"><?php echo tag_cloud()?></div>
		<div class="copyright"><?php echo copyright() ?></div>
	</aside>
	<section id="content">
		<?php echo content()?>
	</section>
	<?php if (analytics() == true):?><?php echo analytics() ?><?php endif;?>
</body>
</html>