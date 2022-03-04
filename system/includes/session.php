<?php
$samesite = 'strict';
if(PHP_VERSION_ID < 70300) {
    session_set_cookie_params('samesite='.$samesite);	
} else {
    session_set_cookie_params(['samesite' => $samesite]);
}

if (isset($_COOKIE['PHPSESSID']))
    session_start();

function login()
{
    if (session_status() == PHP_SESSION_NONE) return false;
    if (isset($_SESSION[config("site.url")]['user']) && !empty($_SESSION[config("site.url")]['user'])) {
        return true;
    } else {
        return false;
    }

}
