<?php
if (!defined('HTMLY')) die('HTMLy');

session_start();

function login()
{
    if (isset($_SESSION[config("site.url")]['user']) && !empty($_SESSION[config("site.url")]['user'])) {
        return true;
    } else {
        return false;
    }

}
