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
    <link href="//fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Crimson+Text:400,400italic" rel="stylesheet" type="text/css">     
    <!-- Global CSS -->
    <link rel="stylesheet" href="<?php echo site_url();?>themes/blog/css/bootstrap.min.css">   
    <!-- Plugins CSS -->
    <link rel="stylesheet" href="<?php echo site_url();?>themes/blog/css/font-awesome.min.css">
    <!-- Theme CSS -->  
    <link id="theme-style" rel="stylesheet" href="<?php echo site_url();?>themes/blog/css/styles.css">
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
    <!-- ******HEADER****** --> 
    <header class="header">
        <div class="container">                       
            <div class="logo pull-left"><img class="logo-image" src="<?php echo site_url();?>themes/blog/images/logo.png"/></div>
            <div class="branding pull-left">
                <?php if (is_index()) {?>
                    <h1 class="name"><a href="<?php echo site_url();?>"><?php echo blog_title();?></a></h1>
                <?php } else {?>
                    <h2 class="name"><a href="<?php echo site_url();?>"><?php echo blog_title();?></a></h2>
                <?php } ?>
                <p class="desc"><?php echo blog_tagline();?></p>   
                <ul class="social list-inline">
                    <li><a href="<?php echo config('social.twitter');?>"><i class="fa fa-twitter"></i></a></li>                   
                    <li><a href="<?php echo config('social.google');?>"><i class="fa fa-google-plus"></i></a></li>
                    <li><a href="<?php echo config('social.facebook');?>"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="<?php echo config('social.tumblr');?>"><i class="fa fa-tumblr"></i></a></li>
                    <li><a href="<?php echo site_url();?>feed/rss"><i class="fa fa-rss"></i></a></li>                                    
                </ul> 
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
                        <h2 class="heading">About</h2>
                        <div class="content">
                         <?php echo blog_description();?>                                  
                        </div><!--//content-->  
                    </div><!--//section-inner-->                 
                </aside><!--//section-->
                <aside class="recent-posts aside section">
                    <div class="section-inner">
                        <!-- Tab nav -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#recent-posts" aria-controls="recent-posts" role="tab" data-toggle="tab">Recent Posts</a></li>
                            <?php if (config('views.counter') === 'true') :?>
                            <li role="presentation"><a href="#popular-posts" aria-controls="popular-posts" role="tab" data-toggle="tab">Popular Posts</a></li>
                            <?php endif;?>
                        </ul>
                        <!-- Tab content -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="recent-posts">
                                <h2 class="hide">Recent Posts</h2>
                                <?php $lists = recent_posts(true);?>
                                <?php $char = 60;?>
                                <?php foreach ($lists as $l):?>
                                    <?php if (strlen(strip_tags($l->title)) > $char) { $recentTitle = shorten($l->title, $char) . '...';} else {$recentTitle = $l->title;}?>
                                    <div class="item">
                                        <h3 class="title"><a href="<?php echo $l->url;?>"><?php echo $recentTitle;?></a></h3>
                                        <div class="content">
                                        <p><?php echo shorten($l->body, 75); ?>...</p>
                                        <a class="more-link" href="<?php echo $l->url;?>"><i class="fa fa-link"></i> Read more</a>
                                        </div><!--//content-->
                                    </div>
                                <?php endforeach;?>
                            </div>
                            <?php if (config('views.counter') === 'true') :?>
                            <div role="tabpanel" class="tab-pane" id="popular-posts">
                                <h2 class="hide">Popular Posts</h2>
                                <?php $lists = popular_posts(true);?>
                                <?php $char = 60;?>
                                <?php foreach ($lists as $l):?>
                                    <?php if (strlen(strip_tags($l->title)) > $char) { $recentTitle = shorten($l->title, $char) . '...';} else {$recentTitle = $l->title;}?>
                                    <div class="item">
                                        <h3 class="title"><a href="<?php echo $l->url;?>"><?php echo $recentTitle;?></a></h3>
                                        <div class="content">
                                        <p><?php echo shorten($l->body, 75); ?>...</p>
                                        <a class="more-link" href="<?php echo $l->url;?>"><i class="fa fa-link"></i> Read more</a>
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
                        <h2 class="heading">Comments</h2>
                        <div class="content">
                            <?php echo recent_comments() ?>
                            <style>.dsq-widget-list {padding:0;}li.dsq-widget-item {color:#434343;border-bottom: 1px dotted #d9d9d9;margin: 0 0 10px;padding-bottom: 10px;font-size:14px;}li.dsq-widget-item:last-child{border-bottom:none;margin-bottom:0;}a.dsq-widget-user {font-weight:normal;}img.dsq-widget-avatar {margin-right:10px; }.dsq-widget-comment {display:block;padding-top:5px;}.dsq-widget-comment p {display:block;margin:0;padding:0!important;font-size:14px!important;}p.dsq-widget-meta {padding-top:5px!important;margin:0;font-size:14px!important;}#dsq-combo-widget.grey #dsq-combo-content .dsq-combo-box {background: transparent;}#dsq-combo-widget.grey #dsq-combo-tabs li {background: #DDDDDD;}</style>
                        </div><!--//content-->
                    </div><!--//section-inner-->
                </aside><!--//section-->
                <?php endif; ?>
                <aside class="archive aside section">
                    <div class="section-inner">
                        <h2 class="heading">Archive</h2>
                        <div class="content">
                            <?php echo archive_list();?>
                        </div><!--//content-->
                    </div><!--//section-inner-->
                </aside><!--//section-->
                <aside class="category-list aside section">
                    <div class="section-inner">
                        <h2 class="heading">Category</h2>
                        <div class="content">
                            <?php echo category_list();?>
                        </div><!--//content-->
                    </div><!--//section-inner-->
                </aside><!--//section-->
                <aside class="tags aside section">
                    <div class="section-inner">
                        <h2 class="heading">Tags</h2>
                        <div class="tag-cloud">
                            <?php $tags = tag_cloud(true);?>
                            <?php foreach ($tags as $tag => $count):?>
                                <a class="more-link" href="<?php echo site_url();?>tag/<?php echo $tag;?>"><?php echo tag_i18n($tag);?></a> 
                            <?php endforeach;?>
                        </div><!--//content-->
                    </div><!--//section-inner-->
                </aside><!--//section-->
            </div><!--//secondary-->    
        </div><!--//row-->
    </div><!--//masonry-->
    <!-- ******FOOTER****** --> 
    <footer class="footer">
        <div class="container text-center">
            <?php echo copyright();?>
        </div><!--//container-->
    </footer><!--//footer-->
    <!-- Javascript -->          
    <script type="text/javascript" src="<?php echo site_url();?>themes/blog/js/jquery-latest.min.js"></script>
    <script type="text/javascript" src="<?php echo site_url();?>themes/blog/js/bootstrap.min.js"></script>
<?php if (analytics()): ?><?php echo analytics() ?><?php endif; ?>    
</body>
</html> 