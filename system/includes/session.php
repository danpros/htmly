<?php
$samesite = 'strict';
if(PHP_VERSION_ID < 70300) {
    session_set_cookie_params('samesite='.$samesite);
} else {
    session_set_cookie_params(['samesite' => $samesite]);
}

session_start();

function current_session_ip()
{
    return isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
}

function is_session_ip_validation_enabled()
{
    $setting = config('session.ip.validation');
    return is_null($setting) || $setting === 'true';
}

function invalidate_auth_session()
{
    unset($_SESSION[site_url()]);

    if (session_status() === PHP_SESSION_ACTIVE) {
        set_session_cookie_lifetime(time() - 3600);
    }
}

function set_session_cookie_lifetime($lifetime = 0)
{
    if (session_status() == PHP_SESSION_NONE || headers_sent()) {
        return;
    }

    $params = session_get_cookie_params();
    if (PHP_VERSION_ID < 70300) {
        $path = isset($params['path']) ? $params['path'] : '/';
        $path .= '; samesite=' . $GLOBALS['samesite'];
        setcookie(session_name(), session_id(), $lifetime, $path, $params['domain'], $params['secure'], $params['httponly']);
    } else {
        setcookie(session_name(), session_id(), array(
            'expires' => $lifetime,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => isset($params['samesite']) ? $params['samesite'] : 'strict'
        ));
    }
}

function login()
{
    if (session_status() == PHP_SESSION_NONE) return false;
    if (isset($_SESSION[site_url()]['user']) && !empty($_SESSION[site_url()]['user'])) {
        if (!is_session_ip_validation_enabled()) {
            return true;
        }

        $storedIp = isset($_SESSION[site_url()]['ip']) ? (string) $_SESSION[site_url()]['ip'] : '';
        $currentIp = current_session_ip();

        // Backward compatibility for older sessions created before IP binding existed.
        if ($storedIp === '' && $currentIp !== '') {
            $_SESSION[site_url()]['ip'] = $currentIp;
            return true;
        }

        if ($storedIp !== '' && $currentIp !== '' && $storedIp !== $currentIp) {
            invalidate_auth_session();
            return false;
        }

        return true;
    } else {
        return false;
    }
}

if (rtrim($_SERVER['REQUEST_URI'], '/') != site_path() . '/login-mfa') {
    if (isset($_SESSION['mfa_pwd']) && isset($_SESSION['mfa_uid'])) {
        unset($_SESSION['mfa_pwd']);
        unset($_SESSION['mfa_uid']);
        unset($_SESSION['mfa_remember']);
    }
}
