<?php
date_default_timezone_set('Asia/Jakarta');
require '../system/includes/dispatch.php';
config('source', '../admin/config.ini');
include 'includes/session.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin Panel</title>
	<link rel="stylesheet" type="text/css" href="resources/style.css" />
</head>
<body>
<div class="wrapper-outer">
<div class="wrapper-inner">
	<?php include 'includes/auth.php'; ?>
</div>
</div>
</body>
</html>