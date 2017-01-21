<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo blog_title() ?> | <?php echo blog_tagline() ?></title>

    <link href="<?php echo site_url();?>themes/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <link href="<?php echo site_url();?>themes/bootstrap/css/blog-home.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<?php     
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }
?>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo site_url();?>"><?php echo blog_title() ?></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php echo menu('navbar-nav pull-right') ?>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <div class="col-md-8 content">
                <div class="container konten">
                    <div class="row">
                        <div class="col-md-8">
                            <?php echo content() ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="well">
                    <h4>Blog Search</h4>
                    <form id="search" class="navbar-form search" role="search">
                        <div class="input-group" style="width: 100%;">
                            <input type="search" name="search" class="form-control" placeholder="Type to search">
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-submit" type="submit">
                                    <span class="glyphicon glyphicon-search"></span>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
                
                <div class='well'>
                    <div class="recent">
                        <h4>Recent Posts</h4>
                        <?php echo recent_posts() ?>
                    </div>
                </div>

                <div class="well">
                    <h4>Blog Categories</h4>
                    <div class="row">
                        <div class="col-lg-12">
                            <?php echo category_list(NULL, 'list-unstyled') ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <hr>

        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; <?php echo blog_copyright() ?></p>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->
    <script src="<?php echo site_url();?>themes/bootstrap/js/jquery.js"></script>
    <script src="<?php echo site_url();?>themes/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>
