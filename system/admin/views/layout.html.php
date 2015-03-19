<!DOCTYPE html>
<html>
<head>
	<?php echo $head_contents ?>
	<link href="<?php echo site_url() ?>system/admin/admin.css" rel="stylesheet" />
	<link href="<?php echo site_url() ?>system/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	
	<?php if (publisher()):?><link href="<?php echo publisher() ?>" rel="publisher" /><?php endif;?>
	<!--[if lt IE 9]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>

<body class="admin <?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
	<div class="hide">
		<meta content="<?php echo blog_title() ?>" itemprop="name"/>
		<meta content="<?php echo blog_description() ?>" itemprop="description"/>
	</div>
	<?php if(login()) { toolbar();} ?>
	
	<header class="row">
	
	</header>
	
	<?php echo content()?>
	
	<footer class="row tema_abu main_footer">
		<?php echo copyright() ?>
	</footer>

	<?php if (analytics()):?><?php echo analytics() ?><?php endif;?>
</body>
</html>