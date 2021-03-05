<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php

unset($_SESSION[config("site.url")]);

header('location: login');

?>