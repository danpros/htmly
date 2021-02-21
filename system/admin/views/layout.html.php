<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title><?php echo $title;?></title>

	<link rel="stylesheet" href="<?php echo site_url() ?>system/resources/css/font-awesome.css">
	<link href="<?php echo site_url() ?>system/resources/css/adminlte.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
	<script src="<?php echo site_url() ?>system/resources/js/jquery.min.js"></script>
</head>
<?php     
    if (isset($_GET['search'])) {
        $search = _h($_GET['search']);
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }
?>
<?php if (login()) { ?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
<style>.error-message ul {margin:0;padding:0;}</style>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo site_url();?>" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
        <input type="search" name="search" class="form-control" placeholder="Type to search">
    </form>


  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">


    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo site_url(); ?>system/resources/images/logo-small.png" class="img-circle elevation-2" alt="HTMLy logo">
        </div>
        <div class="info">
          <a href="<?php echo site_url();?>admin" class="d-block">Dashboard</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="<?php echo site_url();?>admin/content" class="nav-link">
              <i class="nav-icon fa fa-th"></i>
              <p>
                Add Content
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-thumb-tack"></i>
              <p>
                Posts
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/posts" class="nav-link">
                  <p>Posts list</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/draft" class="nav-link">
                  <p>Posts draft</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/pages" class="nav-link">
                  <p>Static pages</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/categories" class="nav-link">
                  <p>Categories</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-cogs"></i>
              <p>
                Settings
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/config" class="nav-link">
                  <p>Config</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/config/reading" class="nav-link">
                  <p>Reading</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/config/widget" class="nav-link">
                  <p>Widget</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/config/metatags" class="nav-link">
                  <p>Metatags</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/config/performance" class="nav-link">
                  <p>Performance</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/config/custom" class="nav-link">
                  <p>Custom</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/menu" class="nav-link">
                  <p>Menus</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-briefcase"></i>
              <p>
                Tools
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/clear-cache" class="nav-link">
                  <p>Clear cache</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/update" class="nav-link">
                  <p>Check update</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/backup" class="nav-link">
                  <p>Backup</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/import" class="nav-link">
                  <p>Import RSS</p>
                </a>
              </li>
			  <?php if (config('views.counter') == 'true') { ?>
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/popular" class="nav-link">
                  <p>Popular posts</p>
                </a>
              </li>
			  <?php } ?>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-user"></i>
              <p>
                User
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo site_url();?>admin/mine" class="nav-link">
                  <p>My posts</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>edit/profile" class="nav-link">
                  <p>Edit profile</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url();?>logout" class="nav-link">
                  <p>Logout</p>
                </a>
              </li>
            </ul>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col">
			<?php if (!empty($breadcrumb)): ?>
				<style>.breadcrumb a {margin:0 5px;}</style>
				<div class="breadcrumb"><?php echo $breadcrumb ?></div>
			<?php endif; ?>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col" >
            <div class="card card-primary card-outline">
              <div class="card-body">
					<?php echo content() ?>
              </div>
            </div><!-- /.card -->
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      <small>Admin panel style based on <a rel="nofollow" target="_blank" href="https://github.com/ColorlibHQ/AdminLTE">AdminLTE</a></small>
    </div>
    <!-- Default to the left -->
    Proudly powered by <a href="https://www.htmly.com" target="_blank">HTMLy</a>
  </footer>
</div>
<!-- ./wrapper -->
<?php } else { ?>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <h1><a href="https://www.htmly.com" target="_blank"><img width="200px" src="<?php echo site_url(); ?>system/resources/images/logo-big.png" alt="HTMLy"/></a></h1>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>
		<?php echo content();?>
    </div>
    <!-- /.login-card-body -->
  </div>
  <span><a href="<?php echo site_url();?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
</svg> Back to <?php echo blog_title();?></a></span>
  
</div>
<?php } ?>

<script src="<?php echo site_url() ?>system/resources/js/bootstrap.min.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/adminlte.min.js"></script>
</body>
</html>
