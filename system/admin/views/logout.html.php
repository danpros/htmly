<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php

unset($_SESSION[site_url()]);
if (session_status() === PHP_SESSION_ACTIVE) {
	set_session_cookie_lifetime(time() - 3600);
	session_destroy();
}

header('location: login');

?>