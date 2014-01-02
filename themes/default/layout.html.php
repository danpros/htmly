<!DOCTYPE html>
<html>
<head>
	<title><?php echo isset($title) ? _h($title) : config('blog.title') ?></title>
	<link href='<?php echo site_url() ?>favicon.ico' rel='icon' type='image/x-icon'/>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no" />
	<meta name="description" content="<?php echo $description; ?>" />
	<link rel="canonical" href="<?php echo $canonical; ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo config('blog.title')?> Feed" href="<?php echo site_url()?>feed/rss" />
	<link href="<?php echo site_url() ?>themes/default/css/style.css" rel="stylesheet" />
	<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700&subset=latin,cyrillic-ext" rel="stylesheet" />
	<?php if (publisher() == true):?><link href="<?php echo publisher() ?>" rel="publisher" /><?php endif;?>
	
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<?php if (analytics() == true):?><script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', '<?php echo config('google.analytics.js')?>');
ga('send', 'pageview');</script><?php endif;?>	
</head>
<body class="<?php echo $bodyclass; ?>">
	<div itemscope="itemscope" itemtype="http://schema.org/Blog" class="hide">
		<meta content="<?php echo config('blog.title') ?>" itemprop="name"/>
		<meta content="<?php echo config('blog.description')?>" itemprop="description"/>
	</div>
	<aside>
		<h1 class="blog-title"><a href="<?php echo site_url() ?>"><?php echo config('blog.title') ?></a></h1>
		<div class="description"><p><?php echo config('blog.description')?></p></div>
		<div class="search">
			<form id="search-form" method="get">
				<input type="text" class="search-input" name="keyword" value="Search..." onfocus="if (this.value == 'Search...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search...';}">
				<input type="submit" value="Search" class="search-button">
			</form>
			<?php if(isset($_GET['keyword'])) {$url = site_url() . 'search/' . $_GET['keyword']; header ("Location: $url");} ?>
		</div>
        <div class="social"><?php echo social() ?></div>
		<?php if (menu() == true):?><div class="menu"><?php echo menu() ?></div><?php endif;?>
		<div class="archive"><?php echo archive_list()?></div>
		<div class="copyright"><?php echo copyright() ?></div>
	</aside>
	<section id="content">
		<?php echo content()?>
	</section>
</body>
</html>
