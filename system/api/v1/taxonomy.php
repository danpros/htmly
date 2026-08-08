<?php
if (!defined('HTMLY')) die('HTMLy Direct Access Denied');

/**
 * Handle GET /api/v1/categories
 */
function api_get_categories()
{
    $categories = get_category_list();
    $formatted = array();

    if (!empty($categories)) {
        foreach ($categories as $cat) {
            $formatted[] = array(
                'name' => $cat->title ?? '',
                'slug' => $cat->slug ?? '',
                'url' => $cat->url ?? '',
                'count' => $cat->count ?? 0
            );
        }
    }

    api_response(array(
        'success' => true,
        'data' => $formatted,
        'total' => count($formatted)
    ));
}

/**
 * Handle POST /api/v1/categories
 */
function api_create_category()
{
    $inputRaw = file_get_contents('php://input');
    $body = json_decode($inputRaw, true) ?: $_POST;

    $title = $body['title'] ?? null;
    $description = $body['description'] ?? '';

    if (empty($title)) {
        api_error('Category title is required', 400, 'MISSING_TITLE');
    }

    $slug = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), remove_accent($title)));
    $dir = 'content/data/category/';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $file = $dir . $slug . '.md';
    $content = "<!--t " . safe_html($title) . " t-->\n<!--d " . safe_html($description) . " d-->\n\n" . $description;
    file_put_contents($file, $content, LOCK_EX);

    api_response(array(
        'success' => true,
        'message' => 'Category successfully created',
        'data' => array(
            'title' => $title,
            'slug' => $slug
        )
    ), 201);
}

/**
 * Handle GET /api/v1/tags
 */
function api_get_tags()
{
    $tags = tag_cloud(true);
    $formatted = array();

    if (!empty($tags)) {
        foreach ($tags as $slug => $data) {
            $formatted[] = array(
                'name' => is_array($data) ? ($data['name'] ?? $slug) : $slug,
                'slug' => $slug,
                'count' => is_array($data) ? ($data['count'] ?? 1) : 1
            );
        }
    }

    api_response(array(
        'success' => true,
        'data' => $formatted,
        'total' => count($formatted)
    ));
}
