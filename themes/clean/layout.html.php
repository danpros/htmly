<!DOCTYPE html>
<html>
<head>
	<?php echo $head_contents ?>
	<link href="<?php echo site_url() ?>themes/clean/css/style.css" rel="stylesheet" />
	<link href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:700&subset=latin,cyrillic-ext" rel="stylesheet" />
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
	<aside>
		<?php if(is_index()) {?>
			<h1 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h1>
		<?php } else {?>
			<h2 class="blog-title"><a rel="home" href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h2>
		<?php } ?>
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
	<?php if (analytics()):?><?php echo analytics() ?><?php endif;?>
</body>
</html>
