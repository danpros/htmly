<!DOCTYPE html>
<html lang="en">
  <head>
    <?php echo head_contents() ?>
    <title><?php echo $title;?></title>
    <meta name="description" content="<?php echo $description; ?>"/>
    <?php if($canonical): ?>
        <link rel="canonical" href="<?php echo $canonical; ?>" />
    <?php endif; ?>
    <link href="<?php echo site_url() ?>system/resources/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo site_url() ?>system/resources/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
    <link href="<?php echo site_url() ?>system/resources/css/datatables.min.css" rel="stylesheet">
    <link href="<?php echo site_url() ?>system/resources/css/dashboard.css" rel="stylesheet">
    <!--[if lt IE 9]><script src="<?php echo site_url() ?>system/resources/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo site_url() ?>system/resources/js/ie-emulation-modes-warning.js"></script>
    <?php if (publisher()): ?>
        <link href="<?php echo publisher() ?>" rel="publisher" /><?php endif; ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
            <a class="navbar-brand" href="<?php echo site_url() ?>admin"><image style="display: inline-block;" src="<?php echo site_url() ?>system/resources/images/logo-small-white.png" width="25"></image> Dashboard</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <?php 
                if (login()) {
                    newToolBar();
                } 
            ?>
          </ul>
          <?php echo search() ?>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar list-group">
          <?php echo sideBar(); ?>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <?php echo content() ?>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?php echo site_url() ?>system/resources/js/jquery.min.js"></script>
    <script src="<?php echo site_url() ?>system/resources/js/datatables.min.js"></script>
    <script src="<?php echo site_url() ?>system/resources/js/bootstrap.min.js"></script>
    <script src="<?php echo site_url() ?>system/resources/js/holder.min.js"></script>
    <script src="<?php echo site_url() ?>system/resources/js/ie10-viewport-bug-workaround.js"></script>
    <script>
        $(document).ready(function() {
            $('#category-list').DataTable({
                "searching":false,
            });
            $('#post-list').DataTable({
                "searching":false,
            });
            $('#overview-post-list').DataTable({
                "searching":false,
                "paging":false,
                "ordering":false,
                "bInfo":false
            });
            $('body .dropdown-toggle').dropdown();
        });
    </script>
  </body>
</html>
