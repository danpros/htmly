<?php
if (!defined('HTMLY')) die('HTMLy Direct Access Denied');

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/posts.php';
require_once __DIR__ . '/pages.php';
require_once __DIR__ . '/taxonomy.php';
require_once __DIR__ . '/media.php';
require_once __DIR__ . '/system.php';

/**
 * Main API v1 Request Entry Point
 */
function handle_api_v1_request($path, $method)
{
    // Validate Bearer Token
    $auth = validate_api_key();
    if (!$auth) {
        api_error('Unauthorized: Invalid or missing API Bearer Token', 401, 'UNAUTHORIZED');
    }

    $subPath = trim(substr($path, strlen('/api/v1')), '/');
    $parts = explode('/', $subPath);
    $resource = $parts[0] ?? '';
    $identifier = $parts[1] ?? null;

    // Route: /api/v1/posts
    if ($resource === 'posts') {
        if ($method === 'GET') {
            api_get_posts();
        } elseif ($method === 'POST') {
            api_create_post($auth);
        } elseif ($method === 'DELETE' && $identifier) {
            api_delete_post($identifier);
        } else {
            api_error('Method Not Allowed', 405, 'METHOD_NOT_ALLOWED');
        }
    }

    // Route: /api/v1/pages
    if ($resource === 'pages') {
        if ($method === 'GET') {
            api_get_pages();
        } elseif ($method === 'POST') {
            api_create_page($auth);
        } elseif ($method === 'DELETE' && $identifier) {
            api_delete_page($identifier);
        } else {
            api_error('Method Not Allowed', 405, 'METHOD_NOT_ALLOWED');
        }
    }

    // Route: /api/v1/categories
    if ($resource === 'categories') {
        if ($method === 'GET') {
            api_get_categories();
        } elseif ($method === 'POST') {
            api_create_category();
        } else {
            api_error('Method Not Allowed', 405, 'METHOD_NOT_ALLOWED');
        }
    }

    // Route: /api/v1/tags
    if ($resource === 'tags') {
        if ($method === 'GET') {
            api_get_tags();
        } else {
            api_error('Method Not Allowed', 405, 'METHOD_NOT_ALLOWED');
        }
    }

    // Route: /api/v1/media/upload
    if ($resource === 'media' && $identifier === 'upload') {
        if ($method === 'POST') {
            api_upload_media();
        } else {
            api_error('Method Not Allowed', 405, 'METHOD_NOT_ALLOWED');
        }
    }

    // Route: /api/v1/system/health
    if ($resource === 'system' && $identifier === 'health') {
        if ($method === 'GET') {
            api_get_system_health();
        } else {
            api_error('Method Not Allowed', 405, 'METHOD_NOT_ALLOWED');
        }
    }

    // Default 404 for unknown API endpoints
    api_error('API Endpoint Not Found', 404, 'NOT_FOUND');
}
