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

function is_admin()
{
	if(login()) {
		$user = $_SESSION[config("site.url")]['user'];
		$role = user('role', $user);
		if ($role === 'admin') {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function is_subadmin()
{
	if(login()) {
		$user = $_SESSION[config("site.url")]['user'];
		$role = user('role', $user);
		if ($role === 'subadmin' || $role === 'admin') {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function is_editor()
{
	if(login()) {
		$user = $_SESSION[config("site.url")]['user'];
		$role = user('role', $user);
		if ($role === 'editor' || $role === 'subadmin' || $role === 'admin') {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function is_moderator()
{
	if(login()) {
		$user = $_SESSION[config("site.url")]['user'];
		$role = user('role', $user);
		if ($role === 'moderator' || $role === 'editor' || $role === 'subadmin' || $role === 'admin') {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function is_author()
{
	if(login()) {
		$user = $_SESSION[config("site.url")]['user'];
		$role = user('role', $user);
		if ($role === 'author' || $role === 'moderator' || $role === 'editor' || $role === 'subadmin' || $role === 'admin') {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function is_user()
{
	if(login()) {
		$user = $_SESSION[config("site.url")]['user'];
		$role = user('role', $user);
		if ($role === 'user' || $role === 'author' || $role === 'moderator' || $role === 'editor' || $role === 'subadmin' || $role === 'admin') {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
