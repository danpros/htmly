<?php
if (!defined('HTMLY')) die('HTMLy Direct Access Denied');

/**
 * Validate Bearer Token API Key against config/api_keys.ini
 * 
 * @return array|null Returns array ['user' => string, 'role' => string] on success, null on failure.
 */
function validate_api_key()
{
    $headers = getallheaders();
    $authHeader = null;

    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
    } elseif (isset($headers['authorization'])) {
        $authHeader = $headers['authorization'];
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (!$authHeader || strpos($authHeader, 'Bearer ') !== 0) {
        return null;
    }

    $token = trim(substr($authHeader, 7));
    if (empty($token)) {
        return null;
    }

    $keyFile = 'config/api_keys.ini';
    if (!file_exists($keyFile)) {
        return null;
    }

    $keys = parse_ini_file($keyFile, true);
    $apiKeys = $keys['api_keys'] ?? array();

    foreach ($apiKeys as $validKey => $role) {
        if (hash_equals((string)$validKey, (string)$token)) {
            return array(
                'user' => 'api_user',
                'role' => $role
            );
        }
    }

    return null;
}

/**
 * Helper to render JSON response & exit script execution
 */
function api_response($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Helper to render JSON error response
 */
function api_error($message, $statusCode = 400, $code = 'BAD_REQUEST')
{
    api_response(array(
        'success' => false,
        'error' => array(
            'code' => $code,
            'message' => $message
        )
    ), $statusCode);
}
