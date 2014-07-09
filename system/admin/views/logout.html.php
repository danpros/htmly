<?php

unset($_SESSION[config("site.url")]);
if(empty($_SESSION))
{
	session_destroy();
}

header('location: login');

?>