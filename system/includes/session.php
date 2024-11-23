<?php
$samesite = 'strict';
if(PHP_VERSION_ID < 70300) {
    session_set_cookie_params('samesite='.$samesite);	
} else {
    session_set_cookie_params(['samesite' => $samesite]);
}

session_start();

function login()
{
    if (session_status() == PHP_SESSION_NONE) return false;
    if (isset($_SESSION[site_url()]['user']) && !empty($_SESSION[site_url()]['user'])) {
        return true;
    } else {
        return false;
    }

}
