<?php if (!login()) { ?>
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <?php echo head_contents() ?>
        <meta name="description" content="">
        <?php if($canonical): ?>
            <link rel="canonical" href="<?php echo $canonical; ?>" />
        <?php endif; ?>
        <link href="<?php echo site_url() ?>system/resources/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo site_url() ?>system/resources/css/ie10-viewport-bug-workaround.css" rel="stylesheet">
        <link href="<?php echo site_url() ?>system/resources/css/signin.css" rel="stylesheet">
        <!--[if lt IE 9]><script src="<?php echo site_url() ?>system/resources/js/ie8-responsive-file-warning.js"></script><![endif]-->
        <script src="<?php echo site_url() ?>system/resources/js/ie-emulation-modes-warning.js"></script>
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
        <div class="container">
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>ERROR!</strong> <?php echo $error ?>
            </div>
        <?php } ?>
            <form class="form-signin" method="POST" action="login">
                <h2 class="form-signin-heading">Please sign in</h2>
                <label for="inputUsername" class="sr-only">Username</label>
                <input name="user" type="username" id="inputUsername" class="form-control" placeholder="your username ..." required autofocus>
                <label for="inputPassword" class="sr-only">Password</label>
                <input name="password" type="password" id="inputPassword" class="form-control" placeholder="your password" required>
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
                <button class="btn btn-lg btn-primary btn-block" type="submit" value="login">Sign in</button>
                <br/>
                <a type="button" href="<?php echo site_url() ?>"><< Return to Blog</a>
            </form>
        </div>
        <script src="<?php echo site_url() ?>system/resources/js/ie10-viewport-bug-workaround.js"></script>
      </body>
    </html>
<?php } else {
    header('location: admin');
} ?>