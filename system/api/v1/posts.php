<?php
if (!defined('HTMLY')) die('HTMLy Direct Access Denied');

/**
 * Handle GET /api/v1/posts
 * Fetch published posts or drafts with pagination
 */
function api_get_posts()
{
    $status = $_GET['status'] ?? 'published';
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 10;
    
    if ($status === 'draft') {
        $posts = get_draft_posts();
    } else {
        $posts = get_all_posts();
    }

    if (empty($posts)) {
        api_response(array(
            'success' => true,
            'data' => array(),
            'pagination' => array(
                'current_page' => $page,
                'total_pages' => 0,
                'total_items' => 0
            )
        ));
    }

    $totalItems = count($posts);
    $totalPages = ceil($totalItems / $limit);
    $offset = ($page - 1) * $limit;
    $pagedPosts = array_slice($posts, $offset, $limit);

    $formattedData = array();
    foreach ($pagedPosts as $p) {
        $url = site_url() . $p['category'] . '/' . $p['slug'];
        $formattedData[] = array(
            'id' => pathinfo($p['filename'], PATHINFO_FILENAME),
            'title' => $p['title'] ?? '',
            'slug' => $p['slug'] ?? '',
            'url' => $url,
            'status' => $status,
            'date' => date('Y-m-d H:i:s', $p['date'] ?? time()),
            'category' => $p['category'] ?? '',
            'tags' => explode(',', $p['tag'] ?? ''),
            'description' => $p['description'] ?? '',
            'content' => $p['body'] ?? ''
        );
    }

    api_response(array(
        'success' => true,
        'data' => $formattedData,
        'pagination' => array(
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'per_page' => $limit
        )
    ));
}

/**
 * Handle POST /api/v1/posts
 * Create new Post or Draft
 */
function api_create_post($auth)
{
    $inputRaw = file_get_contents('php://input');
    $body = json_decode($inputRaw, true);

    if (!$body) {
        $body = $_POST;
    }

    $title = $body['title'] ?? null;
    $content = $body['content'] ?? null;
    $category = $body['category'] ?? 'uncategorized';
    $tags = $body['tags'] ?? '';
    $status = $body['status'] ?? 'published';
    $description = $body['description'] ?? null;

    if (empty($title) || empty($content)) {
        api_error('Title and content fields are required', 400, 'MISSING_FIELDS');
    }

    $user = $auth['user'] ?? 'admin';
    $url = remove_accent($title);
    $type = 'post';
    $draft = ($status === 'draft') ? 'draft' : null;
    $dateTime = date('Y-m-d-H-i-s');

    // Call internal HTMLy helper
    add_content($title, $tags, $url, $content, $user, $draft, $category, $type, $description, null, $dateTime);

    api_response(array(
        'success' => true,
        'message' => 'Content successfully created',
        'data' => array(
            'title' => $title,
            'slug' => strtolower($url),
            'status' => $status,
            'category' => $category
        )
    ), 201);
}

/**
 * Handle DELETE /api/v1/posts/{slug}
 */
function api_delete_post($slug)
{
    if (empty($slug)) {
        api_error('Slug is required', 400, 'MISSING_SLUG');
    }

    // Sanitize slug to prevent path manipulation
    $cleanSlug = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $slug));
    if (empty($cleanSlug)) {
        api_error('Invalid slug parameter', 400, 'INVALID_SLUG');
    }

    $posts = get_all_posts();
    $targetFile = null;

    foreach ($posts as $p) {
        if (($p['slug'] ?? '') === $cleanSlug) {
            $targetFile = $p['file'] ?? null;
            break;
        }
    }

    if (!$targetFile) {
        api_error('Post not found', 404, 'NOT_FOUND');
    }

    // Pass false as $is_draft since we are deleting a published post by slug
    delete_post($targetFile, 'admin/mine', false);

    api_response(array(
        'success' => true,
        'message' => "Post '{$slug}' successfully deleted"
    ));
}
