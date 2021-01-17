<!doctype html>
<html class="no-js" lang="en">
<head>
	<?php echo head_contents() ?>
	<title><?php echo $title;?></title>
	<meta name="description" content="<?php echo $description; ?>"/>
<?php if($canonical): ?>
	<link rel="canonical" href="<?php echo $canonical; ?>" />
<?php endif; ?>
	<link href="/htmly/001.css" rel="stylesheet"/>
	<link href="<?php echo site_url() ?>system/resources/css/admin.css" rel="stylesheet"/>
	<link href="//fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">

	<?php if (publisher()): ?>
		<link href="<?php echo publisher() ?>" rel="publisher" />
	<?php endif; ?>
</head>
<body class="admin <?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
	<!--[if lte IE 9]>
	  <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
	<![endif]-->
<div class="hide">
	<!-- meta itemprops may be redundant, use open graph protocol instead? - https://webmasters.stackexchange.com/questions/108310/should-we-use-name-description-or-itemprop-description-in-the-tag-for-a-meta - 2021.01.16-sean1138 -->
	<meta content="<?php echo blog_title() ?>" itemprop="name"/>
	<meta content="<?php echo blog_description() ?>" itemprop="description"/>
</div>
<nav id="toolbar">
	<?php if (login()) {toolbar();} ?>
</nav>
<main>
	<header>
		<nav>
			<?php echo menu() ?>
			<?php echo search() ?>
		</nav>
		<section id="branding">
			<h1 class="blog-title"><a href="<?php echo site_url() ?>"><?php echo blog_title() ?></a></h1>
			<div class="blog-tagline"><p><?php echo blog_tagline() ?></p></div>
		</section>
	</header>
	<article>
		<section id="content">
			<?php echo content() ?>
		</section>
	</article>
	<footer>
		<?php echo copyright() ?>
	</footer>
</main>
<script type="text/javascript" src="/htmly/001.js"></script>
<?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>
</body>
</html>