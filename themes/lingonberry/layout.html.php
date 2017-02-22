<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <?php echo head_contents();?>
    <title><?php echo $title;?></title>
    <meta name="description" content="<?php echo $description; ?>"/>
    <link rel="canonical" href="<?php echo $canonical; ?>" />
    <?php if (publisher()): ?>
    <link href="<?php echo publisher() ?>" rel="publisher" /><?php endif; ?>
	<link rel="stylesheet" id="lingonberry_googleFonts-css"  href="//fonts.googleapis.com/css?family=Lato%3A400%2C700%2C400italic%2C700italic%7CRaleway%3A600%2C500%2C400&#038;ver=4.3.1" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo site_url();?>themes/lingonberry/css/fontello/css/fontello.css">
    <link rel="stylesheet" href="<?php echo site_url();?>themes/lingonberry/css/normalize.css">   
    <link rel="stylesheet" href="<?php echo site_url();?>themes/lingonberry/css/style.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head> 
<?php     
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }
?>
<body class="<?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
	<div class="hide">
		<meta content="<?php echo blog_title() ?>" itemprop="name"/>
		<meta content="<?php echo blog_description() ?>" itemprop="description"/>
	</div>
	<?php if (facebook()) { echo facebook(); } ?>
	<?php if (login()) { toolbar(); } ?>

	<div class="navigation<?php if (login()) { echo ' spacer'; } ?>">
			
		<div class="navigation-inner section-inner">
	
			<?php echo menu('blog-menu');?>					
			 
			<form id="search" class="search-form" role="search">
				<div class="input-group">
				<i class="icon-search"></i>
				<input id="s" type="search" name="search" class="form-control" placeholder="Type to search">
				<span class="input-group-btn"><button type="submit" class="btn btn-default btn-submit"><i class="fa fa-angle-right"></i></button></span>
				</div>
			</form>
			 
			 <div class="clear"></div>
		 
		</div> <!-- /navigation-inner -->
	 
	</div> <!-- /navigation -->

	<div class="header section">
			
		<div class="header-inner section-inner">
								
			<a href="<?php echo site_url(); ?>" title="<?php echo blog_title();?>" rel="home" class="logo">
 				<img src="<?php echo site_url(); ?>themes/lingonberry/images/logo.jpg" alt="<?php echo blog_title();?>">
			</a>

			<?php if(is_index()) : ?>
				<h1 class="blog-title">
					<a href="<?php echo site_url(); ?>" title="<?php echo blog_title();?>" rel="home"><?php echo blog_title();?></a>
				</h1>
			<?php else : ?>
				<h2 class="blog-title">
					<a href="<?php echo site_url(); ?>" title="<?php echo blog_title();?>" rel="home"><?php echo blog_title();?></a>
				</h2>
			<?php endif; ?>
										
			 <div class="clear"></div>
																						
		</div> <!-- /header section -->
		
	</div> <!-- /header-inner section-inner -->

	<?php echo content(); ?>

	<div class="footer section">
		<div class="footer-inner section-inner">
				<div class="footer-a widgets">
					<h3 class="widget-title">Recent Posts</h2>
					<div class="widget-content">
						<?php echo recent_posts(); ?>
					</div>					
				</div>
				
				<div class="footer-b widgets">
					<h3 class="widget-title">Popular Posts</h2>
					<div class="widget-content">
						<?php echo popular_posts(); ?>
					</div>				
				</div>
			
				<div class="footer-c widgets">
					<?php if (config('input.showCat') == 'true') : ?>				
						<h3 class="widget-title">Categories</h3>
						<div class="widget-content">
							<?php echo category_list();?>
						</div>
					<?php elseif (config('input.showTag') == 'true') : ?>				
						<h3 class="widget-title">Tags</h3>
						<div class="widget-content">
							<?php echo tag_cloud();?>
						</div>
					<?php else : ?>
						<h3 class="widget-title">Nothing in here</h3>
						<div class="widget-content">
							<p>There are no categories or tags yet</p>
						</div>
					<?php endif; ?>
				</div>
							
			<div class="clear"></div>
		</div> <!-- /footer-inner -->
	</div> <!-- /footer -->

	<div class="credits section">
		<div class="credits-inner section-inner">
			<div class="credits-left">
				<span><?php echo copyright();?></span>
			</div>
			<div class="credits-right">
				<span>Theme by <a href="http://www.andersnoren.se">Anders Noren</a>&mdash; ported to HTMLy by <a href="http://trendschau.net">Trendschau</a></span>
			</div>			
			<div class="clear"></div>
		</div> <!-- /credits-inner -->
	</div> <!-- /credits -->
    <script src="<?php echo site_url();?>themes/lingonberry/js/lazy.js"></script>
</body>
</html>	