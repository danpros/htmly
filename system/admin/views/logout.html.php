<?php

unset($_SESSION[config("site.url")]);

header('location: login');

?>