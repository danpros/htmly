<!DOCTYPE html>
<html>
<head>
	<?php echo $head_contents ?>
	<link href="<?php echo site_url() ?>themes/logs/css/style.css" rel="stylesheet" />
	<link href='//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic' rel='stylesheet' type='text/css'>
	<?php if (publisher()):?><link href="<?php echo publisher() ?>" rel="publisher" /><?php endif;?>
	<?php if (wmt()):?><?php echo wmt() ?><?php endif;?>
	<!--[if lt IE 9]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body class="<?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
<div class="hide">
	<meta content="<?php echo blog_title() ?>" itemprop="name"/>
	<meta content="<?php echo blog_description() ?>" itemprop="description"/>
</div>
<?php if(facebook()) { echo facebook();} ?>
<?php if(login()) { toolbar();} ?>
	<div id="cover">
		<div id="header-wrapper">
			<header id="header" class="responsive">
				<div id="branding">
					<?php if(is_index()) {?>
						<h1 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h1>
					<?php } else {?>
						<h2 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h2>
					<?php } ?>
					<div class="blog-tagline"><p><?php echo blog_tagline() ?></p></div>
				</div>
			</header>
		</div>
		<div id="menu-wrapper">
			<nav id="menu" class="responsive">
				<?php echo menu() ?>
				<?php echo search() ?>
			</nav>
		</div>
		<div id="main-wrapper">
			<div id="main" class="responsive">
				<section id="content">
					<?php echo content()?>
				</section>
				<aside id="sidebar">
					<div class="about">
						<h3>About</h3>
						<p><?php echo blog_description() ?></p>
					</div>
					<div class="social">
						<h3>Follow</h3>
						<?php echo social() ?>
					</div>
					<div class="archive">
						<?php echo archive_list()?>
					</div>
					<?php if(disqus()):?>
						<div class="comments">
							<?php echo recent_comments() ?>
						</div>
					<?php endif;?>
					<div class="tagcloud">
						<?php echo tag_cloud()?>
					</div>
				</aside>
			</div>
		</div>
		<div id="copyright-wrapper">
			<footer id="copyright" class="responsive">
				<?php echo copyright() ?>
			</footer>
		</div>
	</div>
	<?php if (analytics()):?><?php echo analytics() ?><?php endif;?>
</body>
</html>
