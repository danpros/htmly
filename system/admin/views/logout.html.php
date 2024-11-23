<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php

unset($_SESSION[site_url()]);

header('location: login');

?>