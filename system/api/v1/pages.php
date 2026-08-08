<?php
if (!defined('HTMLY')) die('HTMLy Direct Access Denied');

/**
 * Handle GET /api/v1/pages
 */
function api_get_pages()
{
    $pages = get_static_pages();
    if (empty($pages)) {
        api_response(array(
            'success' => true,
            'data' => array(),
            'total' => 0
        ));
    }

    $formatted = array();
    foreach ($pages as $p) {
        $formatted[] = array(
            'id' => pathinfo($p['filename'] ?? '', PATHINFO_FILENAME),
            'title' => $p['title'] ?? '',
            'slug' => $p['slug'] ?? '',
            'url' => site_url() . ($p['slug'] ?? ''),
            'description' => $p['description'] ?? '',
            'content' => $p['body'] ?? ''
        );
    }

    api_response(array(
        'success' => true,
        'data' => $formatted,
        'total' => count($formatted)
    ));
}

/**
 * Handle POST /api/v1/pages
 */
function api_create_page($auth)
{
    $inputRaw = file_get_contents('php://input');
    $body = json_decode($inputRaw, true) ?: $_POST;

    $title = $body['title'] ?? null;
    $content = $body['content'] ?? null;
    $url = $body['slug'] ?? ($title ? remove_accent($title) : null);
    $description = $body['description'] ?? null;

    if (empty($title) || empty($content)) {
        api_error('Title and content fields are required', 400, 'MISSING_FIELDS');
    }

    $url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($url)));
    $dir = 'content/pages/';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $filename = $dir . $url . '.md';
    $post_title = safe_html($title);
    $post_description = $description ? "\n<!--d " . safe_html($description) . " d-->" : "";
    $post_content = "<!--t " . $post_title . " t-->" . $post_description . "\n\n" . $content;

    file_put_contents($filename, print_r($post_content, true), LOCK_EX);
    rebuilt_cache('all');

    api_response(array(
        'success' => true,
        'message' => 'Page successfully created',
        'data' => array(
            'title' => $title,
            'slug' => $url,
            'url' => site_url() . $url
        )
    ), 201);
}

/**
 * Handle DELETE /api/v1/pages/{slug}
 */
function api_delete_page($slug)
{
    if (empty($slug)) {
        api_error('Slug is required', 400, 'MISSING_SLUG');
    }

    // Sanitize slug to prevent path traversal attacks (CWE-22)
    $cleanSlug = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $slug));
    if (empty($cleanSlug)) {
        api_error('Invalid slug parameter', 400, 'INVALID_SLUG');
    }

    $file = 'content/pages/' . $cleanSlug . '.md';
    if (!file_exists($file)) {
        api_error('Page not found', 404, 'NOT_FOUND');
    }

    delete_page($file, 'admin');

    api_response(array(
        'success' => true,
        'message' => "Page '{$slug}' successfully deleted"
    ));
}
