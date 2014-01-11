<!DOCTYPE html>
<html>
<head>
	<link href='<?php echo site_url() ?>favicon.ico' rel='icon' type='image/x-icon'/>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no" />
	<title>Search results not found! - <?php echo config('blog.title') ?></title>
	<link href="<?php echo site_url() ?>themes/default/css/style.css" rel="stylesheet" />
	<!-- Include the Open Sans font -->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
</head>
<body>
	<div class="center message">
		<h1>Search results not found!</h1>
		<div class="search-404">
			<form id="search-form-404" method="get">
				<input type="text" class="search-input" name="search" value="Search..." onfocus="if (this.value == 'Search...') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search...';}">
				<input type="submit" value="Search" class="search-button">
			</form>
			<?php if(isset($_GET['search'])) {$url = site_url() . 'search/' . $_GET['search']; header ("Location: $url");} ?>
		</div>
		<p>Please search again, or would you like to try our <a href="<?php echo site_url() ?>">homepage</a> instead?</p>
	</div>
</body>
</html>