<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php echo head_contents(); ?>
  <title><?php echo $title; ?></title>
  <meta name="description" content="<?php echo $description; ?>"/>
  <link rel="canonical" href="<?php echo $canonical; ?>" />
	<link rel="stylesheet" href="<?php echo site_url() ?>system/resources/css/font-awesome.css">
	<link rel="stylesheet" href="<?php echo site_url() ?>system/resources/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo site_url() ?>system/resources/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/resources/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/resources/css/responsive.bootstrap4.min.css">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <script src="<?php echo site_url() ?>system/resources/js/jquery.min.js"></script>
</head>
<?php if (isset($_GET['search'])) {
    $search = _h($_GET['search']);
    $url = site_url() . 'search/' . remove_accent($search);
    header("Location: $url");
} ?>
<?php if (login()) { ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<style>.error-message ul {margin:0;padding:0;}</style>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fa fa-bars"></i></a>
      </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
        <input type="search" name="search" class="form-control" placeholder="<?php echo i18n('Type_to_search') ?>">
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item d-sm-inline-block">
        <a href="<?php echo site_url(); ?>" class="nav-link"><i class="fa fa-home"></i> <span class="d-none d-sm-inline-block"><?php echo config('breadcrumb.home') ?></span></a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?php echo site_url(); ?>admin" class="brand-link">
    <img src="<?php echo site_url(); ?>system/resources/images/logo-small.png"
         alt="HTMLy Logo"
         class="brand-image img-circle elevation-3"
         style="opacity: .8">
    <span class="brand-text font-weight-light"><?php echo i18n('Dashboard') ?></span>
  </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="<?php echo site_url(); ?>admin/content" class="nav-link">
              <i class="nav-icon fa fa-th"></i>
              <p><?php echo ucwords(i18n('Add_content')); ?></p>
            </a>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-thumb-tack"></i>
              <p>
                <?php echo i18n('Posts'); ?>
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/posts" class="nav-link">
                  <p><?php echo i18n('Posts_list'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/draft" class="nav-link">
                  <p><?php echo i18n('Posts_draft'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/pages" class="nav-link">
                  <p><?php echo i18n('Static_pages'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/categories" class="nav-link">
                  <p><?php echo i18n('Categories'); ?></p>
                </a>
              </li>
            </ul>
          </li>
          <?php if(is_admin()): ?>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-users"></i>
              <p>
                <?php echo i18n('Authors'); ?> 
                <i class="right fa fa-angle-left"></i>
                <sup class="font-weight-bold text-danger"><?php echo i18n('Beta'); ?></sup>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/authors" class="nav-link">
                  <p><?php echo i18n('Authors_list'); ?></p>
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-cogs"></i>
              <p>
                <?php echo i18n('Settings'); ?>
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php if (config('static.frontpage') === 'true'): ?>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>edit/frontpage" class="nav-link">
                  <p><?php echo i18n('Edit_frontpage'); ?></p>
                </a>
              </li>
              <?php endif; ?>
              <?php if(is_admin()): ?>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/config" class="nav-link">
                  <p><?php echo i18n('Config'); ?></p>
                </a>
              </li>
              <?php endif; ?>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/menu" class="nav-link">
                  <p><?php echo i18n('Menus'); ?></p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-briefcase"></i>
              <p>
                <?php echo i18n('Tools'); ?>
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/clear-cache" class="nav-link">
                  <p><?php echo i18n('Clear_cache'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/update" class="nav-link">
                  <p><?php echo i18n('Check_update'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/backup" class="nav-link">
                  <p><?php echo i18n('Backup'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/import" class="nav-link">
                  <p><?php echo i18n('Import_RSS'); ?></p>
                </a>
              </li>
			        <?php if (config('views.counter') === 'true'): ?>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/popular" class="nav-link">
                  <p><?php echo i18n('Popular_posts'); ?></p>
                </a>
              </li>
              <?php endif; ?>
            </ul>
          </li>
          <li class="nav-item has-treeview menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa-user"></i>
              <p>
                <?php echo i18n('User'); ?>
                <i class="right fa fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>admin/mine" class="nav-link">
                  <p><?php echo i18n('My_posts'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>edit/profile" class="nav-link">
                  <p><?php echo i18n('Edit_profile'); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo site_url(); ?>logout" class="nav-link">
                  <p><?php echo i18n('Logout'); ?></p>
                </a>
              </li>
            </ul>
          </li>
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

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      <small><?php echo i18n('Admin_panel_style_based_on'); ?> <a rel="nofollow" target="_blank" href="https://github.com/ColorlibHQ/AdminLTE">AdminLTE</a></small>
    </div>
    <!-- Default to the left -->
    <?php echo i18n('Proudly_powered_by'); ?> <a href="https://www.htmly.com" target="_blank">HTMLy</a>
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
      <p class="login-box-msg"><?php echo i18n('Sign_in_to_start_your_session'); ?></p>
		<?php echo content(); ?>
    </div>
    <!-- /.login-card-body -->
  </div>
  <span><a href="<?php echo site_url(); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
  </svg> <?php echo i18n('Back_to'); ?> <?php echo blog_title(); ?></a></span>
</div>
<?php } ?>

<script src="<?php echo site_url() ?>system/resources/js/bootstrap.min.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/jquery.dataTables.min.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/dataTables.responsive.min.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/jquery.overlayScrollbars.min.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    $('#htmly-table').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": false,
      "info": false,
      "autoWidth": true,
      "responsive": true,
    });
} );
</script>
</body>
</html>
