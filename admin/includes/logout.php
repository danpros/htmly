<?php
ob_start();
include 'session.php';

session_destroy();

header('location: ../index.php');

?>
