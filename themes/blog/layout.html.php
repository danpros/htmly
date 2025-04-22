<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="<?php echo blog_language();?>">
<head>
    <?php echo head_contents();?>
    <?php echo $metatags;?>
    <link href="//fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Crimson+Text:400,400italic" rel="stylesheet" type="text/css">     
    <!-- Global CSS -->
    <link rel="stylesheet" href="<?php echo theme_path();?>css/bootstrap.min.css">   
    <!-- Plugins CSS -->
    <link rel="stylesheet" href="<?php echo theme_path();?>css/font-awesome.min.css">
    <!-- Theme CSS -->  
    <link id="theme-style" rel="stylesheet" href="<?php echo theme_path();?>css/styles.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="<?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
<div class="hide">
    <meta content="<?php echo blog_title() ?>" itemprop="name"/>
    <meta content="<?php echo strip_tags(blog_description()); ?>" itemprop="description"/>
</div>
<?php if (facebook()) { echo facebook(); } ?>
<?php if (login()) { toolbar(); } ?>
    <!-- ******HEADER****** --> 
    <header class="header">
        <div class="container">                       
            <div class="logo pull-left"><img class="logo-image" src="<?php echo theme_path();?>images/logo.png"/></div>
            <div class="branding pull-left">
                <?php if (is_index()) {?>
                    <h1 class="name"><a href="<?php echo site_url();?>"><?php echo blog_title();?></a></h1>
                <?php } else {?>
                    <h2 class="name"><a href="<?php echo site_url();?>"><?php echo blog_title();?></a></h2>
                <?php } ?>
                <p class="desc"><?php echo blog_tagline();?></p>   
                <?php echo social('social');?>
            </div><!--//branding-->
            <nav id="main-nav" class="main-nav navbar-right" role="navigation" > 
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div id="navbar" class="menu navbar-collapse collapse pull-right">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-search"></i></a>
                            <div class="dropdown-menu searchbox" role="menu">
                                <form id="search" class="navbar-form search" role="search">
                                    <div class="input-group">
                                    <input type="search" name="search" class="form-control" placeholder="Type to search">
                                    <span class="input-group-btn"><button type="submit" class="btn btn-default btn-submit"><i class="fa fa-angle-right"></i></button></span>
                                    </div>
                                </form>
                            </div>
                        </li><!-- /.searchbox -->
                    </ul>
                    <?php echo menu('navbar-nav navbar-right');?>
                </div>
            </nav>
        </div><!--//container-->
    </header><!--//header-->
    <div class="container sections-wrapper">
        <div class="row">
            <div class="primary col-md-8 col-sm-12 col-xs-12">
            <?php echo content();?>
            </div><!--//primary-->
            <div class="secondary col-md-4 col-sm-12 col-xs-12">
                <aside class="aside section">
                    <div class="section-inner">
                        <h2 class="heading"><?php echo i18n("About");?></h2>
                        <div class="content">
                         <?php echo blog_description();?>                                  
                        </div><!--//content-->  
                    </div><!--//section-inner-->                 
                </aside><!--//section-->
                <aside class="recent-posts aside section">
                    <div class="section-inner">
                        <!-- Tab nav -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#recent-posts" aria-controls="recent-posts" role="tab" data-toggle="tab"><?php echo i18n("Recent_posts");?></a></li>
                            <?php if (config('views.counter') === 'true') :?>
                            <li role="presentation"><a href="#popular-posts" aria-controls="popular-posts" role="tab" data-toggle="tab"><?php echo i18n("Popular_posts");?></a></li>
                            <?php endif;?>
                        </ul>
                        <!-- Tab content -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="recent-posts">
                                <h2 class="hide"><?php echo i18n("Recent_Posts");?></h2>
                                <?php $recent = recent_posts(true);?>
                                <?php foreach ($recent as $rc):?>
                                    <?php $recentTitle = (strlen(strip_tags($rc->title)) > 60) ? shorten($rc->title, 60) . '...' : $rc->title; ?>
                                    <div class="item">
                                        <h3 class="title"><a href="<?php echo $rc->url;?>"><?php echo $recentTitle;?></a></h3>
                                        <div class="content">
                                        <p><?php echo shorten($rc->body, 75); ?>...</p>
                                        <a class="more-link" href="<?php echo $rc->url;?>"><i class="fa fa-link"></i> <?php echo i18n("read_more");?></a>
                                        </div><!--//content-->
                                    </div>
                                <?php endforeach;?>
                            </div>
                            <?php if (config('views.counter') === 'true') :?>
                            <div role="tabpanel" class="tab-pane" id="popular-posts">
                                <h2 class="hide"><?php echo i18n("Popular_posts");?></h2>
                                <?php $popular = popular_posts(true);?>
                                <?php foreach ($popular as $pp):?>
                                    <?php $popularTitle = (strlen(strip_tags($pp->title)) > 60) ? shorten($pp->title, 60) . '...' : $pp->title; ?>
                                    <div class="item">
                                        <h3 class="title"><a href="<?php echo $pp->url;?>"><?php echo $popularTitle;?></a></h3>
                                        <div class="content">
                                        <p><?php echo shorten($pp->body, 75); ?>...</p>
                                        <a class="more-link" href="<?php echo $pp->url;?>"><i class="fa fa-link"></i> <?php echo i18n("read_more");?></a>
                                        </div><!--//content-->
                                    </div>
                                <?php endforeach;?>
                            </div>
                            <?php endif;?>
                        </div>
                    </div><!--//section-inner-->
                </aside><!--//section-->
                <?php if (disqus()): ?>
                <aside class="comments aside section">
                    <div class="section-inner">
                        <h2 class="heading"><?php echo i18n("Comments");?></h2>
                        <div class="content">
                            <?php echo recent_comments() ?>
                            <style>.dsq-widget-list {padding:0;}li.dsq-widget-item {color:#434343;border-bottom: 1px dotted #d9d9d9;margin: 0 0 10px;padding-bottom: 10px;font-size:14px;}li.dsq-widget-item:last-child{border-bottom:none;margin-bottom:0;}a.dsq-widget-user {font-weight:normal;}img.dsq-widget-avatar {margin-right:10px; }.dsq-widget-comment {display:block;padding-top:5px;}.dsq-widget-comment p {display:block;margin:0;padding:0!important;font-size:14px!important;}p.dsq-widget-meta {padding-top:5px!important;margin:0;font-size:14px!important;}#dsq-combo-widget.grey #dsq-combo-content .dsq-combo-box {background: transparent;}#dsq-combo-widget.grey #dsq-combo-tabs li {background: #DDDDDD;}</style>
                        </div><!--//content-->
                    </div><!--//section-inner-->
                </aside><!--//section-->
                <?php endif; ?>
                <aside class="archive aside section">
                    <div class="section-inner">
                        <h2 class="heading"><?php echo i18n("Archives");?></h2>
                        <div class="content">
                            <?php echo archive_list();?>
                        </div><!--//content-->
                    </div><!--//section-inner-->
                </aside><!--//section-->
                <aside class="category-list aside section">
                    <div class="section-inner">
                        <h2 class="heading"><?php echo i18n('Category');?></h2>
                        <div class="content">
                            <?php echo category_list();?>
                        </div><!--//content-->
                    </div><!--//section-inner-->
                </aside><!--//section-->
                <aside class="category-list aside section">
                    <div class="section-inner">
                        <h2 class="heading"><?php echo i18n("Tags");?></h2>
                        <div class="content">
                        <div class="tagcloud">
                        <?php echo tag_cloud();?>
                        </div>
                        </div><!--//content-->
                    </div><!--//section-inner-->
                </aside><!--//section-->
            </div><!--//secondary-->    
        </div><!--//row-->
    </div><!--//masonry-->
    <!-- ******FOOTER****** --> 
    <footer class="footer">
        <div class="container text-center">
            <?php echo copyright();?><br><span>Design by <a href="https://3rdwavemedia.com/" target="_blank" rel="nofollow">3rd Wave Media</a></span>
        </div><!--//container-->
    </footer><!--//footer-->
    <!-- Javascript -->          
    <script type="text/javascript" src="<?php echo theme_path();?>js/jquery-latest.min.js"></script>
    <script type="text/javascript" src="<?php echo theme_path();?>js/bootstrap.min.js"></script>
<?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>    
</body>
</html> 